<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Http\Resources\Api\V1\PropertyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        // 1. Safely grab token context from current request
        $user = $request->user('sanctum');

        $query = Property::query()
            ->with(['district', 'county', 'subCounty', 'parish', 'village', 'media', 'categories'])
            ->withCount('units');


        $query->where('status', 'active');

        // Filter by Category 
        $query->when($request->has('category_id'), function ($q) use ($request) {
            $q->whereHas('categories', function ($subQ) use ($request) {
                $subQ->where('categories.id', $request->category_id);
            });
        });

        // Filter by District
        $query->when($request->has('district_id'), function ($q) use ($request) {
            $q->where('district_id', $request->district_id);
        });

        // Filter by Gated Community
        $query->when($request->has('is_gated'), function ($q) use ($request) {
            $q->where('is_gated', $request->is_gated);
        });

        // Search by Title/Address
        $query->when($request->has('search'), function ($q) use ($request) {
            $searchTerm = '%' . $request->search . '%';
            $q->where(function ($subQ) use ($searchTerm) {
                $subQ->where('title', 'like', $searchTerm)
                    ->orWhere('address', 'like', $searchTerm);
            });
        });

        // 🟢 FIX: Single pagination statement execution block
        $properties = $query->latest()->paginate(15);

        // 🟢 Robust boolean calculation injection
        $properties->getCollection()->transform(function ($property) use ($user) {
            $property->is_favorite = $user ? $user->favoriteProperties()->where('properties.id', $property->id)->exists() : false;
            return $property;
        });

        return PropertyResource::collection($properties);
    }


    public function myProperties(Request $request)
    {
        $user = $request->user();

        $properties = $request->user()
            ->properties()
            ->with(['district', 'county', 'subCounty', 'parish', 'village', 'media', 'categories'])
            ->withCount('units')

            ->latest()
            ->paginate(15);

        // 🟢 Map consistently for user inventory queries too
        $properties->getCollection()->transform(function ($property) use ($user) {
            $property->is_favorite = $user ? $user->favoriteProperties()->where('properties.id', $property->id)->exists() : false;
            return $property;
        });

        return PropertyResource::collection($properties);
    }


    // Load everything needed for a deep dive into the property details page
    public function show(Request $request, Property $property)
    {
        $user = $request->user('sanctum');

        $property->load([
            'district',
            'county',
            'subCounty',
            'parish',
            'village',
            'media',
            'landlord:id,name,phone,avatar',
            'units.categories'
        ]);
        $startDate = null;
        $endDate = null;

        $rawStart = $request->query('start_date');
        $rawEnd = $request->query('end_date');

        $units = $property->units()->with('categories')->get();

        if ($rawStart && $rawEnd) {
            try {
                $startDate = \Carbon\Carbon::parse($rawStart)->toDateString();
                $endDate = \Carbon\Carbon::parse($rawEnd)->toDateString();

                $units->each(function ($unit) use ($startDate, $endDate) {
                    if ($unit->status === 'occupied') {
                        return;
                    }

                    // Check for overlapping active schedules
                    $hasOverlap = $unit->bookings()
                        ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                        ->where('start_date', '<', $endDate)
                        ->where('end_date', '>', $startDate)
                        ->exists();

                    if ($hasOverlap) {
                        $unit->status = 'occupied';
                    }
                });
            } catch (\Exception $e) {
                Log::error("Date parsing mismatch context: " . $e->getMessage());
            }
        }

        // Set the modified collection back onto the property model instance
        $property->setRelation('units', $units);

        // 🟢 Inject into individual resource model instances on detail lookups
        $property->is_favorite = $user ? $user->favoriteProperties()->where('properties.id', $property->id)->exists() : false;

        return new PropertyResource($property);
    }

    // Create properties
    public function store(Request $request)
    {
        // 1. Validate the input
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'address'       => 'required|string',
            'district_id'   => 'required|exists:districts,id',
            'county_id'     => 'required|exists:counties,id',
            'sub_county_id' => 'required|exists:sub_counties,id',
            'parish_id'     => 'required|exists:parishes,id',
            'village_id'    => 'required|exists:villages,id',
            'is_gated'      => 'boolean',
            'is_multi_unit' => 'boolean',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        // 2. Use a Transaction to ensure everything saves or nothing saves
        return DB::transaction(function () use ($validated, $request) {

            // 3. Create the property (UUID is handled by the model's booted method)
            $property = $request->user()->properties()->create(array_merge($validated, [
                'status' => 'active' // Or 'pending' if you want to verify listings first
            ]));

            // Sync Categories (Commercial, Residential, etc.)
            if ($request->has('category_ids')) {
                $property->categories()->sync($request->category_ids);
            }

            // 4. Handle image upload if provided
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('properties', 'public');
                $property->media()->create([
                    'file_path' => $path,
                    'file_type' => $request->file('image')->getClientMimeType(),
                    'is_primary' => true
                ]);
            }

            // 5. Return the newly created property using your Resource
            return new PropertyResource($property->load(['district', 'county', 'subCounty', 'parish', 'village', 'media', 'categories']));
        });

        // if ($request->has('category_ids')) {
        //     $property->categories()->sync($request->category_ids);
        // }
    }

    // Edit property details.
    public function update(Request $request, Property $property)
    {
        // 1. Security Check: Only the owner can edit
        if ($request->user()->id !== $property->landlord_id) {
            return response()->json(['message' => 'Unauthorized. You do not own this property.'], 403);
        }

        // 2. Validation (Make fields optional using 'sometimes')
        $validated = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'description'   => 'sometimes|string',
            'address'       => 'sometimes|string',
            'district_id'   => 'sometimes|exists:districts,id',
            'county_id'     => 'sometimes|exists:counties,id',
            'sub_county_id' => 'sometimes|exists:sub_counties,id',
            'parish_id'     => 'sometimes|exists:parishes,id',
            'village_id'    => 'sometimes|exists:villages,id',
            'is_gated'      => 'sometimes|boolean',
            'is_multi_unit' => 'sometimes|boolean',
            'status'        => 'sometimes|in:active,inactive,pending',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids'   => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        return DB::transaction(function () use ($validated, $request, $property) {

            // 3. Update the property
            $property->update($validated);

            // Sync Categories (Commercial, Residential, etc.)
            if ($request->has('category_ids')) {
                $property->categories()->sync($request->category_ids);
            }

            // 4. Handle image upload if provided
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('properties', 'public');
                $property->media()->create([
                    'file_path' => $path,
                    'file_type' => $request->file('image')->getClientMimeType(),
                    'is_primary' => true
                ]);
            }

            // 5. Return the updated version
            return new PropertyResource($property->load(['district', 'county', 'subCounty', 'parish', 'village', 'media', 'categories']));
        });
    }

    // Delete a property (soft delete)
    public function destroy(Request $request, Property $property)
    {
        // Security: Only owner can delete
        if ($request->user()->id !== $property->landlord_id) {
            return response()->json(['message' => 'Unauthorized. You do not own this property.'], 403);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }
}
