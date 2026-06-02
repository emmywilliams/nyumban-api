<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\CategoryResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? 'Standard Unit',
            'price' => (float) $this->price,
            'price_formatted' => number_format($this->price) . ' UGX',
            'bedrooms' => (int) $this->bedrooms,
            'bathrooms' => (int) $this->bathrooms,
            'size_sqm' => $this->size_sqm ? (int) $this->size_sqm : null,
            'status' => $this->status ?? 'available',
            'stay_type' => $this->stay_type ?? 'long_term',

            // 1. The Main Type (Studio, Bedsitter, etc.)
            'type' => $this->categories->where('type', 'unit_type')->first()?->name ?? 'Apartment',

            // 2. The Amenities List (WiFi, Yaka, etc.)
            'amenities' => $this->categories->where('type', 'amenity')->pluck('name')->toArray(),

            // 3. Raw category data (helpful if Flutter needs the Icons or IDs)
            'features_raw' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
