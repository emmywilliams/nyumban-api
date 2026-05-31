<?php

// namespace Database\Seeders;

// use App\Models\User;
// use App\Models\Unit;
// use App\Models\Booking;
// use Illuminate\Database\Seeder;
// use Illuminate\Support\Str;
// use Carbon\Carbon;

// class BookingTestSeeder extends Seeder
// {
//     public function run(): void
//     {
//         // 1. Fetch an existing user to act as the tenant
//         // We look for a tenant account, or default to any available user record
//         $tenant = User::where('email', '!=', '')->first();

//         if (!$tenant) {
//             $this->command->warn('❌ No users found in the database. Please register a user first.');
//             return;
//         }

//         // 2. Fetch two existing units from your real properties to test both stay types
//         $shortTermUnit = Unit::first();
//         $longTermUnit = Unit::skip(1)->first() ?? $shortTermUnit; // Fallback to first if only one exists

//         if (!$shortTermUnit) {
//             $this->command->warn('❌ No units found in the database. Please add units to your properties first.');
//             return;
//         }

//         $this->command->info("♻️ Generating real bookings for Tenant: {$tenant->name} ({$tenant->email})");

//         // 3. Create a Real Short-Term Booking (e.g., 3-day stay)
//         $startDateST = Carbon::now()->addDays(2);
//         $endDateST = Carbon::now()->addDays(5);
//         $days = $startDateST->diffInDays($endDateST);

//         Booking::create([
//             'uuid' => (string) Str::uuid(),
//             'unit_id' => $shortTermUnit->id,
//             'tenant_id' => $tenant->id,
//             'start_date' => $startDateST->toDateString(),
//             'end_date' => $endDateST->toDateString(),
//             'stay_type' => 'short_term',
//             'price_per_period' => $shortTermUnit->price,
//             'total_amount' => $shortTermUnit->price * $days,
//             'amount_paid' => 0.00,
//             'status' => 'pending',
//             'payment_status' => 'unpaid',
//             'tenant_notes' => 'This is a real test booking for a short-term stay using existing data.',
//         ]);

//         $this->command->info("✅ Short-term booking generated for Unit: {$shortTermUnit->name}");

//         // 4. Create a Real Long-Term Booking (e.g., 2-month lease, if a second unit exists)
//         if ($longTermUnit && $longTermUnit->id !== $shortTermUnit->id) {
//             $startDateLT = Carbon::now()->addWeek();
//             $endDateLT = Carbon::now()->addWeek()->addMonths(2);
//             $months = $startDateLT->diffInMonths($endDateLT);
//             $durationMonths = $months === 0 ? 1 : $months;

//             Booking::create([
//                 'uuid' => (string) Str::uuid(),
//                 'unit_id' => $longTermUnit->id,
//                 'tenant_id' => $tenant->id,
//                 'start_date' => $startDateLT->toDateString(),
//                 'end_date' => $endDateLT->toDateString(),
//                 'stay_type' => 'long_term',
//                 'price_per_period' => $longTermUnit->price,
//                 'total_amount' => $longTermUnit->price * $durationMonths,
//                 'amount_paid' => $longTermUnit->price, // Let's simulate 1 month deposit paid
//                 'status' => 'confirmed',
//                 'payment_status' => 'partially_paid',
//                 'tenant_notes' => 'This is a real test booking for a long-term lease using existing data.',
//             ]);

//             $this->command->info("✅ Long-term booking generated for Unit: {$longTermUnit->name}");
//         }
//     }
// }
