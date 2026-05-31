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
        // 1. Basic Payload Validation
        $validated = $request->validate([
            'unit_id'    => 'required|exists:units,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
            'stay_type'  => 'required|in:short_term,long_term',
            'tenant_notes' => 'nullable|string',
        ]);

        $tenantId = $request->user()->id;
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        // 2. Wrap query in a Database Transaction to prevent race-conditions
        return DB::transaction(function () use ($validated, $tenantId, $start, $end) {

            // 🔒 Pessimistic Lock: Prevent other database threads from reading/writing to this unit right now
            $unit = Unit::where('id', $validated['unit_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // 3. Double-Booking Validation Block
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

            // 4. Calculate Billing Cost Based on Stay Type
            if ($validated['stay_type'] === 'short_term') {
                $days = $start->diffInDays($end);
                $duration = $days === 0 ? 1 : $days; // Handle same-day checkout anomalies safely
                $totalAmount = $unit->price * $duration;
            } else {
                // Long Term stay: Calculate via monthly intervals
                $months = $start->diffInMonths($end);
                $duration = $months === 0 ? 1 : $months;
                $totalAmount = $unit->price * $duration;
            }

            // 5. Execute Record Insertion
            $booking = Booking::create([
                'uuid'             => (string) Str::uuid(),
                'unit_id'          => $unit->id,
                'tenant_id'        => $tenantId,
                'start_date'       => $start->toDateString(),
                'end_date'         => $end->toDateString(),
                'stay_type'        => $validated['stay_type'],
                'price_per_period' => $unit->price,
                'total_amount'     => $totalAmount,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'tenant_notes'     => $validated['tenant_notes'] ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking request submitted successfully.',
                'data' => [
                    'booking_id'   => $booking->uuid,
                    'total_cost'   => $booking->total_amount,
                    'stay_type'    => $booking->stay_type,
                    'order_status' => $booking->status
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

        // If user is a landlord, load requests incoming to their properties
        if ($user->tokenCan('role:landlord') || $request->header('X-User-Role') === 'landlord') {
            $bookings = Booking::whereHas('unit.property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })
                ->with(['unit.property', 'tenant:id,name,phone'])
                ->latest()
                ->paginate(15);
        } else {
            // Otherwise, fetch regular tenant transaction history records
            $bookings = Booking::where('tenant_id', $user->id)
                ->with(['unit.property'])
                ->latest()
                ->paginate(15);
        }

        return response()->json($bookings);
    }
}
