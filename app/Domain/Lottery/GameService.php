<?php

namespace App\Domain\Lottery;

use App\Domain\Lottery\Games\Lottery;
use App\Domain\Lottery\Games\GameHandler;
use App\Repositories\VendorRepository;
use App\Repositories\GameVendorMappingRepository;
use Illuminate\Support\Collection;
use Orchestra\Support\Facades\Memory;


/**
 * Class GameService: 需實現的 class.
 * @package App\Domain\Lottery
 */
class GameService
{
    /** @var string  */
    private const VENDORS_PATH = 'App\\Domain\\Lottery\\Vendors\\';

    /** @var GameService */
    private static $instance;

    /** @var VendorRepository */
    private $vendorRepo;

    /** @var GameVendorMappingRepository */
    private $gameVendorMappingRepo;

    /**
     * GameService constructor.
     * @param VendorRepository $vendorRepo
     * @param GameVendorMappingRepository $gameVendorMappingRepo
     */
    public function __construct(VendorRepository $vendorRepo, GameVendorMappingRepository $gameVendorMappingRepo)
    {
        $this->vendorRepo = $vendorRepo;
        $this->gameVendorMappingRepo = $gameVendorMappingRepo;

        $memory = Memory::make('cache');

        // 載入所有的第三方 API 廠商 (號源)
        if (!$memory->get('vendorList')) {
            echo '載入所有的第三方 API 廠商 (號源)' . PHP_EOL;
            $memory->put('vendorList', $this->transformVendorList());
        }

        echo 'GameService Constructed' . PHP_EOL;
    }

    /**
     * 取得並轉換 號源列表
     * @return array
     */
    private function transformVendorList(): array
    {
        return $this->vendorRepo->getList()
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
        $lotteryVendorMapping = $this->gameVendorMappingRepo->getListByGameId($gameId);
        $vendorList = Memory::make('cache')->get('vendorList');

        return $lotteryVendorMapping->keyBy('vendor_id')
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
