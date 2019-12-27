<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Trait VendorRepository
 * @package App\Repositories
 */
trait VendorRepository
{
    /**
     * 讀取號源列表
     * @return Collection
     */
    public function getVendorList(): Collection
    {
        echo 'VendorRepository -> getList (從資料庫拿)' . PHP_EOL . PHP_EOL;
        return DB::table('vendors')->get();
    }
}
