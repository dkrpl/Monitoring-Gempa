<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        // Custom render untuk error pages
        $this->renderable(function (Throwable $e, Request $request) {
            return $this->handleException($e, $request);
        });
    }

    /**
     * Handle custom error rendering
     */
    private function handleException(Throwable $e, Request $request)
    {
        // Only return custom error pages for web requests
        if ($request->expectsJson()) {
            return $this->renderJsonError($e);
        }

        $statusCode = $this->getStatusCode($e);

        // Render custom error pages
        if (view()->exists("errors.{$statusCode}")) {
            return response()->view("errors.{$statusCode}", [
                'exception' => $e,
                'statusCode' => $statusCode,
                'message' => $this->getErrorMessage($e, $statusCode)
            ], $statusCode);
        }

        return null; // Let Laravel handle it
    }

    /**
     * Get HTTP status code from exception
     */
    private function getStatusCode(Throwable $e): int
    {
        if ($e instanceof HttpException) {
            return $e->getStatusCode();
        }

        // Default status codes
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return 404;
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return 401;
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return 422;
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return 405;
        }

        return 500;
    }

    /**
     * Get user-friendly error message
     */
    private function getErrorMessage(Throwable $e, int $statusCode): string
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Page Not Found',
            405 => 'Method Not Allowed',
            419 => 'Page Expired',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        return $messages[$statusCode] ?? 'Something went wrong';
    }

    /**
     * Render JSON error response
     */
    private function renderJsonError(Throwable $e)
    {
        $statusCode = $this->getStatusCode($e);

        return response()->json([
            'success' => false,
            'error' => [
                'code' => $statusCode,
                'message' => $this->getErrorMessage($e, $statusCode),
                'details' => config('app.debug') ? $e->getMessage() : null,
            ]
        ], $statusCode);
    }

    /**
 * Render the exception into an HTTP response.
 */
public function render($request, Throwable $e)
{
    // Custom error pages for web requests
    if ($request->expectsJson()) {
        return $this->renderJsonError($e);
    }

    $statusCode = $this->getStatusCode($e);

    // Try to render specific error page
    if (view()->exists("errors.{$statusCode}")) {
        return response()->view("errors.{$statusCode}", [
            'exception' => $e,
            'statusCode' => $statusCode,
            'message' => $this->getErrorMessage($e, $statusCode)
        ], $statusCode);
    }

    // Fallback to generic error page
    return response()->view('errors.generic', [
        'exception' => $e,
        'statusCode' => $statusCode,
        'message' => $this->getErrorMessage($e, $statusCode)
    ], $statusCode);
}
}
