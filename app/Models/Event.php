<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'is_published',
        'start_date',
        'end_date',
        'max_participants',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'event_categories');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function getAvailableSpotsAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        
        $registeredCount = $this->registrations()->where('status', '!=', 'cancelled')->count();
        return max(0, $this->max_participants - $registeredCount);
    }

    public function getIsFullAttribute()
    {
        return $this->max_participants && $this->available_spots <= 0;
    }
}
