<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class VendorRepository
 * @package App\Repositories
 */
class VendorRepository
{
    /**
     * VendorRepository constructor.
     */
    public function __construct()
    {
        echo 'VendorRepository Constructed' . PHP_EOL;
    }

    /**
     * 讀取號源列表
     * @return Collection
     */
    public function getList(): Collection
    {
        return new Collection([
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
        ]);
    }
}
