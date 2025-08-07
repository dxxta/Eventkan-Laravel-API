<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_removed',
    ];

    protected $casts = [
        'is_removed' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('is_removed', false);
    }

    public function getTotalRegistrationsCountAttribute()
    {
        return $this->registrations()->count();
    }
}
