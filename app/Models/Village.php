<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = ['ug_id', 'name', 'parish_id'];

    public function parish()
    {
        return $this->belongsTo(Parish::class);
    }
}
