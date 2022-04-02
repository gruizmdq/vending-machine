<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coin;

class CoinSeeder extends Seeder
{
    final const VALUES = [0.05, 0.1, 0.25, 1.0];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        Coin::truncate();
        for($i = 0; $i < 1000; $i++) {
            $coin = new Coin();
            $coin->value = self::VALUES[random_int(0, sizeof(self::VALUES)-1)];
            $coin->save();
        }
        
    }
}
