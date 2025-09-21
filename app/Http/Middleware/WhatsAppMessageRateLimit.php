<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomBusinessException;
use App\Services\MessageRateLimit\MessageRateLimitService;
use Closure;
use Illuminate\Http\Request;

class WhatsAppMessageRateLimit
{
    protected $messageLimitService;

    public function __construct(MessageRateLimitService $messageLimitService)
    {
        $this->messageLimitService = $messageLimitService;
    }

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user->isSchool()) {
            $school = $user->schoolProfile;

            // If this is a bulk notification, estimate message count from request
            if ($request->has('ids_array') && is_array($request->ids_array)) {
                $estimatedCount = count($request->ids_array);

                // For each recipient, check if we can send a message
                try {
                    $this->messageLimitService->canSendMessages($school, $estimatedCount);
                } catch (CustomBusinessException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'data' => $e->getContext(),
                    ], $e->getCode());
                }
            }
        }

        return $next($request);
    }
}
