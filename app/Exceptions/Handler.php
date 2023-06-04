<?php

namespace App\Exceptions;

use Throwable;
use App\Exceptions\NotEnoughBalanceException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/account/*')) {
                return response()->json([
                    'message' => 'Account not found.'
                ], 404);
            }

            if ($request->is('api/transaction/*')) {
                return response()->json([
                    'message' => 'Transaction not found.'
                ], 404);
            }
        });

        $this->renderable(function (NotEnoughBalanceException $e, $request) {
            return response()->json([
                'message' => 'You do not have enough balance on the account.'
            ], 400);
        });
    }
}
