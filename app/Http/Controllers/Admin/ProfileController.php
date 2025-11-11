<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use Auth;
use App\Models\Admin;
use App\Models\Role;

use App\Http\Requests\Admin\UpdateProfilerequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use Illuminate\Database\Eloquent\Builder;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = admin()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(UpdateProfilerequest $request)
    {
        try {
            $adminid = admin()->id();
            $req = $request->only(["name", "date_of_birth", "gender", "phone"]);
            $filePath = config('static.uplaodPath.profile_image_path');
            if ($request->has('image')) {
                $image = $request->file('image');
                $imageName = rand() . '.' . $image->extension();
                $image->move($filePath, $imageName);
                $req['image'] = $imageName;
            }

            Admin::where('id', $adminid)->update($req);
            return redirect()->back()->with('success', 'Profile Updated');
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());      //write log here
            return redirect()->back()->with('error', 'Some issue reaised');
        }
    }

    public function displaypassword(){
        return view('admin.updatepassword');
    }
    
    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $adminid = admin()->id();
            $admin =  Admin::findOrFail($adminid);
            $admin->password = Hash::make($request->password);
            $admin->save();

            Auth::guard('admin')->login($admin);
            return redirect()->back()->with('success', 'Password Updated');
        } catch (\Throwable $th) {
            \Log::critical($th->getMessage());      //write log here
            return redirect()->back()->with('error', 'Some issue occur');
        }
    }
}
