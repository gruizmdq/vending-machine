<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Coin;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    public function createOrder(Request $request) {
        try {
            
            $item = self::validateOrder($request);

            DB::beginTransaction();
            
            $item->qty -= 1;
            $item->save();

            $change = Coin::getChange($item->price);
            Coin::setInsertedCoinToIsNotReturned();
            
            DB::commit();
            
            return $change;


        }
        catch(ValidationException $e) {
            return response("You must select a product.", 400);
        }
        catch(ModelNotFoundException $e) {
            return response("Product not found.", 400);
        }
        catch(Exception $e){
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        
    }

    private function validateOrder(Request $request): Item {
        $request->validate([
            'item' => 'required|max:255'
        ]);

        $item = Item::where("code", strtolower($request->input("item")))
                ->firstOrFail();

        if ($item->qty <= 0)
            throw new Exception("Sorry, there is no more ".$item->code);        
        if (Coin::getCurrentCredit() < $item->price)
            throw new Exception("Please, insert more coins.");
        
        return $item;
    }
}
