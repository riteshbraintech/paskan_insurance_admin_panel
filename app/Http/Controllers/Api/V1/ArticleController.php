<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    //get Articles List 
    public function articlelist()
    {
        $articles = Article::where('is_active', 1)->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Articles fetched successfully.',
            'data'    => ArticleResource::collection($articles)
        ], 200);
    }
}
