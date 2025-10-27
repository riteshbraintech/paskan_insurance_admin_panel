<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Support\Str;



class RoleController extends Controller
{
    public function index(Request $request)
    {
        if(admin()->check()){
            if(admin()->user()->role_id == Role::STAFF || admin()->user()->role_id == Role::MANAGER) 
                return redirect()->back()->with('error', 'Not allowed');
        }
        
        $current_page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;
        $search = $request->search ?? '';
        $isAjax = $request->method;

        $cus_query = Role::select('role_name','id');
       
        if ($search) 
            $cus_query = $cus_query->where("role_name", "LIKE", "%{$search}%");

        $roles = $cus_query->paginate($perPage);

        if (!empty($isAjax)) {
            $html = view('admin.role.table', compact('roles'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.role.index', compact('roles'));
        }
    }

    public function create()
    {
        return view('admin.role.create');
    }

    public function store(RoleRequest $request)
    {
        try {
            Role::create([
                'role_name' => $request->role_name,
                'role_slug' => Str::slug($request->role_name),
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
        return redirect()->back()->with('success', 'Roles Created');
    }

    public function edit($id)
    {
        $roleInfo = Role::select('role_name','id')->find($id);
        return view('admin.role.edit', compact( 'roleInfo'));
    }

    public function update(RoleRequest $request, $id)
    {
        try{
            $this->validate($request, ['role_name' => 'required|unique:roles,role_name,' . $id]);
            Role::where('id', $id)->update([
                'role_name' => $request->role_name,
                'role_slug' => Str::slug($request->role_name),
            ]);
            return redirect()->back()->with('success', 'Roles Updated');
        } catch (\Throwable $th) {
            \Log::critical($th->getMessage());      //write log here 
            return redirect()->back()->with('error', 'Some issue occur');
        }    
    }

    public function destroy($id)
    {
        try {
            Role::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Role Removed');
        } catch (\Throwable $th) {
            \Log::critical($th->getMessage());      //write log here 
            return redirect()->back()->with('error', 'Some issue occur');
        }
    }
}
