<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Http\Resources\Api\V1\UnitResource;
use Illuminate\Http\Request;
use App\Models\Unit;
// use App\Http\Resources\Api\V1\UnitResource;

class UnitController extends Controller
{
    /**
     * LIST ALL UNITS for a specific property
     * GET /v1/properties/{property}/units
     */
    public function index(Property $property)
    {
        // Load categories/amenities to avoid N+1 database queries
        $units = $property->units()->with('categories')->get();

        return UnitResource::collection($units);
    }

    /**
     * SHOW A SINGLE UNIT
     * GET /v1/properties/{property}/units/{unit}
     */
    public function show(Property $property, Unit $unit)
    {
        // Ensure the unit actually belongs to this property
        if ($unit->property_id !== $property->id) {
            return response()->json(['message' => 'Unit not found for this property'], 404);
        }

        return new UnitResource($unit->load('categories'));
    }

    /**
     * STORE A NEW UNIT
     */
    public function store(Request $request, Property $property)
    {
        // 1. Security Check
        if ($request->user()->id !== $property->landlord_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Validate Unit Details
        $validated = $request->validate([
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'name'             => 'nullable|string |max:100',
            'price'            => 'required|numeric|min:0',
            'bedrooms'         => 'required|integer|min:0',
            'bathrooms'        => 'required|integer|min:0',
            'size_sqm'         => 'nullable|integer',
            'is_available'     => 'boolean',
        ]);

        // 3. Create the Unit
        $unit = $property->units()->create($validated);

        if ($request->has('category_ids')) {
            $unit->categories()->sync($request->category_ids);
        }

        return (new UnitResource($unit->load('categories')))
            ->additional(['message' => 'Unit added to ' . $property->title]);
    }

    /**
     * UPDATE UNIT DETAILS
     */
    public function update(Request $request, Property $property, Unit $unit)
    {
        // 1. Security: Check Ownership & Belonging
        if ($request->user()->id !== $property->landlord_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($unit->property_id !== $property->id) {
            return response()->json(['message' => 'Unit does not belong to this property'], 404);
        }

        // 2. Validate (using 'sometimes' so they can update just one field)
        $validated = $request->validate([
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'name'         => 'sometimes|string|max:100',
            'price'        => 'sometimes|numeric|min:0',
            'bedrooms'     => 'sometimes|integer|min:0',
            'bathrooms'    => 'sometimes|integer|min:0',
            'size_sqm'     => 'sometimes|nullable|integer',
            'is_available' => 'sometimes|boolean',
        ]);

        // 3. Perform Update
        $unit->update($validated);

        if ($request->has('category_ids')) {
            $unit->categories()->sync($request->category_ids);
        }

        return (new UnitResource($unit->load('categories')))
            ->additional(
                ['message' => 'Unit updated successfully for ' . $property->title]
            );
    }

    /**
     * DELETE A UNIT
     * DELETE /v1/properties/{property}/units/{unit}
     */
    public function destroy(Request $request, Property $property, Unit $unit)
    {
        if ($request->user()->id !== $property->landlord_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($unit->property_id !== $property->id) {
            return response()->json(['message' => 'Unit mismatch'], 404);
        }

        $unit->delete();

        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
