<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationUpdateStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:process,finished,cancelled',
        ];
    }
}
