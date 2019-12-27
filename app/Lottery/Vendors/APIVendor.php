<?php


namespace App\Lottery\Vendors;

use App\Lottery\Games\Lottery;

/**
 * Class APIVendor: 第三方 API 廠商 (號源)
 * @package App\Lottery\Vendors
 */
abstract class APIVendor
{
    /** @var int 號源ID */
    protected $vendorId;
    /** @var string 號源名稱 */
    protected $name;
    /** @var string 號源url */
    protected $url;

    /**
     * constructor
     * @param array $data 一筆在資料庫中的號源紀錄
     */
    public function __construct(array $data)
    {
        $this->vendorId = $data['id'];
        $this->name = $data['name'];
        $this->url = $data['url'];
    }

    /**
     * 用來取得特定彩種的開獎號碼
     * @param Lottery $lottery 指定彩種
     * @return string
     */
    abstract function getWinningNumber(Lottery $lottery): string;
}
