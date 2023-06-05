<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\ExchangeRate;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_given_all_necessary_information_when_calling_create_transaction_api_transaction_will_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $currency = ExchangeRate::factory()->create();
        $sourceAccount = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->from_currency_id
        ]);
        $destinationAccount = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->to_currency_id
        ]);

        $response = $this->post(route('transaction.create'), [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'amount' => 500
        ]);

        $response->assertValid();
        $response->assertStatus(200);
    }

    public function test_given_missing_account_information_when_calling_create_transaction_api_transaction_will_not_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->post(route('transaction.create'), [
            'destination_account_id' => 12,
            'amount' => 500
        ]);
        $response->assertStatus(400);
        $response->assertInvalid(['source_account_id']);

        $response = $this->post(route('transaction.create'), [
            'source_account_id' => 12,
            'amount' => 500
        ]);
        $response->assertStatus(400);
        $response->assertInvalid(['source_account_id']);
    }

    public function test_given_account_that_does_not_belong_to_user_when_calling_create_transaction_api_transaction_will_not_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $currency = ExchangeRate::factory()->create();
        $sourceAccount = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->from_currency_id
        ]);
        $destinationAccount = Account::factory()->create([
            'user_id' => 12,
            'currency_id' => $currency->to_currency_id
        ]);

        $response = $this->post(route('transaction.create'), [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'amount' => 500
        ]);
        $response->assertStatus(400);
        $response->assertInvalid(['destination_account_id']);
    }

    public function test_given_destination_account_that_is_same_as_source_account_when_calling_create_transaction_api_transaction_will_not_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => 1
        ]);

        $response = $this->post(route('transaction.create'), [
            'source_account_id' => $account->id,
            'destination_account_id' => $account->id,
            'amount' => 500
        ]);

        $response->assertStatus(400);
        $response->assertInvalid(['destination_account_id']);
    }

    public function test_given_missing_amount_when_calling_create_transaction_api_transaction_will_not_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->post(route('transaction.create'), [
            'source_account_id' => 1,
            'destination_account_id' => 2,
        ]);

        $response->assertStatus(400);
        $response->assertInvalid(['amount']);
    }

    public function test_given_incorrect_amount_when_calling_create_transaction_api_transaction_will_not_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $sourceAccount = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => 1
        ]);
        $destinationAccount = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => 2
        ]);

        $response = $this->post(route('transaction.create'), [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'amount' => -800
        ]);

        $response->assertStatus(422);
    }

    public function test_given_amount_that_is_bigger_than_balance_when_calling_create_transaction_api_transaction_will_not_be_created(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $sourceAccount = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => 1
        ]);
        $destinationAccount = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => 2
        ]);

        $response = $this->post(route('transaction.create'), [
            'source_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'amount' => 20000
        ]);

        $response->assertStatus(400);
    }

    public function test_given_account_when_calling_get_transactions_api_only_transactions_from_that_account_will_be_returned(): void {
        $transaction = Transaction::factory()->create();
        $account = Account::find($transaction->account_id);
        Sanctum::actingAs(User::find($account->user_id), ['*']);

        $response = $this->get(route('transaction.show', ['transaction' => $transaction->reference]));

        $response->assertStatus(200)
                ->assertJsonPath('data.reference', $transaction->reference)
                ->assertJsonPath('data.account_id', $transaction->account_id)
                ->assertJsonPath('data.amount', strval($transaction->amount));
    }

    public function test_given_missing_account_when_calling_get_transactions_api_all_transactions_will_be_returned(): void {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        Transaction::factory(10)->create(['account_id' => $account->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->get(route('transaction.index'));

        $response->assertStatus(200)
                ->assertJsonCount(10, 'data');
    }

    public function test_given_offset_when_calling_get_transactions_api_list_of_returned_transactions_will_be_changed(): void {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        Transaction::factory(10)->create(['account_id' => $account->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/api/transactions/?offset=3');

        $response->assertStatus(200)
                ->assertJsonCount(7, 'data');
    }

    public function test_given_limit_when_calling_get_transactions_api_total_number_of_returned_transactions_will_be_reduced(): void {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        Transaction::factory(10)->create(['account_id' => $account->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/api/transactions/?limit=5');

        $response->assertStatus(200)
                ->assertJsonCount(5, 'data');
    }

    public function test_given_transaction_reference_when_calling_get_transaction_transaction_information_will_be_returned(): void {
        $transaction = Transaction::factory()->create();
        $account = Account::find($transaction->account_id);
        Sanctum::actingAs(User::find($account->user_id), ['*']);

        $response = $this->get(route('transaction.show', ['transaction' => $transaction->reference]));

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonPath('data.reference', $transaction->reference)
                ->assertJsonPath('data.account_id', $transaction->account_id);
    }

    public function test_given_incorrect_transaction_reference_when_calling_get_transaction_transaction_information_will_not_be_returned(): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->get(route('transaction.show', ['transaction' => 'zzzz']));

        $response->assertStatus(404);
    }
}
