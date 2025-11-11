<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CMSPage;
use Illuminate\Http\Request;

class CMSPageControler extends Controller
{
    public function index()
    {
        $cmspages = CMSPage::with('translations')->where('is_published', 1)->get();

        return response()->json([
            'status' => true,
            'message' => 'CMS Pages fetched successfully',
            'data' => $cmspages
        ]);
    }

    public function cmspagedetail($slug)
    {
        $cmspage = CMSPage::with('translations')->where('page_slug', $slug)->where('is_published', 1)->first();

        if (!$cmspage) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'CMS Page fetched successfully',
            'data' => $cmspage
        ], 200);
    }

}
