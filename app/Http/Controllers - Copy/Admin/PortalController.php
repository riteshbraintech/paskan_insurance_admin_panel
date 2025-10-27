<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PortalController extends Controller
{
    public function create(){
        return view('admin.portal.create');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string'],
            'slug' => ['required','string','unique:portals,slug'],
        ]);
        if($validator->passes()){
            $portal = Portal::create([
                'name' => $request->name,
                'slug'=> $request->slug
            ]);

            $portals = Portal::get();
            return response()->json([
                'status' => true,
                'data' => $portals,
                'id'=> $portal->id,
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
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
