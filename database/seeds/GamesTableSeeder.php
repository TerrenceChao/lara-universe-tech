<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class GamesTableSeeder
 */
class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = 5;
        $idx = 0;
        do {
            DB::table('games')->insert([
                'name' => Str::random(10)
            ]);
        } while(++$idx <= $count);
    }
}
