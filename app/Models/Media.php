<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['model_type', 'model_id', 'file_path', 'file_type', 'is_primary'];

    public function model()
    {
        return $this->morphTo();
    }
}
