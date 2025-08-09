<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ResponseTrait;

class ValidateRole
{
    use ResponseTrait;
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role, $roles)) {
            return $this->error('Unauthorized', 403);
        }

        return $next($request);
    }
}

