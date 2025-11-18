<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\BannerResource;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Banner;
use App\Models\Categoryformfield;
use App\Models\CMSPage;
use App\Http\Resources\Api\v1\CategoryFieldResource;



class HomeController extends Controller
{
    // create home function and reposen as json for api
    public function home(Request $request)
    {
        $lang = $request->lang;

        // You may tweak queries: eager load, caching, per-page limits, etc.
        // 1️⃣ Get top 4 categories
        $topcategories = Category::with('translation')
            ->active()
            ->orderBy('created_at', 'asc') // or by any other priority column
            ->limit(4)
            ->get();

        // 2️⃣ Get 4 random categories excluding top ones
        $randomCategories = Category::with('translation')
            ->active()
            ->whereNotIn('id', $topcategories->pluck('id')) // exclude top 4
            ->inRandomOrder()
            ->limit(4)
            ->get();

            
        $banners = Banner::with('translation')->where('is_active',1)->orderby('sort_order','asc')->get();
         // Fetch banners as needed
        $testimonials = []; // Fetch testimonials as needed
        $insurances = []; // Fetch insurances as needed

        
        $payload = [
            'banners' => BannerResource::collection($banners),
            'topcategories' => CategoryResource::collection($topcategories),
            'categories' => CategoryResource::collection($randomCategories),
            'testimonials' => $testimonials,
            'insurances' => $insurances,
        ];


        return response()->json(
            [
                'status' => 'success',
                'message' => 'Home data fetched successfully.',
                'data' => $payload,
            ],
            200
        );

    }


    // create home function and reposen as json for api
    public function headerMenu(Request $request)
    {
        $lang = $request->lang;

        // 3️⃣ Get all active categories if needed
        $allCategories = Category::with('translation')
            ->active()
            ->get();

        $payload = [
            'allCategories' => CategoryResource::collection($allCategories),
        ];

        return response()->json(
            [
                'status' => 'success',
                'message' => 'headerMenu data fetched successfully.',
                'data' => $payload,
            ],
            200
        );

    }


    // global single file upload function
    public function uploadSingleFiles(Request $request)
    {
        $filename = null;

        // Handle image upload
        if ($request->hasFile('document')) {
            $filename = time() . '.' . $request->document->extension();
            $request->document->move(public_path('tempfiles'), $filename);
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'File upload endpoint.',
                'data' => [
                    'file_name' => $filename,
                    'file_path' => url('tempfiles/' . $filename),
                ]
            ]
        );
    }

    // global multiple file upload function
    public function uploadMultipleFiles(Request $request)
    {
        $fileNames = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('tempfiles'), $filename);
                $fileNames[] = [
                    'file_name' => $filename,
                    'file_path' => url('tempfiles/' . $filename),
                ];
            }
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Multiple file upload endpoint.',
                'data' => $fileNames,
            ]
        );
    }

    // get CMS page by slug
    public function getCMSPage($slug)
    {
        $cmsPage = CMSPage::with('translation')->where('page_slug', $slug)->where('is_published', 1)->first();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'CMS page fetched successfully.',
                'data' => $cmsPage ?? null,
            ],
            200
        );

    }


    //get Category Form Fields By slug
    // public function categoryfield($slug){
    //     $category = Category::where('slug', $slug)->where('is_active', 1)->first();
    //     $formFields = Categoryformfield::with('translation')->where('category_id', $category->id)->orderBy('sort_order', 'asc')->get();
        
    //     return response()->json(
    //         [
    //             'status' => 'success',
    //             'message' => 'Form Fields fetched successfully.',
    //             'data' => $formFields ?? null,
    //         ],
    //         200
    //     );
    // }

    public function categoryfield($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        $formFields = Categoryformfield::with('translation')
            ->where('category_id', $category->id)
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Form Fields fetched successfully.',
            'data'    => CategoryFieldResource::collection($formFields),
        ]);
    }



    //get Banner Part
    // public function banner(){
    //     $bannerinfo=Banner::with('translation')->where('is_active',1)->orderby('sort_order','asc')->get();

    //     return response()->json(
    //         [
    //             'status' => 'success',
    //             'message' => 'Banners fetched successfully.',
    //             'data' => $bannerinfo ?? null,
    //         ],
    //         200
    //     );
    // }


    public function show($id)
    {
        // Load field + translations
        $field=Categoryformfield::with('translations')->findOrFail($id);

        // Primary English options (values)
        $primaryOptions = json_decode($field->options, true) ?? [];

        // Images (shared)
        $images = json_decode($field->images, true) ?? [];

        // Translation options (labels)
        $translatedOptions = json_decode(optional($field->translations->first())->options, true)
                            ?? $primaryOptions;

        // Build final array
        $output = [];
        foreach ($primaryOptions as $index => $val) {
            $output[] = [
                "label" => $translatedOptions[$index] ?? $val,
                "value" => $val,
                "image" => isset($images[$index]) 
                            ? asset($images[$index])
                            : null
            ];
        }

        return response()->json([
            "field_id" => $field->id,
            "type"     => $field->type,
            "options"  => $output
        ]);
    }

}
