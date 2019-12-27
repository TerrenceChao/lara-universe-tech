<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        return DB::table('vendors')->get();
    }
}
