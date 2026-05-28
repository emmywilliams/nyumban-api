<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $fillable = ['ug_id', 'name', 'district_id'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function subCounties()
    {
        return $this->hasMany(SubCounty::class);
    }
}
