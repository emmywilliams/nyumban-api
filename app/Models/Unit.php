<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Unit extends Model
{
    protected $fillable = [
        'property_id',
        'name',
        'price',
        'bedrooms',
        'bathrooms',
        'size_sqm',
        'is_available',
        'currency'
    ];

    /**
     * Relationship back to the property
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Relationship to Categories (Amenities)
     */
    public function categories(): BelongsToMany
    {
        // This assumes you have a pivot table: category_unit
        return $this->belongsToMany(Category::class, 'category_unit');
    }

    // Helper to get only amenities for Flutter
    public function getAmenitiesAttribute()
    {
        return $this->categories()->where('type', 'amenity')->get();
    }

    // Helper to get the Unit Type (e.g., Studio)
    public function getTypeAttribute()
    {
        return $this->categories()->where('type', 'unit_type')->first();
    }
}
