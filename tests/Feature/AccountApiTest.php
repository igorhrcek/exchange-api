<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
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

    public function test_given_account_uuid_when_calling_show_account_api_account_information_will_be_returned(): void {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->get(route('account.show', ['account' => $account->uuid->toString()]));

        $response->assertValid();
        $response->assertStatus(200)
                ->assertJsonPath('data.currency_id', $account->id)
                ->assertJsonPath('data.user_id', $user->id)
                ->assertJsonPath('data.uuid', $account->uuid->toString());
    }

    public function test_given_incorrect_account_uuid_when_calling_show_account_api_account_information_will_not_be_returned(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->get(route('account.show', ['account' => '12345']));
        $response->assertStatus(404);
    }
}
