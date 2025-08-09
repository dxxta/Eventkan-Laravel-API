<?php

namespace App\Http\Traits;

trait ResponseTrait
{
    public function success($data, $message, $code)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'result' => $data,
        ], $code);
    }

    public function error($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
