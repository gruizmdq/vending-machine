<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Coin extends Model
{
    use HasFactory;
    final const VALUES = [0.05, 0.1, 0.25, 1.0];
    protected $fillable = ['value', "isForReturn"];

    public static function getCurrentCredit() {
        return Coin::where("isForReturn", true)
            ->sum("value");
    }

    public static function getChange(float $price) {
        $creditToReturn = Coin::getCurrentCredit() - $price;
        $coinsToReturn = [];
        $stop = false;
        $index = sizeof(self::VALUES) - 1;
        while ($creditToReturn > 0) {
            if($index == -1) {
                break;
            }
            $coin = Coin::where([
                ["value", self::VALUES[$index]],
                ["isForReturn", false]
                ])
                ->first();
            if ($coin == null || $coin->value > $creditToReturn)
                $index -= 1;
            else {
                $creditToReturn -= $coin->value;
                $coinsToReturn[] = $coin->value;
                $coin->delete();
            }
        }
        return $coinsToReturn;
    }

    public static function setInsertedCoinToIsNotReturned() {
        Coin::where('isForReturn', true)
            ->update(['isForReturn' => false]);
    }

    public static function getValueValidator($data) {
        return Validator::make($data, [
            'value' => [
                'required',
                Rule::in(Coin::VALUES),
            ],
        ]);
    }
}


