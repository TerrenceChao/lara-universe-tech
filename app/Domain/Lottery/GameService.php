<?php

namespace App\Domain\Lottery;

use App\Domain\Lottery\Games\Lottery;
use App\Domain\Lottery\Games\GameHandler;
use App\Repositories\VendorRepository;
use App\Repositories\GameVendorMappingRepository;
use Orchestra\Support\Facades\Memory;


/**
 * Class GameService: 需實現的 class.
 * @package App\Domain\Lottery
 */
class GameService
{
    use VendorRepository;
    use GameVendorMappingRepository;

    /** @var string  */
    private const VENDORS_PATH = 'App\\Domain\\Lottery\\Vendors\\';

    /** GameService */
    private static $instance;

    /**
     * GameService constructor.
     */
    private function __construct()
    {
        $memory = Memory::make('cache');

        // 載入所有的第三方 API 廠商 (號源)
        if (!$memory->get('vendorList')) {
            echo '載入所有的第三方 API 廠商 (號源)' . PHP_EOL;
            $memory->put('vendorList', $this->transformVendorList());
        }
    }

    /**
     * 取得 GameService 實例
     */
    public static function instance(): GameService
    {
        if (empty(self::$instance)) {
            echo 'GameService Constructed' . PHP_EOL;
            self::$instance = new GameService();
        }

        return self::$instance;
    }

    /**
     * 取得並轉換 號源列表
     * @return array
     */
    private function transformVendorList(): array
    {
        return $this->getVendorList()
            ->keyBy('id')
            ->map(function ($vendorInfo) {
                $class = self::VENDORS_PATH. $vendorInfo->name;
                return new $class((array) $vendorInfo);
            })
            ->all();
    }

    /**
     * 取得並轉換 彩種與號源之間的對映列表
     * @param int $gameId 彩種編號
     * @return array
     */
    private function transformMappingList(int $gameId): array
    {
        $gameVendorMapping = $this->getMappingListByGameId($gameId);
        $vendorList = Memory::make('cache')->get('vendorList');

        return $gameVendorMapping->keyBy('vendor_id')
            ->map(function ($item) use ($vendorList) {
                $vendorId = $item->vendor_id;
                return [
                    'game_id' => $item->game_id,
                    'major' => $item->major,
                    'vendor' => $vendorList[$vendorId]
                ];
            })
            ->all();
    }

    /**
     * 取得特定彩種的開獎號碼
     * @param Lottery $lottery 彩卷
     * @return string
     */
    public function getWinningNumber(Lottery $lottery): string
    {
        $gameId = $lottery->getGameId();
        $memKey = 'game.handler.' . $gameId;
        $gameHandler = Memory::make('cache')->get($memKey);

        if (empty($gameHandler)) {
            echo '新增彩種為 ' . $gameId . ' 的實例' . PHP_EOL;
            $mappingList = $this->transformMappingList($gameId);
            $gameHandler = new GameHandler($mappingList);
            Memory::make('cache')->put($memKey, $gameHandler);
        }

        return $gameHandler->getWinningNumber($lottery);
    }
}
