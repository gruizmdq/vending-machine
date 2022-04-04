<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Coin;
use Exception;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{   
    /**
     * {
     *  "items": [
     *  	{
     *          "code": "soda"
     *          "qty": 5,
     *          "price" ? : 1.5
     *      }
     *    ]
     *  "coins": [
     *  	{
     *          "value": 0.1
     *          "qty": 5
     *      }
     *    ]
     * }
     */

    public function newService(Request $request) {
        $items = $request->input("items", null);
        $coins = $request->input("coins", null);

        try {

            foreach ($items as $item) {
                self::updateOrInsertItem($item);
            }
            
            self::updateCoins($coins);

        }
        catch(Exception $e) {
            return $e;
        }
        

    }

    private function updateOrInsertItem(array $item) {
        $newItem = ["qty" => $item["qty"]];

        if ($item["price"])
            $newItem["price"] = $item["price"];

        Item::updateOrCreate(
            ["code" => strtoupper($item["code"])],
            $newItem);
    }

    private function updateCoins(array $coins) {
        Coin::truncate();
        foreach ($coins as $coin) {
            if (Coin::getValueValidator($coin)->fails()) {
                throw new Exception("There was an error with coins.", 400);
            }
            else {
                for ($i=0; $i < $coin["qty"]; $i++) { 
                    Coin::create([
                        "value" => $coin["value"],
                        "isForReturn" => false,
                    ]);
                }
            }
        }

    }
}
