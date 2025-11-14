<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function faqlist(){
        $faqinfo=FAQ::with('translation')->where('is_published',1)->orderby('sort_order','asc')->get();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'FAQ List fetched successfully.',
                'data' => $faqinfo ?? null,
            ],
            200
        );
    }
}
