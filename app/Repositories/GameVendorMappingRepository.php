<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Trait GameVendorMappingRepository
 * @package App\Repositories
 */
trait GameVendorMappingRepository
{
    /**
     * 讀取 '彩種與號源之間的對映列表'
     * @param int $gameId
     * @return Collection
     */
    public function getMappingListByGameId(int $gameId): Collection
    {
        echo 'GameVendorMappingRepository -> getListByGameId (從資料庫拿)' . PHP_EOL . PHP_EOL;
        return DB::table('game_vendor_mappings')
            ->where('game_id', '=', $gameId)
            ->get();
    }
}
