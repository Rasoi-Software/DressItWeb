<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // If it's an unauthenticated exception, delegate to the custom method
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Custom unauthenticated response for API routes.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // This will cover most API requests
        if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
            return returnError('Authentication failed');
        }

        // Fallback for web requests
        return redirect()->guest(route('login'));
    }
}
