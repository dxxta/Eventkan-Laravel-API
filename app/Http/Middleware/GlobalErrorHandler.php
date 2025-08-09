<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GlobalErrorHandler
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (\Throwable $th) {
            // Rollback any open transactions
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            // Log the error
            Log::error('API Error: ' . $th->getMessage(), [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'trace' => $th->getTraceAsString()
            ]);
            
            // Return consistent error response
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'detail' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}