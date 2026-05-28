<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PropertyResource;
use Illuminate\Http\Request;
use App\Models\Property;


class FavoriteController extends Controller
{
    /**
     * Display a listing of the tenant's favorite properties.
     */
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favoriteProperties()
            ->with([
                'district',
                'county',
                'subCounty',
                'parish',
                'village',
                'categories',
                'media',
            ])
            ->paginate(15);

        return PropertyResource::collection($favorites);
    }


    /**
     * Toggle item favorite status configuration.
     */

    public function toggle(Request $request)
    {
        $request->validate([
            'property_uuid' => 'required|exists:properties,uuid',
        ]);

        $property = Property::where('uuid', $request->property_uuid)->firstOrFail();

        // Performs sleek automated attachment/detachment actions inside your pivot node
        $status = $request->user()->favoriteProperties()->toggle($property->id);
        $isFavorite = count($status['attached']) > 0;

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Added to favorites' : 'Removed from favorites'
        ]);
    }
}
