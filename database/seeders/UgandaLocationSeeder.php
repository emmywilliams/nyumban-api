<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UgandaLocationSeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/uganda.json'));
        $data = json_decode($json, true);

        DB::beginTransaction();

        try {

            $districts = [];
            $counties = [];
            $subCounties = [];
            $parishes = [];

            foreach ($data as $item) {

                // ✅ DISTRICT
                $district = $item['district'];

                if (!isset($districts[$district['id']])) {
                    $districtId = DB::table('districts')->insertGetId([
                        'ug_id' => $district['id'],
                        'name' => $district['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $districts[$district['id']] = $districtId;
                }

                // ✅ COUNTY
                $county = $item['county'];

                if (!isset($counties[$county['id']])) {
                    $countyId = DB::table('counties')->insertGetId([
                        'ug_id' => $county['id'],
                        'name' => $county['name'],
                        'district_id' => $districts[$county['district']],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $counties[$county['id']] = $countyId;
                }

                // ✅ SUB COUNTY
                $subCounty = $item['subCounty'];

                if (!isset($subCounties[$subCounty['id']])) {
                    $subCountyId = DB::table('sub_counties')->insertGetId([
                        'ug_id' => $subCounty['id'],
                        'name' => $subCounty['name'],
                        'county_id' => $counties[$subCounty['county']],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $subCounties[$subCounty['id']] = $subCountyId;
                }

                // ✅ PARISH
                $parish = $item['parish'];

                if (!isset($parishes[$parish['id']])) {
                    $parishId = DB::table('parishes')->insertGetId([
                        'ug_id' => $parish['id'],
                        'name' => $parish['name'],
                        'sub_county_id' => $subCounties[$parish['subcounty']],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $parishes[$parish['id']] = $parishId;
                }

                // ✅ VILLAGES (no need to dedupe usually, but safe to insert directly)
                foreach ($item['villages'] as $village) {
                    DB::table('villages')->insert([
                        'ug_id' => $village['id'],
                        'name' => $village['name'],
                        'parish_id' => $parishes[$village['parish']],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
