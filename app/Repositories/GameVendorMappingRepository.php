<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

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
        // database records
        $dbRows = [
            // 重慶時時彩
            [
                'game_id' => 1,
                'vendor_id' => 18,
                'major' => true,
            ],
            // 重慶時時彩
            [
                'game_id' => 1,
                'vendor_id' => 22,
                'major' => false,
            ],
            // 北京11選5
            [
                'game_id' => 2,
                'vendor_id' => 22,
                'major' => true,
            ],
            // 北京11選5
            [
                'game_id' => 2,
                'vendor_id' => 18,
                'major' => false,
            ],
        ];

        // 2. filter target gameId
        $list = [];
        foreach ($dbRows as $row) {
            if ($gameId !== $row['game_id']) {
                continue;
            }

            $list[] = $row;
        }

        return new Collection($list);
    }
}
