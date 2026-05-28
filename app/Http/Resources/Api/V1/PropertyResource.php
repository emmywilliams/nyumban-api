<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 🟢 Grab the authenticated tenant via sanctum middleware
        $user = $request->user('sanctum');

        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'slug' => \Str::slug($this->title),

            'is_favorite' => $user ? ($this->is_favorite ?? $user->favoriteProperties()->where('properties.id', $this->id)->exists()) : false,

            'units_count' => $this->units_count ?? $this->whenLoaded('units', fn() => $this->units->count(), 0),

            'description' => $this->when($request->routeIs('*.show'), $this->description, \Str::limit($this->description, 100)),

            'address' => $this->address,
            // Human-readable location names
            'location' => [
                'district'   => $this->district?->name,
                'county'     => $this->county?->name,
                'sub_county' => $this->subCounty?->name,
                'parish'     => $this->parish?->name,
                'village'    => $this->village?->name,
            ],
            // Raw
            'location_ids' => [
                'district_id'   => $this->district_id,
                'county_id'     => $this->county_id,
                'sub_county_id' => $this->sub_county_id,
                'parish_id'     => $this->parish_id,
                'village_id'    => $this->village_id,
            ],


            'meta' => [
                'is_gated' => (bool) $this->is_gated,
                'is_multi_unit' => (bool) $this->is_multi_unit,
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ],


            'units' => UnitResource::collection($this->whenLoaded('units')),

            'landlord' => $this->whenLoaded('landlord', function () {
                return [
                    'name' => $this->landlord->name,
                    'phone' => $this->landlord->phone,
                    'avatar' => $this->landlord->avatar ? asset('storage/' . $this->landlord->avatar) : null,
                ];
            }),

            'categories' => CategoryResource::collection($this->whenLoaded('categories')),

            'images' => $this->media->map(fn($m) => [
                'url' => asset('storage/' . $m->file_path),
                'is_featured' => (bool) $m->is_primary,
            ])->values(),

            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
