<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileControler extends Controller
{
    public function profile(Request $request)
    {
        $user=$request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication token is missing or invalid.',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile fetched successfully.',
            'data' => $user,
        ], 200);
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication token is missing or invalid.',
            ], 401);
        }


        // Validate input with custom messages
        $validator = Validator::make($request->all(), [
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'phone'       => 'nullable|string|max:20',
            'gender'      => 'nullable|in:male,female,other',
            'dob'         => 'nullable|date',
            'address'     => 'nullable|string|max:500',
            'postcode'    => 'nullable|string|max:20',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'email.unique'   => 'This email is already registered with another account.',
            'image.image'    => 'Profile image must be a valid image file.',
            'image.mimes'    => 'Only JPG, JPEG, and PNG formats are allowed.',
            'image.max'      => 'Profile image must not exceed 2MB in size.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle image upload if provided
        if ($request->hasFile('image')) {

            //  Delete old image if exists
            if ($user->image && file_exists(public_path('user/images/' . $user->image))) {
                @unlink(public_path('user/images/' . $user->image));
            }

            $filename = time() . '.' . $request->image->extension();
            $request->image->move(public_path('user/images'), $filename);
            $validated['image'] = $filename;
        }

        // Update user data
        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user,
        ], 200);
    }
}
