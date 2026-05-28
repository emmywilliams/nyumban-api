<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\Parish;
use App\Models\Village;

class LocationController extends Controller
{
   public function index()
    {
        $districts = District::select('id', 'name')->orderBy('name')->get();
        return response()->json(['data' => $districts]);
    }

    public function getDistricts()
    {
        $districts = District::select('id', 'name')->orderBy('name')->get();
        return response()->json(['data' => $districts]);
    }

    public function getCounties(District $district)
    {
        $counties = County::where('district_id', $district->id)->select('id', 'name')->orderBy('name')->get();
        return response()->json(['data' => $counties]);
    }

    public function getSubCounties(County $county)
    {
        $subCounties = SubCounty::where('county_id', $county->id)->select('id', 'name')->orderBy('name')->get();
        return response()->json(['data' => $subCounties]);
    }

    public function getParishes(SubCounty $subCounty)
    {
        $parishes = Parish::where('sub_county_id', $subCounty->id)->select('id', 'name')->orderBy('name')->get();
        return response()->json(['data' => $parishes]);
    }

    public function getVillages(Parish $parish)
    {
        $villages = Village::where('parish_id', $parish->id)->select('id', 'name')->orderBy('name')->get();
        return response()->json(['data' => $villages]);
    }
}
