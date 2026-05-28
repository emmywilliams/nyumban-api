<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the Admin Role first
        $adminRole = \App\Models\Role::create([
            'name' => 'admin',
            'description' => 'System Administrator',
        ]);

        // 2. Create other roles for Nyumban
        \App\Models\Role::create(['name' => 'landlord']);
        \App\Models\Role::create(['name' => 'tenant']);

        // 3. Now create your Admin User linked to that role
        \App\Models\User::factory()->create([
            'name' => 'Emmy William',
            'email' => 'emmywilliamkayanja@gmail.com',
            'role_id' => $adminRole->id, // Use the ID from the role we just created
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'phone' => '256755131352', // Add a dummy phone to satisfy your DB constraint
            'password' => bcrypt('William@1618'), // Set a default password
        ]);

        // 4. Seed Uganda Locations
        $this->call([
            // RoleSeeder::class,
            // UserSeeder::class,
            UgandaLocationSeeder::class,
        ]);
    }
}
