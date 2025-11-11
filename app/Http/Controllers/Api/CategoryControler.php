<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryControler extends Controller
{
    public function index()
    {
        $category = Category::with('translations')->where('is_active', 1)->get();

        return response()->json([
            'status' => true,
            'message' => 'Category Pages fetched successfully',
            'data' => $category,
        ]);
    }

    public function categorydetail($slug)
    {

        $category = Category::with('translations')->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Category Page fetched successfully',
            'data' => $category,
        ], 200);
    }

}
