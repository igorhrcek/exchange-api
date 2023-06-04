<?php

namespace App\Services;

use App\Services\ExchangeService;
use App\Models\Transaction;
use App\Models\Account as Account;
use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\IncorrectSourceAccountException;
use App\Exceptions\AccountsMustBeDifferentException;
use App\Exceptions\IncorrectTransactionAmountException;
use App\Exceptions\IncorrectDestinationAccountException;
use App\Exceptions\AccountsBelongToDifferentUsersException;
use App\Models\ExchangeRate;
use stdClass;

class AccountExchangeTransaction {
    /**
     * Account from which we are taking money
     *
     * @var Account
     */
    private $sourceAccount;

    /**
     * Account to which we are transferring money
     *
     * @var Account
     */
    private $destinationAccount;

    /**
     * Base transfer amount
     *
     * @var float
     */
    private $amount;

    /**
     * Converted transfer amount
     *
     * @var float
     */
    private $exchangedAmount;

    /**
     * Transaction reference number
     *
     * @var string
     */
    private $transactionReference;

    /**
     * Keeps transaction information
     *
     * @var object
     */
    public $transaction;

    /**
     * Construct me gently
     *
     * @param Account $sourceAccount
     * @param Account $destinationAccount
     * @param float $amount
     */
    public function __construct(Account $sourceAccount, Account $destinationAccount, float $amount) {
        $this->sourceAccount = $this->setSourceAccount($sourceAccount);
        $this->destinationAccount = $this->setDestinationAccount($destinationAccount);
        $this->amount = $this->setAmount($amount);
        $this->exchangedAmount = $this->calculateExchangeAmount();
        $this->transactionReference = $this->setTransactionReference();
    }

    /**
     * Set source account information
     *
     * We need to make sure that value passed is instance of Account::class
     *
     * @param Account $account
     * @return Account
     * @throws IncorrectSourceAccountException
     */
    private function setSourceAccount(Account $account): Account {
        if(!($account instanceof Account) || !isset($account->id)) {
            throw new IncorrectSourceAccountException();
        }

        return $account;
    }

    /**
     * Set destination class
     *
     * We need to make sure that both source and destination accounts belong to same user and that information passed is instance of Account::class
     *
     * @param Account $account
     * @return Account
     * @throws IncorrectDestinationAccountException|AccountsBelongToDifferentUsersException|
     */
    private function setDestinationAccount(Account $account): Account {
        if(!($account instanceof Account) || !isset($account->id)) {
            throw new IncorrectDestinationAccountException();
        }

        if($this->sourceAccount->id === $account->id) {
            throw new AccountsMustBeDifferentException();
        }

        if($this->sourceAccount->user_id !== $account->user_id) {
            throw new AccountsBelongToDifferentUsersException();
        }

        return $account;
    }

    /**
     * Set trasanction amount
     *
     * We need to check if transaction amount is within set limits (gt than 0 and that there is enough balance on source account)
     *
     * @param float $amount
     * @return float
     * @throws IncorrectTransactionAmountException|NotEnoughBalanceException
     */
    private function setAmount(float $amount): float {
        if($amount <= 0) {
            throw new IncorrectTransactionAmountException();
        }

        if($this->sourceAccount->balance < $amount) {
            throw new NotEnoughBalanceException();
        }

        return $amount;
    }

    /**
     * Calculates and sets destination account transactional amount
     *
     * @return float
     */
    private function calculateExchangeAmount(): float {
        $exchangeRate = ExchangeRate::where('from_currency_id', '=', $this->sourceAccount->currency->id)->
                        where('to_currency_id', '=', $this->destinationAccount->currency->id)
                        ->first();

        return ExchangeService::convert($this->amount, $exchangeRate->rate);
    }

    /**
     * Returns a unique transaction reference id
     *
     * @return string
     */
    private function setTransactionReference(): string {
        return fake()->unique()->regexify('[A-Z0-9]{20}');
    }

    /**
     * Executes transaction between accounts
     *
     * First we need to create a transaction on a source account and take certain amount out of it, then create another one and add converter amount.
     *
     * @return bool
     */
    public function execute(): bool {
        //Create transaction on source account and reduce its balance
        $sourceAccountTransaction = Transaction::create([
            'account_id' => $this->sourceAccount->id,
            'reference' => $this->transactionReference,
            'amount' => -$this->amount
        ]);

        //Fail early if we cannot create a transaction
        if(!$sourceAccountTransaction) {
            return false;
        }

        $destinationAccountTransaction = Transaction::create([
            'account_id' => $this->destinationAccount->id,
            'reference' => $this->transactionReference,
            'amount' => $this->exchangedAmount
        ]);

        if(!$destinationAccountTransaction) {
            return false;
        }

        //Update balance of both accounts
        $this->sourceAccount->balance -= $this->amount;
        $this->sourceAccount->update();

        $this->destinationAccount->balance += $this->exchangedAmount;
        $this->destinationAccount->update();

        //Store resulting transaction objects
        $this->transaction = new stdClass();
        $this->transaction->source = $sourceAccountTransaction;
        $this->transaction->destination = $destinationAccountTransaction;

        return true;
    }
}
