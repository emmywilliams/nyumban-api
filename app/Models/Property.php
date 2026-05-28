<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'landlord_id',
        'district_id',
        'county_id',
        'sub_county_id',
        'parish_id',
        'village_id',
        'title',
        'description',
        'address',
        'is_gated',
        'is_multi_unit',
        'latitude',
        'longitude',
        'status'
    ];

    protected $casts = [
        'is_gated' => 'boolean',
        'is_multi_unit' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function booted()
    {
        static::creating(fn($property) => $property->uuid = (string) Str::uuid());
    }

    // Relationships
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function subCounty(): BelongsTo
    {
        return $this->belongsTo(SubCounty::class, 'sub_county_id');
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'property_categories');
    }

    public function media(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Favorite properties (many-to-many relationship)
    public function favoritedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'favorites'
        )->withTimestamps();
    }
}
