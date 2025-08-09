<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'category_ids' => 'required|array',
            'location' => 'required|string|max:255',
            'max_participants' => 'required|integer',
            'is_published' => 'required|boolean',
        ];
    }
}
