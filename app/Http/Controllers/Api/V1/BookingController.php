<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Store a fresh booking request securely.
     */
    public function store(Request $request)
    {
        // 1. Basic Payload Validation (Added explicit duration requirements)
        $validated = $request->validate([
            'unit_id'      => 'required|exists:units,id',
            'start_date'   => 'required|date|after_or_equal:today',
            'end_date'     => 'required|date|after:start_date',
            'stay_type'    => 'required|in:short_term,long_term',
            'duration'     => 'required|integer|min:1', // 🎯 Pass the explicit duration from the scroller selector wheel
            'tenant_notes' => 'nullable|string',
        ]);

        $tenantId = $request->user()->id;
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $duration = (int) $validated['duration'];

        // 2. Wrap query in a Database Transaction to prevent race-conditions down to the millisecond
        return DB::transaction(function () use ($validated, $tenantId, $start, $end, $duration) {

            // 🔒 Pessimistic Lock: Queues overlapping read/write queries to prevent double allocations
            $unit = Unit::where('id', $validated['unit_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // 🛑 Hard Operational Check
            if ($unit->status === 'occupied') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This unit is currently occupied by another resident tenant.'
                ], 422);
            }

            // 3. Double-Booking Validation Block (Flawless inverted date intersection logic)
            $isOverlapping = Booking::where('unit_id', $unit->id)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->where(function ($query) use ($start, $end) {
                    $query->where('start_date', '<', $end->toDateString())
                        ->where('end_date', '>', $start->toDateString());
                })
                ->exists();

            if ($isOverlapping) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This unit is already booked or has an active pending request for the selected dates.'
                ], 422);
            }

            // 4. Calibrate Financial Escrow Calculations to mirror Flutter UI precisely
            $basePricePerPeriod = (float) $unit->price;
            $rawSubtotal = $basePricePerPeriod * $duration;

            // 🎯 Added Platform Infrastructure Fee (5%) to balance ledger allocations perfectly
            $serviceFee = round($rawSubtotal * 0.05, 0);
            $totalEscrowPayable = $rawSubtotal + $serviceFee;

            // 5. Execute Booking Record Insertion
            $booking = Booking::create([
                'uuid'             => (string) Str::uuid(),
                'unit_id'          => $unit->id,
                'tenant_id'        => $tenantId,
                'start_date'       => $start->toDateString(),
                'end_date'         => $end->toDateString(),
                'stay_type'        => $validated['stay_type'],
                'price_per_period' => $basePricePerPeriod,
                'total_amount'     => $totalEscrowPayable, // Store total escrow due cleanly
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'tenant_notes'     => $validated['tenant_notes'] ?? null,
            ]);

            // Update unit state criteria layout smoothly
            $unit->update([
                'status' => 'pending_approval'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking request submitted successfully.',
                'data' => [
                    'booking_id'      => $booking->uuid,
                    'base_cost'       => $rawSubtotal,
                    'infrastructure_fee' => $serviceFee,
                    'total_payable'   => $booking->total_amount,
                    'stay_type'       => $booking->stay_type,
                    'order_status'    => $booking->status
                ]
            ], 201);
        });
    }

    /**
     * Get booking histories for the current logged-in profile
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Safe evaluation of role capability layers
        if ($user->tokenCan('role:landlord') || $request->header('X-User-Role') === 'landlord') {
            $bookings = Booking::whereHas('unit.property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })
                ->with(['unit.property', 'tenant:id,name,phone'])
                ->latest()
                ->paginate(15);
        } else {
            $bookings = Booking::where('tenant_id', $user->id)
                ->with(['unit.property'])
                ->latest()
                ->paginate(15);
        }

        return response()->json($bookings);
    }
}
