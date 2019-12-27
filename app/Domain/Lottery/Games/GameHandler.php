<?php

namespace App\Domain\Lottery\Games;

/**
 * Class GameHandler: 彩種
 * @package App\Domain\Lottery\Games
 */
class GameHandler
{
    /** @var APIVendor: 主號源 */
    private $majorVendor;

    /** @var array: 副號源列表 */
    private $minorVendorList;

    /**
     * constructor
     * @param array $vendorList 號源列表
     */
    public function __construct(array $vendorList)
    {
        $this->parseVendorList($vendorList);
    }

    /**
     * 解析主號源和其他副號源
     * @param array $vendorList 號源列表
     */
    private function parseVendorList(array $vendorList): void
    {
        foreach ($vendorList as $vendorId => $item) {
            if ($item['major'] === true) {
                $this->majorVendor = $item['vendor'];
                continue;
            }

            $this->minorVendorList[$vendorId] = $item['vendor'];
        }
    }

    /**
     * 用來取得特定彩種的開獎號碼
     * @param Lottery $lottery 指定彩種
     * @return string
     */
    public function getWinningNumber(Lottery $lottery): string
    {
//        var_dump(['major' => $this->majorVendor]);
//        var_dump(['minor' => $this->minorVendorList]);

        try {
            $majorWinNum = $this->majorVendor->getWinningNumber($lottery);
            foreach ($this->minorVendorList as $minorVendor) {
                if ($majorWinNum === $minorVendor->getWinningNumber($lottery)) {
                    return $majorWinNum;
                }
            }

            throw new Exception('無法與其他來源比對，二次確認開獎號碼');

        } catch (Exception $e) {
            echo 'class GameHandler->getWinningNumber: ' . $e->getMessage();
            throw $e;
        }
    }
}
