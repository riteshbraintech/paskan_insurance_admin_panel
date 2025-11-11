<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoryformfield;
use Illuminate\Http\Request;

class CategoryFormControler extends Controller
{
    public function index()
    {
        $categoryformfield = Categoryformfield::with('translations')->get();

        return response()->json([
            'status' => true,
            'message' => 'Category Form Field fetched successfully',
            'data' => $categoryformfield,
        ]);
    }

    public function categoryformfieldlist($label)
    {

        $categoryformfield = Categoryformfield::with('translations')->where('label', $label)
            ->first();

        if (!$categoryformfield) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Category Page fetched successfully',
            'data' => $categoryformfield,
        ], 200);
    }


    public function categoryfields($category_id){
        $categoryformfields = Categoryformfield::with('translations')->where('category_id', $category_id)
            ->get();

        if (!$categoryformfields) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Category Form Fields are fetched successfully',
            'data' => $categoryformfields,
        ], 200);
    }
}
