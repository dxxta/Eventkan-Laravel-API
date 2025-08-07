<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = [
        'user_id',
        'entity_id',
        'entity_name',
        'field_name',
        'field_value',
        'action',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
