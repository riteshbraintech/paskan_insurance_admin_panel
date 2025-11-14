<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;

class ProfileControler extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile fetched successfully.',
            'data' => $user,
        ], 200);
    }


    public function updateProfile(ProfileUpdateRequest $request)
    {
        $user = $request->user();

       
        $validated = $request->validated();

        // Handle image upload
        if ($request->filled('image')) {
            $tempPath = public_path('tempfiles/' . $request->image);
            $destinationPath = public_path('user/images/' . $request->image);
            if (file_exists($tempPath)) {
                // Move the file
                if (@rename($tempPath, $destinationPath)) {
                    $validated['image'] = $request->image;
                }
            }
        }


        // Update user profile
        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'data'    => [
                'user'  => base64_encode(json_encode($user)),
                'user2'  => $user,
            ],
        ], 200);
    }
}
