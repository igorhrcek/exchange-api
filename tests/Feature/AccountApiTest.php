<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Currency;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_given_all_necessary_information_when_calling_create_account_api_account_will_be_created(): void {
        Sanctum::actingAs(User::factory()->create(), ['*']);
        $currency = Currency::factory()->create();

        $response = $this->post(route('account.create'), [
            'currency_id' => $currency->id,
        ]);

        $response->assertValid();
        $response->assertStatus(201)
                ->assertJsonPath('data.currency_id', $currency->id);
    }

    public function test_given_missing_currency_when_calling_create_account_api_account_will_not_be_created(): void {
        Sanctum::actingAs(User::factory()->create(), ['*']);
        $response = $this->post(route('account.create'), []);

        $response->assertStatus(400);
        $response->assertInvalid(['currency_id']);
    }

    public function test_given_already_used_currency_when_calling_create_account_api_account_will_not_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $currency = Currency::factory()->create();

        $this->post(route('account.create'), [
            'currency_id' => $currency->id,
        ]);

        $response = $this->post(route('account.create'), [
            'currency_id' => $currency->id,
        ]);

        $response->assertStatus(400);
        $response->assertInvalid(['currency_id']);
    }

    public function test_given_unknown_currency_when_calling_create_account_api_account_will_not_be_created(): void {
        Sanctum::actingAs(User::factory()->create(), ['*']);
        $response = $this->post(route('account.create'), [
            'currency_id' => 500,
        ]);

        $response->assertStatus(400);
        $response->assertInvalid(['currency_id']);
    }
}
