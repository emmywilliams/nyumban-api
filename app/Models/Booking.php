<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    // 💡 1. Add your fresh structural database columns here
    protected $fillable = [
        'uuid',
        'unit_id',
        'tenant_id',
        'start_date',
        'end_date',
        'stay_type',
        'price_per_period',
        'total_amount',
        'amount_paid',
        'status',
        'payment_status',
        'tenant_notes',
        'cancellation_reason'
    ];

    // 💡 2. Fix the missing method that caused the crash
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
