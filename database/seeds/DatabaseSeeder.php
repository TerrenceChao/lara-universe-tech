<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
             GamesTableSeeder::class,
             VendorsTableSeeder::class,
             GameVendorMappingTableSeeder::class
         ]);
    }
}
