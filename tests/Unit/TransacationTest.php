<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\ExchangeRate;
use App\Exceptions\IncorrectSourceAccountException;
use App\Exceptions\IncorrectDestinationAccountException;
use App\Exceptions\AccountsBelongToDifferentUsers;
use App\Exceptions\IncorrectTransactionAmount;
use App\Exceptions\AccountsMustBeDifferent;
use App\Exceptions\NotEnoughBalance;
use App\Services\AccountTransaction;

class TransacationTest extends TestCase
{
    public function test_given_all_correct_necessary_information_when_creating_transaction_it_will_be_created(): void {
        $user = User::factory()->create();
        $sourceAccount = Account::factory()->create(['user_id' => $user->id]);
        $destinationAccount = Account::factory()->create(['user_id' => $user->id]);
        $amount = 1000;
        $transaction = new AccountTransaction($sourceAccount, $destinationAccount, $amount);

        $this->assertCount(2, Transaction::all());

        $transactions = Transaction::where('reference', $transaction->getReference())->get();
        $this->assertCount(2, $transactions);
    }

    public function test_given_incorrect_account_information_when_creating_transaction_it_will_return_exception(): void {
        $this->expectException(IncorrectSourceAccountException::class);
        (new AccountTransaction('blah', 'blah', 1000));

        $this->expectException(IncorrectDestinationAccountException::class);
        (new AccountTransaction(Account::factory()->create(), 'blah', 1000));
    }

    public function test_given_accounts_that_belong_to_different_users_when_creating_transaction_it_will_return_exception(): void {
        $sourceAccount = Account::factory()->create();
        $destinationAccount = Account::factory()->create();

        $this->expectException(AccountsBelongToDifferentUsers::class);
        (new AccountTransaction($sourceAccount, $destinationAccount, 1000));
    }

    public function test_given_incorrect_amount_when_creating_transaction_it_will_return_exception(): void {
        $this->expectException(IncorrectTransactionAmount::class);

        (new AccountTransaction(
            Account::factory()->create(),
            Account::factory()->create(),
            -500
        ));
    }

    public function test_given_same_source_and_destination_account_when_creating_transaction_it_will_return_exception(): void {
        $account = Account::factory()->create();

        $this->expectException(AccountsMustBeDifferent::class);
        (new AccountTransaction($account, $account, 1000));
    }

    public function test_given_bigger_amount_than_available_on_source_account_when_creating_transaction_it_will_return_exception(): void {
        $sourceAccount = Account::factory()->create(['balance' => 1]);
        $destinationAccount = Account::factory()->create();

        $this->expectException(NotEnoughBalance::class);
        (new AccountTransaction($sourceAccount, $destinationAccount, 1000));
    }

    public function test_given_amount_when_creating_transaction_it_will_be_correctly_calculated(): void {
        //Prepare transaction
        $exchangeRate = ExchangeRate::factory()->create();
        $transactionAmount = 1000;
        $sourceAccount = Account::factory()->create(['currency_id' => $exchangeRate->from_currency_id]);
        $destinationAccount = Account::factory()->create(['currency_id' => $exchangeRate->to_currency_id]);

        $transactionReference = (new AccountTransaction($sourceAccount, $destinationAccount, $transactionAmount))->execute();

        $transaction = Transaction::where('reference', '=', $transactionReference)
                        ->where('amount', '>', 0)
                        ->first();

        $this->assertEquals($transaction->amount, $transactionAmount * $exchangeRate->rate);
    }

    public function test_given_amount_when_creating_transaction_balance_will_be_updated_on_both_accounts(): void {
        //Prepare transaction
        $exchangeRate = ExchangeRate::factory()->create();
        $transactionAmount = 1000;
        $sourceAccount = Account::factory()->create(['currency_id' => $exchangeRate->from_currency_id]);
        $destinationAccount = Account::factory()->create(['currency_id' => $exchangeRate->to_currency_id]);

        //Execture transaction
        $transactionReference = (new AccountTransaction($sourceAccount, $destinationAccount, $transactionAmount))->execute();

        //Refresh models and do assertions
        $sourceAccount->refresh();
        $destinationAccount->refresh();

        $this->assertEquals($sourceAccount->balance, $sourceAccount->balance - $transactionAmount);
        $this->assertEquals($destinationAccount->balance, $transactionAmount * $exchangeRate->rate);
    }
}
