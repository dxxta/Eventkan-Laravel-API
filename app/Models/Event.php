<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

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
        'max_participants' => 'integer',
        'location' => 'array',
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
