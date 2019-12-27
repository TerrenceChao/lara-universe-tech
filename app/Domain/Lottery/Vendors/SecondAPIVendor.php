<?php


namespace App\Domain\Lottery\Vendors;

use App\Domain\Lottery\Games\Lottery;

/**
 * Class SecondAPIVendor
 * @package App\Domain\Lottery\Vendors
 */
class SecondAPIVendor extends APIVendor
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
                'code' => 'cqssc',
            ],
            // 北京11選5
            2 => [
                'code' => 'bj11x5',
            ],
            3 => [
                'code' => 'bj11x5_B',
            ],
            4 => [
                'code' => 'bj11x5_C',
            ],
            5 => [
                'code' => 'bj11x5_D',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getWinningNumber(Lottery $lottery): string
    {
        $gameId = $lottery->getGameId();
        $reqUrl = $this->url . '?code=' . $this->lotteries[$gameId]['code'];
        // TODO: do request with specific 'code'
        echo '號源 ' . $this->vendorId . ': request -> ' . $reqUrl . PHP_EOL;

        $response = [
            'data' => [
                [
                    'expect' => '20190903001',
                    'opencode' => '0,6,2,2,3',
                    'opentime' => '2019-09-02 01:12:46'
                ],
                [
                    'expect' => '20190902002',
                    'opencode' => '3,1,5,8,6',
                    'opentime' => '2019-09-02 00:52:37'
                ],
                [
                    'expect' => '20190902001',
                    'opencode' => '6,1,9,0,3',
                    'opentime' => '2019-09-02 00:32:03'
                ]
            ]
        ];

        $issue = $lottery->getValue('issue');
        foreach ($response['data'] as $num) {
            if ($num['expect'] === $issue) {
                return $num['opencode'];
            }
        }

        throw new Exception('找不到開獎期號為: ' . $issue . ' 的開獎號碼');
    }
}
