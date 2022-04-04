<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class CoinController extends Controller
{
    public function list(Request $request) {
        return Coin::select(DB::raw('value, count(*) as qty'))
        ->groupBy('value')
        ->orderBy('value')
        ->get();
    }

    public function insert(Request $request) {
        if (self::getValueValidator($request->all())->fails()) {
            return response("Coin not accepted, coin was returned.", 400);
        }
        try {
            Coin::create([
                "value" => $request->input("value"),
                "isForReturn" => true,
            ]);
        }
        catch (Exception $e) {
            return response("There was an error.", 500);
        }
    }

    public function returnCoins(Request $request) {
        try {
            $coinsForReturn = Coin::select("value")
                                ->where("isForReturn", true)->get();
            Coin::where("isForReturn", true)->delete();
            return $coinsForReturn;
        }
        catch (Exception $e) {
            return response("There was an error.", 500);
        }
    }

}
