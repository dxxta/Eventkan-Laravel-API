<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventCategories extends Pivot
{
    use HasFactory;

    protected $table = 'event_categories';
    
    protected $fillable = [
        'event_id',
        'category_id',
    ];

    public $timestamps = true;

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('category', function($q) {
            $q->where('is_removed', false);
        });
    }

    public function scopePublished($query)
    {
        return $query->whereHas('event', function($q) {
            $q->where('is_published', true);
        });
    }
}
