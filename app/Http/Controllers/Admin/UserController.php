<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\UserInsuranceFillup;
use Illuminate\Http\Request;

class UserController extends Controller
{
        public function index(Request $request)
        {
            $isAjax = $request->method ?? "";
            $perPage = $request->perPage ?? 10;
            $search = $request->search ?? '';
            $status = $request->status ?? '';

            $query = User::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%");
                });
            }

            if($status){
                $user = $query->where('status',$status);
            }
            $records = $query->sortable('id', 'desc')->paginate($perPage);
            if ($isAjax) {
                $html = view('admin.userpage.table', compact('records'))->render();
                return response()->json(['html' => $html]);
            }
            return view('admin.userpage.index', compact('records'));
        }


        public function create(Request $request){
            return view('admin.userpage.create');
        }

        public function store(Request $request)
        {
            $this->validate($request, [
                'name'           => 'required|string|min:2|max:100',
                'dob'            => 'required|date',
                'gender'         => 'required|in:male,female,other',
                'nationality'    => 'required|string|max:100',
                'marital_status' => 'required|in:single,married,divorced',
                'phone'         => 'required|digits_between:8,15',
                'address'        => 'required|string|min:5|max:255',
                'email'          => 'required|email|unique:users,email',
                'password'       => 'required|min:6|max:50',
                'id_number'      => 'required|string|max:50|unique:users,id_number',
            ]);

            User::create([
                'name'           => $request->name,
                'dob'            => $request->dob,
                'gender'         => $request->gender,
                'nationality'    => $request->nationality,
                'marital_status' => $request->marital_status,
                'phone'         => $request->phone,
                'address'        => $request->address,
                'email'          => $request->email,
                'password'       => bcrypt($request->password), 
                'id_number'      => $request->id_number,
            ]);

            return redirect()->route('admin.user.index')->with('success', 'User added successfully!');
        }


        public function edit(Request $request, $id){
            $record  = User::findOrFail($id);
            // dd($record);
            return view('admin.userpage.edit',compact('record'));
        }


        public function update(Request $request, $id)
        {
            $this->validate($request, [
                'name'           => 'required|string|min:2|max:100',
                'dob'            => 'required|date',
                'gender'         => 'required|in:male,female,other',
                'nationality'    => 'required|string|max:100',
                'marital_status' => 'required|in:single,married,divorced,widowed',
                'phone'          => 'required|digits_between:8,15',
                'address'        => 'required|string|min:5|max:255',
                'email'          => 'required|email|unique:users,email,' . $id,
                'id_number'      => 'required|string|max:50|unique:users,id_number,' . $id,
            ]);

            $user = User::findOrFail($id);
            // dd($user);
            $user->update([
                'name' => $request->name,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'marital_status' => $request->marital_status,
                'phone' => $request->phone,
                'address' => $request->address,
                'email' => $request->email,
                'id_number' => $request->id_number,
            ]);

            return redirect()->route('admin.user.index')->with('success', 'User updated successfully!');
        }


        public function delete($id){
            User::findOrFail($id)->delete();
            return redirect()->route('admin.user.index')->with('success', 'User Removed');
        }

        public function changeStatus($id)
        {
            $record = User::findOrFail($id);

            $record->is_active = $record->is_active == 1 ? 0 : 1;
            $record->save();
            $status = $record->is_active == 1 ? 'active' : 'inactive';
            return response()->json([
                'status' => $status,
                'success' => true,
                'message' => "status changed successfully.",
            ]);
        }

        public function view($id){
            $record  = User::findOrFail($id);
            return view('admin.userpage.view',compact('record'));
        }
        
        
        public function formfieldview(Request $request, $id)
        {
            $user = User::findOrFail($id);

            // Get all fillups for user, grouped by category
            $records = UserInsuranceFillup::with(['category', 'formField'])
                ->where('user_id', $id)
                ->get()
                ->groupBy('category_id');

            // Get distinct categories
            $categories = Category::whereIn('id', $records->keys())->get();

            return view('admin.userpage.formfieldview', compact('user','categories', 'records'));
        }


}
