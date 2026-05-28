<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'unit_id',
        'title',
        'daily_price',
        'weekly_price',
        'monthly_price',
        'minimum_stay_days',
        'rules',
        'visibility_status',
        'is_featured'
    ];

    protected $casts = [
        'rules' => 'json',
        'is_featured' => 'boolean',
        'daily_price' => 'decimal:2',
        'monthly_price' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(fn($listing) => $listing->uuid = (string) Str::uuid());
    }

    // Relationships
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class);
    }
}
