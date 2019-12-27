<?php

namespace App\Lottery;

use App\Lottery\Games\Lottery;
use App\Lottery\Games\GameHandler;
use App\Repositories\VendorRepository;
use App\Repositories\GameVendorMappingRepository;
use Illuminate\Support\Collection;

/**
 * Class GameService: 需實現的 class.
 * @package App\Lottery
 */
class GameService
{
    /** @var string  */
    private const VENDORS_PATH = 'App\\Lottery\\Vendors\\';

    /** @var Collection  號源列表 */
    private $vendorList;

    /** @var Collection  GameHandler列表 */
    private $mapping;

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

        // 載入所有的第三方 API 廠商 (號源)
        $this->vendorList = $this->transformVendorList();

        $this->mapping = collect();
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
     * @return Collection
     */
    private function transformVendorList(): Collection
    {
        return $this->vendorRepo->getList()
            ->keyBy('vendor_id')
            ->map(function ($vendorInfo) {
                $class = self::VENDORS_PATH. $vendorInfo['name'];
                return new $class($vendorInfo);
            });
    }

    /**
     * get the collection by the given key: vendor_id
     * @param Collection $lotteryVendorMapping 彩種與號源之間的對映列表
     * @return array
     */
    private function transformMappingList(Collection $lotteryVendorMapping): array
    {
        return $lotteryVendorMapping->keyBy('vendor_id')
            ->map(function ($item) {
                $vendorId = $item['vendor_id'];
                return [
                    'game_id' => $item['game_id'],
                    'major' => $item['major'],
                    'vendor' => $this->vendorList[$vendorId]
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
        if (empty($this->mapping[$gameId])) {
          $lotteryVendorMapping = $this->gameVendorMappingRepo->getListByGameId($gameId);
          $mappingList = $this->transformMappingList($lotteryVendorMapping);
          $this->mapping[$gameId] = new GameHandler($mappingList);
        }

        return $this->mapping[$gameId]->getWinningNumber($lottery);
    }
}
