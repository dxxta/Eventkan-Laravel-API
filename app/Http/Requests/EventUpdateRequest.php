<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'content' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'category_ids' => 'sometimes|array',
            'location' => 'sometimes|string|max:255',
            'max_participants' => 'sometimes|integer',
            'is_published' => 'sometimes|boolean',
        ];
    }
}
