<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomBusinessException;
use Closure;
use Illuminate\Http\Request;

class CheckSchoolSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user->isSchool() && ! $user->hasActiveSubscription()) {
            throw CustomBusinessException::schoolSubscriptionExpired($user);
        }

        return $next($request);
    }
}
