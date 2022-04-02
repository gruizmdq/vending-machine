<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        Item::truncate();

        $soda = new Item();
        $soda->code = "SODA";
        $soda->qty = 10;
        $soda->price = 1.50;
        $soda->save();

        $juice = new Item();
        $juice->code = "JUICE";
        $juice->qty = 10;
        $juice->price = 1.0;
        $juice->save();

        $water = new Item();
        $water->code = "WATER";
        $water->qty = 10;
        $water->price = 0.65;
        $water->save();
    }
}
