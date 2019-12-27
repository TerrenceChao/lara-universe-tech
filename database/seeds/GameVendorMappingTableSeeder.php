<?php

use Illuminate\Database\Seeder;

class GameVendorMappingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendors = DB::table('vendors')->get();
        $games = DB::table('games')->get();

        $vendorCount = $vendors->count();

        // 設定各個 game 的主號源/副號源 對映
        $games->each(function ($game) use ($vendors, $vendorCount) {
            $key = ($game->id % $vendorCount) + 1;
            $majorVendor = collect($vendors)->first(function ($vendor) use ($key) {
                return $vendor->id == $key;
            });

            // 設定 game 的主號源
            DB::table('game_vendor_mappings')->insert([
                'game_id' => $game->id,
                'vendor_id' => $majorVendor->id,
                'major' => true
            ]);

            $minorVendors = $vendors->filter(function ($vendor) use ($key) {
                return $vendor->id != $key;
            });

            // 設定 game 的副號源
            $minorVendors->each(function ($minorVendor) use ($game) {
                DB::table('game_vendor_mappings')->insert([
                    'game_id' => $game->id,
                    'vendor_id' => $minorVendor->id,
                    'major' => false
                ]);
            });
        });
    }
}
