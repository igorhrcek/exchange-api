<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Services\AccountExchangeTransaction;
use App\Http\Resources\TransactionCollection;
use App\Http\Requests\StoreTransactionRequest;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Account $account)
    {
        //If Account UUID is passed, filter transactions only by that account
        //Otherwise, get all accounts that this user has and fetch all transactions
        $accounts = isset($account->id) ? collect($account)->pluck('id') : collect($request->user()->accounts)->pluck('id');
        $transactions = Transaction::whereIn('account_id', $accounts);

        //Provide ability to offset query
        if($request->input('offset')) {
            $transactions->offset($request->input('offset'));
        }

        //Provide ability to limit number of results
        if($request->input('limit')) {
            $transactions->limit($request->input('limit'));
        }

        return new TransactionCollection($transactions->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $exchangeTransaction = new AccountExchangeTransaction(
            Account::find($request->source_account_id),
            Account::find($request->destination_account_id),
            $request->amount
        );
        $exchangeTransaction->execute();

        return new TransactionResource(
            Transaction::reference($exchangeTransaction->transaction->source->reference)->get()
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $reference)
    {
        return new TransactionResource(
            Transaction::reference($reference)->get()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
