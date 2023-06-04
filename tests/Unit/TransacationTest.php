<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\ExchangeRate;
use App\Services\AccountExchangeTransaction;
use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\IncorrectSourceAccountException;
use App\Exceptions\AccountsMustBeDifferentException;
use App\Exceptions\IncorrectTransactionAmountException;
use App\Exceptions\IncorrectDestinationAccountException;
use App\Exceptions\AccountsBelongToDifferentUsersException;

class TransacationTest extends TestCase
{
    public function test_given_all_correct_necessary_information_when_creating_transaction_it_will_be_created(): void {
        $user = User::factory()->create();

        $sourceCurrency = Currency::factory()->create();
        $destinationCurrency = Currency::factory()->create();
        ExchangeRate::factory()->create(['from_currency_id' => $sourceCurrency->id, 'to_currency_id' => $destinationCurrency->id]);

        $sourceAccount = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $sourceCurrency->id]);
        $destinationAccount = Account::factory()->create(['user_id' => $user->id,  'currency_id' => $destinationCurrency->id]);
        $amount = 1000;

        $exchangeTransaction = new AccountExchangeTransaction($sourceAccount, $destinationAccount, $amount);
        $exchangeTransaction->execute();

        $this->assertCount(2, Transaction::all());

        $transactions = Transaction::where('reference', '=', $exchangeTransaction->transaction->source->reference)->get();
        $this->assertCount(2, $transactions);
    }

    public function test_given_incorrect_account_information_when_creating_transaction_it_will_return_exception(): void {
        $this->expectException(IncorrectSourceAccountException::class);
        (new AccountExchangeTransaction((new Account()), (new Account()), 1000));

        $this->expectException(IncorrectDestinationAccountException::class);
        (new AccountExchangeTransaction(Account::factory()->create(), (new Account()), 1000));
    }

    public function test_given_accounts_that_belong_to_different_users_when_creating_transaction_it_will_return_exception(): void {
        $sourceAccount = Account::factory()->create();
        $destinationAccount = Account::factory()->create();

        $this->expectException(AccountsBelongToDifferentUsersException::class);
        (new AccountExchangeTransaction($sourceAccount, $destinationAccount, 1000));
    }

    public function test_given_incorrect_amount_when_creating_transaction_it_will_return_exception(): void {
        $this->expectException(IncorrectTransactionAmountException::class);

        $user = User::factory()->create();
        (new AccountExchangeTransaction(
            Account::factory()->create(['user_id' => $user->id]),
            Account::factory()->create(['user_id' => $user->id]),
            -500
        ));
    }

    public function test_given_same_source_and_destination_account_when_creating_transaction_it_will_return_exception(): void {
        $account = Account::factory()->create();

        $this->expectException(AccountsMustBeDifferentException::class);
        (new AccountExchangeTransaction($account, $account, 1000));
    }

    public function test_given_bigger_amount_than_available_on_source_account_when_creating_transaction_it_will_return_exception(): void {
        $user = User::factory()->create();
        $sourceAccount = Account::factory()->create(['balance' => 1, 'user_id' => $user->id]);
        $destinationAccount = Account::factory()->create(['user_id' => $user->id]);

        $this->expectException(NotEnoughBalanceException::class);
        (new AccountExchangeTransaction($sourceAccount, $destinationAccount, 1000));
    }

    public function test_given_amount_when_creating_transaction_it_will_be_correctly_calculated(): void {
        //Prepare transaction
        $exchangeRate = ExchangeRate::factory()->create();
        $transactionAmount = 1000;
        $user = User::factory()->create();
        $sourceAccount = Account::factory()->create(['currency_id' => $exchangeRate->from_currency_id, 'user_id' => $user->id]);
        $destinationAccount = Account::factory()->create(['currency_id' => $exchangeRate->to_currency_id, 'user_id' => $user->id]);

        $exchangeTransaction = new AccountExchangeTransaction($sourceAccount, $destinationAccount, $transactionAmount);
        $exchangeTransaction->execute();

        $transaction = Transaction::where('reference', '=', $exchangeTransaction->transaction->source->reference)
                        ->where('amount', '>', 0)
                        ->first();

        $this->assertEquals($transaction->amount, $transactionAmount * $exchangeRate->rate);
    }

    public function test_given_amount_when_creating_transaction_balance_will_be_updated_on_both_accounts(): void {
        //Prepare transaction
        $user = User::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create();
        $transactionAmount = 1000;
        $sourceAccount = Account::factory()->create(['currency_id' => $exchangeRate->from_currency_id, 'user_id' => $user->id]);
        $destinationAccount = Account::factory()->create(['currency_id' => $exchangeRate->to_currency_id, 'user_id' => $user->id]);

        $sourceAccountStartingBalance = $sourceAccount->balance;
        $destinationAccountStartingBalance = $destinationAccount->balance;
        //Execture transaction
        $exchangeTransaction = new AccountExchangeTransaction($sourceAccount, $destinationAccount, $transactionAmount);
        $exchangeTransaction->execute();

        //Refresh models and do assertions
        $sourceAccount->refresh();
        $destinationAccount->refresh();

        $this->assertEquals($sourceAccount->balance, $sourceAccountStartingBalance - $transactionAmount);
        $this->assertEquals($destinationAccount->balance, $destinationAccountStartingBalance + ($transactionAmount * $exchangeRate->rate));
    }
}
