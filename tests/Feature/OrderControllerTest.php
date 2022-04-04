<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Coin;
use App\Models\Item;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_createOrder_success() {
        Item::create([
            "code" => "SODA",
            "price" => 1.5,
            "qty" => 5
        ]);

        self::createCoinsForChange();
        self::createInsertedCoins();

        $response = $this->postJson('/api/order', ["item" => "SODA"]);
 
        $response
            ->assertStatus(200)
            ->assertJson([
                "item" => "SODA",
                "change" => [
                    0.25
                ]
            ]);
    }

    public function test_createOrder_error_no_more_item() {
        Item::create([
            "code" => "SODA",
            "price" => 1.5,
            "qty" => 0
        ]);

        self::createCoinsForChange();
        self::createInsertedCoins();

        $response = $this->postJson('/api/order', ["item" => "SODA"]);
 
        $response
            ->assertStatus(500)
            ->assertSeeText("Sorry, there is no more SODA");
    }

    public function test_createOrder_error_not_found_item() {

        self::createCoinsForChange();
        self::createInsertedCoins();

        $response = $this->postJson('/api/order', ["item" => "SODA"]);
 
        $response
            ->assertStatus(404)
            ->assertSeeText("Product not found");
    }

    public function test_createOrder_error_insert_more_coins() {
        Item::create([
            "code" => "SODA",
            "price" => 1.5,
            "qty" => 1
        ]);

        self::createCoinsForChange();

        $response = $this->postJson('/api/order', ["item" => "SODA"]);
 
        $response
            ->assertStatus(500)
            ->assertSeeText("Please, insert more coins.");
    }

    public function test_createOrder_error_must_select_a_item() {

        $response = $this->postJson('/api/order');
 
        $response
            ->assertStatus(400)
            ->assertSeeText("You must select a product");
    }

    private function createCoinsForChange() {
        for($i = 0; $i < 50; $i++) {
            $coin = new Coin();
            $coin->value = Coin::VALUES[random_int(0, sizeof(Coin::VALUES)-1)];
            $coin->save();
        }
    }
    private function createInsertedCoins() {
        Coin::create(["value" => 0.25, "isForReturn" => true]);
        Coin::create(["value" => 0.25, "isForReturn" => true]);
        Coin::create(["value" => 0.25, "isForReturn" => true]);
        Coin::create(["value" => 1, "isForReturn" => true]);
    }
}
