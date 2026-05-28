<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = ['ug_id', 'name'];


    public function countiess(): HasMany
    {
        return $this->hasMany(County::class);
    }
}
