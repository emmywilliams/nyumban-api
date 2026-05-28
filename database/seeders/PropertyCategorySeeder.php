<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyTypes = [
            ['name' => 'Residential', 'slug' => 'residential', 'type' => 'property_type', 'icon' => 'home'],
            ['name' => 'Commercial', 'slug' => 'commercial', 'type' => 'property_type', 'icon' => 'business'],
            ['name' => 'Hotel', 'slug' => 'hotel', 'type' => 'property_type', 'icon' => 'hotel'],
            ['name' => 'Hostel', 'slug' => 'hostel', 'type' => 'property_type', 'icon' => 'school'],
            ['name' => 'Airbnb', 'slug' => 'airbnb', 'type' => 'property_type', 'icon' => 'vpn_key'],
        ];

        foreach ($propertyTypes as $type) {
            \App\Models\Category::updateOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
