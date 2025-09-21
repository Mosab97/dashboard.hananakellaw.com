<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        if ($this->isApiRequest($request)) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Check if request is an API request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function isApiRequest($request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    /**
     * Handle API exceptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleApiException($request, Throwable $e)
    {
        // Log the exception details
        Log::error('API Exception', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        // Handle CustomBusinessException
        if ($e instanceof CustomBusinessException) {
            $code = $e->getCode() ?: 422; // Default to 422 if no code is set

            return apiError(
                message: $e->getMessage(),
                status: $code,
                data: $e->getData(),
                errors: null
            );
        }

        // Handle ValidationException
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        // Handle ModelNotFoundException
        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));

            return apiError(
                // message: "{$model} not found",
                message: api('The requested resource was not found'),
                status: 404,
                errors: ['resource' => 'The requested resource was not found']
            );
        }

        // Handle AuthenticationException
        if ($e instanceof AuthenticationException) {
            return apiError(
                message: 'Unauthenticated',
                status: 401,
                errors: ['auth' => 'Please login to access this resource']
            );
        }

        // Handle NotFoundHttpException
        if ($e instanceof NotFoundHttpException) {
            return apiError(
                message: 'Route not found',
                status: 404,
                errors: ['route' => 'The requested URL was not found']
            );
        }

        if ($e instanceof ThrottleRequestsException) {
            return response()->json([
                'message' => 'Too many requests',
                'status' => false,
                'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
            ], 429);
        }

        // Handle all other exceptions
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        // Only show detailed error messages in non-production environments
        $message = app()->environment('production')
            ? 'An unexpected error occurred'
            : $e->getMessage();

        return apiError(
            message: $message,
            status: $statusCode,
            data: app()->environment('production') ? null : [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
            errors: ['server' => 'An internal server error occurred']
        );
    }

    /**
     * Handle validation exceptions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleValidationException(ValidationException $e)
    {
        $errors = $e->errors();

        // Get the first error message
        $firstError = array_values($errors)[0][0] ?? 'Validation failed';

        // Format errors to be more user-friendly
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages[0]; // Take only the first error message for each field
        }

        return apiError(
            message: $firstError,
            status: 422,
            data: [
                // 'failed_fields' => array_keys($errors)
            ],
            errors: $formattedErrors
        );
    }
}
