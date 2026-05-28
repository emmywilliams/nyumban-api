<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Country
        $uganda = Country::create([
            'name' => 'Uganda',
            'iso_code' => 'UG'
        ]);

        // 2. Create Cities
        $kampala = City::create([
            'country_id' => $uganda->id,
            'name' => 'Kampala'
        ]);

        $entebbe = City::create([
            'country_id' => $uganda->id,
            'name' => 'Entebbe'
        ]);

        // 3. Create Districts for Kampala
        $districts = ['Central', 'Kawempe', 'Makindye', 'Nakawa', 'Rubaga'];

        foreach ($districts as $name) {
            District::create([
                'city_id' => $kampala->id,
                'name' => $name
            ]);
        }
    }
}
