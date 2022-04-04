<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Coin;

class CoinControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_getCoin_return_empty_arr() {
        $response = $this->get('/api/coin');
        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_getCoin_return_coins_arr() {

        Coin::create(["value" => 1]);

        $response = $this->get('/api/coin');
        $response->assertStatus(200);
        $response->assertJson([[
            "value" => 1,
            "qty" => 1
        ]]);
    }

    public function test_insertCoin_success() {
        $response = $this->postJson('/api/coin', ['value' => 1]);
 
        $response
            ->assertStatus(200);
    }

    public function test_insertCoin_return_invalid_coin_error() {
        $response = $this->postJson('/api/coin', ['value' => .5]);
 
        $response
            ->assertStatus(400)
            ->assertSeeText("Coin not accepted, coin was returned.");
    }

    public function test_deleteCoin_return_success() {
        Coin::create(["value" => 0.1, "isForReturn" => true]);
        Coin::create(["value" => 0.05, "isForReturn" => true]);
        Coin::create(["value" => 1, "isForReturn" => true]);

        $response = $this->deleteJson('/api/coin');
 
        $response
            ->assertStatus(200)
            ->assertJson([
                [
                    "value" => 0.1,
                ],
                [
                    "value" => 0.05,
                ],
                [
                    "value" => 1,
                ]
            ]);
    }
}
