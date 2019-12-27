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

        if (!$memory->get('mapping')) {
            echo '初始化 GameHandler 列表' . PHP_EOL;
            $memory->put('mapping', []);
        }

        echo 'GameService Constructed' . PHP_EOL;
    }

    /**
     * 取得 GameService 實例
     * @param VendorRepository $vendorRepo
     * @param GameVendorMappingRepository $gameVendorMappingRepo
     * @return GameService
     */
    public static function instance(VendorRepository $vendorRepo, GameVendorMappingRepository $gameVendorMappingRepo): GameService
    {
        if (empty(self::$instance)) {
            self::$instance = new GameService($vendorRepo, $gameVendorMappingRepo);
        }

        return self::$instance;
    }

    /**
     * 取得並轉換號源列表
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
     * get the collection by the given key: vendor_id
     * @param Collection $lotteryVendorMapping 彩種與號源之間的對映列表
     * @return array
     */
    private function transformMappingList(Collection $lotteryVendorMapping): array
    {
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
     * 用來取得特定彩種的開獎號碼
     * @param Lottery $lottery 指定彩種
     * @return string
     */
    public function getWinningNumber(Lottery $lottery): string
    {
        $gameId = $lottery->getGameId();
        $memKey = 'game.handler.' . $gameId;
        $gameHandler = Memory::make('cache')->get($memKey);

        if (empty($gameHandler)) {
            echo '取得彩種為 ' . $gameId . ' 的實例' . PHP_EOL;
            $lotteryVendorMapping = $this->gameVendorMappingRepo->getListByGameId($gameId);
            $mappingList = $this->transformMappingList($lotteryVendorMapping);
            $gameHandler = new GameHandler($mappingList);
            Memory::make('cache')->put($memKey, $gameHandler);
        }

        return $gameHandler->getWinningNumber($lottery);
    }
}
