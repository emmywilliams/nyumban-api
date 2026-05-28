<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['uuid', 'listing_id', 'tenant_id', 'start_date', 'end_date', 'total_amount', 'status', 'payment_status'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
