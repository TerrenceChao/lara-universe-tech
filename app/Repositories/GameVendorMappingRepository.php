<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class GameVendorMappingRepository
 * @package App\Repositories
 */
class GameVendorMappingRepository
{
    /**
     * GameVendorMappingRepository constructor.
     */
    public function __construct()
    {
        echo 'GameVendorMappingRepository Constructed' . PHP_EOL;
    }

    /**
     * 讀取 '彩種與號源之間的對映列表'
     * @param int $gameId
     * @return Collection
     */
    public function getListByGameId(int $gameId): Collection
    {
        return DB::table('game_vendor_mappings')
            ->where('game_id', '=', $gameId)
            ->get();
    }
}
