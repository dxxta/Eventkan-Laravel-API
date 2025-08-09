<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_categories');
    }

    public function registrations()
    {
        return Registration::whereHas('event.categories', function($query) {
            $query->where('event_categories.category_id', $this->id);
        });
    }

    public function index()
    {
        return $this->where('deleted_at', null);
    }

    public function getTotalRegistrationsCountAttribute()
    {
        return $this->registrations()->count();
    }
}
