<?php

namespace App\Lottery;

use App\Lottery\Games\Lottery;
use App\Lottery\Games\GameHandler;
use App\Lottery\Vendors\APIVendor;
use App\Repositories\VendorRepository;
use App\Repositories\GameVendorMappingRepository;

/**
 * Class GameService: 需實現的 class.
 * @package App\Lottery
 */
class GameService
{
    /** @var string  */
    private const VENDORS_PATH = 'App\\Lottery\\Vendors\\';

    /** @var array  號源列表 */
    private $vendorList;

    /** @var array  GameHandler列表 */
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

        $this->vendorList = [];
        $this->mapping = [];

        // 載入所有的第三方 API 廠商 (號源)
        foreach ($this->getAPIVendorList() as $vendorId => $vendorInfo) {
            $this->vendorList[$vendorId] = $this->createVendor($vendorInfo);
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
     * @param array $vendorInfo
     * @return APIVendor
     */
    private function createVendor(array $vendorInfo): APIVendor
    {
        $class = self::VENDORS_PATH. $vendorInfo['name'];
        return new $class($vendorInfo);
    }

    /**
     * '模擬'從資料庫中讀取號源列表
     * @return array
     */
    private function getAPIVendorListFromDB(): array
    {
         echo 'getAPIVendorListFromDB (every time)' . PHP_EOL;
         return [
           [
             'vendor_id' => 18,
             'name' => 'FirstAPIVendor',
             'url' => 'http://one.faker/v1'
           ],
           [
             'vendor_id' => 22,
             'name' => 'SecondAPIVendor',
             'url' => 'https://two.fake/newly.do'
           ],
           [
             'vendor_id' => 66,
             'name' => 'ThirdAPIVendor',
             'url' => 'https://three.fake/just.bet'
           ]
         ];
    }

    /**
     * 取得並轉換號源列表
     * @return array
     */
    private function getAPIVendorList(): array
    {
        // 1. read vendor list from DB
        $dbRows = $this->getAPIVendorListFromDB();

        // 2. use collect([...])->keyBy('vendor_id')
        $vendorList = [];
        foreach ($dbRows as $row) {
          $vendorList[$row['vendor_id']] = $row;
        }

        return $vendorList;
    }

    /**
     * '模擬'從資料庫中讀取 '彩種與號源之間的對映列表'
     * @param int $gameId 彩種編號
     * @return array
     */
    private function getLotteryVendorMappingFromDB(int $gameId): array
    {
         // database records
         $dbRows = [
           // 重慶時時彩
           [
             'game_id' => 1,
             'game_name' => 'chongqing_anytime',
             'vendor_id' => 18,
             'major' => true,
           ],
           // 重慶時時彩
           [
             'game_id' => 1,
             'game_name' => 'chongqing_anytime',
             'vendor_id' => 22,
             'major' => false,
           ],
           // 北京11選5
           [
             'game_id' => 2,
             'game_name' => 'beijing_11x5',
             'vendor_id' => 22,
             'major' => true,
           ],
           // 北京11選5
           [
             'game_id' => 2,
             'game_name' => 'beijing_11x5',
             'vendor_id' => 18,
             'major' => false,
           ],
         ];

         // 2. filter target gameId
         $list = [];
         foreach ($dbRows as $row) {
           if ($gameId !== $row['game_id']) {
             continue;
           }

           $list[] = $row;
         }

         return $list;
    }

    /**
     * get the collection by the given key: vendor_id
     * @param array $lotteryVendorMapping 彩種與號源之間的對映列表
     * @return array
     */
    private function keyByVendorId(array $lotteryVendorMapping): array
    {
        $list = [];
        foreach ($lotteryVendorMapping as $vendorInfo) {
          $vendorId = $vendorInfo['vendor_id'];
          $list[$vendorId] = [
            'game_id' => $vendorInfo['game_id'],
            'game_name' => $vendorInfo['game_name'],
            'major' => $vendorInfo['major'],
            'vendor' => $this->vendorList[$vendorId]
          ];
        }

        return $list;
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
          $lotteryVendorMapping = $this->getLotteryVendorMappingFromDB($gameId);
          $vendorList = $this->keyByVendorId($lotteryVendorMapping);
          $this->mapping[$gameId] = new GameHandler($vendorList);
        }

        return $this->mapping[$gameId]->getWinningNumber($lottery);
    }
}
