<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TechnologyController extends Controller
{
    public function create(){
        return view('admin.technology.create');
    }
    
    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'name'=>['required','string'],
            'slug'=>['required','string','unique:technologies,slug'],
        ]);
        
        if($validator->passes()){
            $technology = Technology::create([
                'name' => $request->name,
                'slug'=> $request->slug
            ]);
    
            $technologies = Technology::get();
            return response()->json([
                'status'=>true,
                'data'=> $technologies,
                'id' => $technology->id,
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'error'=> $validator->errors(),
            ]);
        }
    }
    public function getSlug(Request $request){
        $slug = "";
        if(!empty($request->name)){
            $slug = Str::slug($request->name);
        }
        return response()->json([
            'status'=>true,
            'slug' => $slug
        ]);
    }
}
