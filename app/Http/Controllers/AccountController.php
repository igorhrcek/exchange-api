<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;
use App\Http\Resources\AccountCollection;
use App\Http\Requests\StoreAccountRequest;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return new AccountCollection(Account::where(['user_id' => $request->user()->id])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request)
    {
        $account = Account::create([
            'user_id' => $request->user()->id,
            'currency_id' => $request->currency_id,
            'uuid' => Str::uuid(),
            'balance' => fake()->numberBetween(2000, 15000)
        ]);

        return new AccountResource($account);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        return new AccountResource($account);
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
