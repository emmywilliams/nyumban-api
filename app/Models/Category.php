<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'icon'
    ];

    /**
     * Relationship to Units (Many-to-Many)
     */
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'category_unit');
    }
}
