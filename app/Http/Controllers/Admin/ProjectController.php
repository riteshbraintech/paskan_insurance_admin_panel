<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request){
        
        $isAjax = $request->method ?? "";
        $perPage = $request->perPage ?? 10;
        $search = $request->search ?? '';
        $statusFilter = $request->statusFilter ?? '';

        $projects = Project::query()->select('id','app_name','technology','application_status','play_store_url','app_store_url','website_url','description');

        if($search){
            $projects = $projects->whereRaw("(app_name like '%".$search."%' or technology like  '%".$search."%' or application_status like '%".$search."%' or description like '%".$search."%')");
        }

        if($statusFilter){
            $projects = $projects->where('application_status',$statusFilter);
        }

        $projects = $projects->orderBy('id','desc')->paginate($perPage);

        if(!blank($isAjax)){
            $html = view('admin.project.table',compact('projects'))->render();
            return response()->json(["html"=>$html]);
        }
        return view('admin.project.index',compact('projects'));
    }

    public function create(Request $request){
        return view('admin.project.create');
    }

    public function store(Request $request){
        
        $this->validate($request,[
            'app_name'=>'required|min:2|max:50',
            'technology'=>'required',
            'play_store_url'=>'required|min:2|max:100',
            'app_store_url'=>'required|min:2|max:100',
            'website_url'=>'required|min:2|max:100',
            'status'=>'required',
            'description'=>'required|min:2|max:300',
        ]);

        Project::create([
            'app_name' => $request->app_name,
            'technology' => $request->technology,
            'play_store_url' => $request->play_store_url,
            'app_store_url' => $request->app_store_url,
            'website_url' => $request->website_url,
            'application_status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.project.index')->with('success','Project added !!');
    }

    public function edit(Request $request, $id){
        $project  = Project::select('id','app_name','technology','application_status','play_store_url','app_store_url','website_url','description')->findOrFail($id);
        return view('admin.project.edit',compact('project'));
    }

    public function update(Request $request, $id){
        $this->validate($request,[
            'app_name'=>'required|min:2|max:50',
            'technology'=>'required',
            'play_store_url'=>'required|min:2|max:100',
            'app_store_url'=>'required|min:2|max:100',
            'website_url'=>'required|min:2|max:100',
            'status'=>'required',
            'description'=>'required|min:2|max:300',
        ]);

        Project::findOrFail($id)->update([
            'app_name' => $request->app_name,
            'technology' => $request->technology,
            'play_store_url' => $request->play_store_url,
            'app_store_url' => $request->app_store_url,
            'website_url' => $request->website_url,
            'application_status' => $request->status,
            'description' => $request->description,
        ]);
        return redirect()->back()->with('success',"Project details updated !!");
    }
    public function destroy($id){
        Project::findOrFail($id)->delete();
        return redirect()->route('admin.project.index')->with('success', 'Project Removed');
    }
}
