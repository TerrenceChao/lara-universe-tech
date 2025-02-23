<?php


namespace App\Domain\Lottery\Vendors;

use App\Domain\Lottery\Games\Lottery;

/**
 * Class FirstAPIVendor
 * @package App\Domain\Lottery\Vendors
 */
class FirstAPIVendor extends APIVendor
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
        // 定義不同彩種所需的參數
        $this->lotteries = [
            // 重慶時時彩
            1 => [
                'gamekey' => 'ssc',
            ],
            // 北京11選5
            2 => [
                'gamekey' => 'bjsyxw',
            ],
            3 => [
                'gamekey' => 'bjsyxw_B',
            ],
            4 => [
                'gamekey' => 'bjsyxw_C',
            ],
            5 => [
                'gamekey' => 'bjsyxw_D',
            ],
            6 => [
                'gamekey' => 'bjsyxw_E',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getWinningNumber(Lottery $lottery): string
    {
        $gameId = $lottery->getGameId();
        $reqUrl = $this->url . '?gamekey=' . $this->lotteries[$gameId]['gamekey'] . '&issue=' . $lottery->getValue('issue');
        // TODO: do request with specific 'gamekey' & 'issue'
        echo '號源 ' . $this->vendorId . ': request -> ' . $reqUrl . PHP_EOL;

        return '0,6,2,2,3';
    }
}
