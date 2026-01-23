<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Custom render for exceptions.
     */
    public function render($request, Throwable $exception)
    {
        // Handle 419 Page Expired (CSRF Token mismatch)
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('login')
                ->with('error', '⚠️ Your session has expired. Please login again.');
        }

        return parent::render($request, $exception);
    }
}
