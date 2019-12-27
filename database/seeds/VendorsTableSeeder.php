<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class VendorsTableSeeder
 */
class VendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendorNames = [
            'FirstAPIVendor',
            'SecondAPIVendor',
            'ThirdAPIVendor',
        ];
        $count = count($vendorNames);

        $idx = 0;
        do {
            DB::table('vendors')->insert([
                'name' => $vendorNames[$idx],
                'url' => 'http://' . Str::random(10) . '/' . Str::random(5)
            ]);
        } while(++$idx < $count);
    }
}
