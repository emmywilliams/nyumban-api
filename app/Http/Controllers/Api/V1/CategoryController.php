<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of all unit types and amenities.
     */
    public function index()
    {
        $categories = Category::select('id', 'name', 'slug', 'type', 'icon')
            ->orderBy('type', 'desc') // Groups unit_types together, then amenities
            ->get();

        return response()->json([
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }
}
