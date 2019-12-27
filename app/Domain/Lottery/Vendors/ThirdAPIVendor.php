<?php


namespace App\Domain\Lottery\Vendors;

use App\Domain\Lottery\Games\Lottery;

/**
 * Class ThirdAPIVendor
 * @package App\Domain\Lottery\Vendors
 */
class ThirdAPIVendor extends APIVendor
{
    /** @var array 針對不同彩種定義所需的參數 */
    private $lotteries;

    /**
     * constructor
     * @param array $data 一筆在資料庫中的號源紀錄
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        // TODO: 定義不同彩種所需的參數
        // $this->lotteries = [];
    }

    /**
     * @inheritDoc
     */
    function getWinningNumber(Lottery $lottery): string
    {
        $gameId = $lottery->getGameId();
        // TODO: do request with specific 'gameId' and/or 'issue' ...

        return '0,6,2,2,3';
    }
}
