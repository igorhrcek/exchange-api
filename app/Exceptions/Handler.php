<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use App\Exceptions\NotEnoughBalanceException;
use Illuminate\Validation\ValidationException;
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
        $this->renderable(function (ValidationException  $e, $request) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (NotEnoughBalanceException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have enough balance on the account.'
            ], Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/account/*')) {
                return response()->json([
                    'message' => 'Account not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            //Fail if transaction reference provided is incorrect
            if ($request->is('api/transaction/*')) {
                return response()->json([
                    'message' => 'Transaction not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            //Fail if account UUID provided is incorrect
            if ($request->is('api/transactions/*')) {
                return response()->json([
                    'message' => 'Account not found.'
                ], Response::HTTP_NOT_FOUND);
            }
        });
    }
}
