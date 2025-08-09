<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Traits\ResponseTrait;

class Controller extends BaseController
{
    use ResponseTrait;
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse($data = null, $message = 'Success', $code = 200)
    {
        return $this->success($data, $message, $code);
    }

    protected function errorResponse($message = 'Error', $code = 400, $errors = null)
    {
        return $this->error($message, $code);
    }
}
