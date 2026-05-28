<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Unit Types (Dropdown items)
            ['name' => 'Studio', 'type' => 'unit_type', 'icon' => 'apartment'],
            ['name' => 'Office', 'type' => 'unit_type', 'icon' => 'work'],
            ['name' => 'Single room', 'type' => 'unit_type', 'icon' => 'home'],
            ['name' => 'Double room', 'type' => 'unit_type', 'icon' => 'house'],
            ['name' => 'Shop Space', 'type' => 'unit_type', 'icon' => 'store'],
            ['name' => 'Warehouse', 'type' => 'unit_type', 'icon' => 'warehouse'],
            ['name' => 'Self-Contained', 'type' => 'unit_type', 'icon' => 'door'],
            ['name' => 'Restaurant/Café', 'type' => 'unit_type', 'icon' => 'restaurant'],

            // Amenities (Checkbox items)
            ['name' => 'Free WiFi', 'type' => 'amenity', 'icon' => 'wifi'],
            ['name' => 'Yaka', 'type' => 'amenity', 'icon' => 'bolt'],
            ['name' => 'Inside Toilet', 'type' => 'amenity', 'icon' => 'wc'],
            ['name' => 'Water Tank/Tap', 'type' => 'amenity', 'icon' => 'water'],
            ['name' => 'Parking', 'type' => 'amenity', 'icon' => 'car'],
            ['name' => 'Security Guard', 'type' => 'amenity', 'icon' => 'security'],
            ['name' => 'Furnished', 'type' => 'amenity', 'icon' => 'chair'],
            ['name' => 'Paved Compound', 'type' => 'amenity', 'icon' => 'landscape'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'type' => $cat['type'],
                    'icon' => $cat['icon']
                ]
            );
        }
    }
}
