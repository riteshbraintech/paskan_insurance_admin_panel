<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    private $excludeIds = [42, 33, 51];
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $perPage = $request->perPage ?? 50;
        $search = $request->search ?? "";
        $isAjax = $request->method;
        $status = $request->status ?? '';
        $roleId = $request->role ?? "";

        $cus_query = Admin::query()->testfilter();
        
        // if manager login only thier team staff will show else admin all staff
        if (admin()->check()) {

            if (admin()->user()->role_id == Role::STAFF){
                return redirect()->back()->with('error', 'Not allowed');
            }

            if (admin()->user()->role_id == Role::MANAGER) {
                $managerId =  admin()->user()->id;
                $cus_query = $cus_query->where("created_by", $managerId);
            }
        }

        if ($roleId){
            $cus_query = $cus_query->where("role_id","=",$roleId); 
        }else{
            $cus_query = $cus_query->where('role_id', '!=', Admin::SUPERADMIN);
        }
        
        if ($search){
            $cus_query = $cus_query->whereRaw(" ( name like '%".$search."%' OR email like '%".$search."%' OR gender like '%".$search."%' OR role_id = '$search')");
        } 

        if ($status){
            $cus_query = $cus_query->where('status',$status);    
        } 
        
        $staffList = $cus_query->orderBy("status", "asc")->orderBy("name","asc")->paginate($perPage); 
        
        $role = Role::select('id','role_name','role_slug')->get();

        if (!empty($isAjax)) {
            $html = view('admin.staff.table', compact('staffList'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.staff.index', compact('staffList', 'role'));
        }
    }

    public function create()
    {
        $role = Role::select('role_name','role_slug')->get();
        $manager = Admin::select('id','name')->testfilter()->where("role_id", Role::MANAGER)->get();
        return view('admin.staff.create', compact('role', 'manager'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:admins,email',
            'user_name' => 'required|unique:admins,user_name',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
            'name' => 'required|regex:/^[\p{L}\'\-\. ]+$/u',
            'gender' => ['required'],
            'role_id' => ['required'],
            'qtrly_target' => ['required_if:role_id,==,manager','nullable','numeric'],
            'minimum_accepted_qtrly_target' => ['required_if:role_id,==,manager','nullable','numeric','lte:qtrly_target'],
            'date_of_birth' => 'nullable|date|before_or_equal:' . Carbon::today(), 
            'phone' => 'nullable|digits:10',   
        ]);

        try {
            $req = $request->only('name','user_name', 'email', 'date_of_birth', 'dial_code', 'gender', 'description', 'phone','qtrly_target','minimum_accepted_qtrly_target');
            
            $req['password'] = Hash::make($request->password);
            $req['is_test'] = $request->has('is_test') && !empty($request->is_test) ? $request->is_test : admin()->user()->is_test;

            $filePath = config('static.uplaodPath.profile_image_path');

            if ($request->has('image')) {
                $image = $request->file('image');
                $imageName = rand() . '.' . $image->extension();
                $image->move($filePath, $imageName);
                $req['image'] = $imageName;
            }

            if ($request->has("role_id")){
                $req['role_id'] = $request->role_id;
            }else{
                $req['role_id'] = Role::STAFF;
            }

            if ($request->has("created_by")){
                $req['created_by'] = $request->created_by;
            }else{
                $req['created_by'] = admin()->id();
            } 

            Admin::create($req);

            return redirect()->route('admin.staff.list')->with('success', 'Staff Account Created');
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $role = Role::select('role_name','role_slug')->get();
        $staffInfo = Admin::select('id','name','email','user_name','date_of_birth','gender','dial_code','phone','image','role_id','created_by','qtrly_target','minimum_accepted_qtrly_target','is_test','description','status','check')->find($id);
        $manager = Admin::select('id','name')->testfilter()->where("role_id", Role::MANAGER)->get();
        return view('admin.staff.edit', compact('staffInfo', 'role', 'manager'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:admins,email,' . $id,
            'user_name' => 'required|unique:admins,user_name,' . $id,
            'password' => 'confirmed',
            'role_id' => ['required'],
            'qtrly_target' => ['required_if:role_id,==,manager','nullable','numeric'],
            'minimum_accepted_qtrly_target' => ['required_if:role_id,==,manager','nullable','numeric','lte:qtrly_target'],
            'date_of_birth'=> ['nullable', 'date','before_or_equal:'.Carbon::today()],
            'phone'=>'nullable|digits:10',
            'check' => 'nullable', 
        ]);

        try {
            $chk= $request->has('check') ? 1: 0; 
            $req = $request->only('name', 'qtrly_target', 'minimum_accepted_qtrly_target', 'user_name','email', 'date_of_birth', 'gender', 'dial_code', 'address', 'postcode', 'country', 'state', 'description', 'phone','is_test');
            $req['check'] = $chk;
            $filePath = config('static.uplaodPath.profile_image_path');
            
            if ($request->has('image')) {

                $image = $request->file('image');

                $imageName = rand() . '.' . $image->extension();

                $image->move($filePath, $imageName);
                $req['image'] = $imageName;
            }

            if ($request->has("role_id")){
                $req['role_id'] = $request->role_id;
            }else{
                $req['role_id'] = Role::STAFF;
            }

            if($request->has("password") && !empty($request->password)){
                $req['password'] = Hash::make($request->password);
            }

            if ($request->has("created_by") && $request->created_by && $req['role_id'] == Role::STAFF){
                $req['created_by'] = $request->created_by;
            }else{
                $req['created_by'] = admin()->id();
            }

            Admin::where('id', $id)->update($req);

            return redirect()->back()->with('success', 'Staff Account Updated');
        } catch (\Throwable $th) {
            Log::critical($th);      //write log here
            return redirect()->back()->with('error', $th);
        }
    }
    public function destroy($id)
    {
        try {
            Admin::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Staff Removed');
        } catch (\Throwable $th) {
            Log::critical($th);      //write log here
            return redirect()->back()->with('error', $th);
        }
    }
    public function changeStatus(Request $request){
        $admin = Admin::findOrFail($request->id);
        $status = $admin->status == "active" ? "inactive" : "active";
        $admin->status = $status;
        $admin->save();
        return response()->json(['status' => $status]);
    }

    public function staffList(Request $request){
        $manager = Admin::testfilter()
                ->where('role_id',Role::MANAGER)
                ->where('status' ,'active')
                ->whereNotIn('id', $this->excludeIds)
                ->orderBy('name', 'asc');

        $staff_list = Admin::testfilter()
                ->where('role_id' , Role::STAFF)
                ->where('status','active')
                ->whereNotIn('id', $this->excludeIds)
                ->orderBy('name', 'asc');
        
        $staff_dd = collect();

        if($request->filled('id')){
            $manager =  $manager->where('id',$request->id)->first();
            
            if ($manager) {
                $staff_dd->push($manager); // Add manager
                $staff_list = $staff_list->where('created_by', $manager->id)->get();
                $staff_dd = $staff_dd->merge($staff_list); // Add staff under manager
            }
        }else{
            $manager_list = $manager->get();
            $staff_list = $staff_list->get();
            $staff_dd = $manager_list->merge($staff_list);
        }

        return response()->json([
            'status'=>true,
            'detail' => $staff_dd,
        ]);

    }
}
