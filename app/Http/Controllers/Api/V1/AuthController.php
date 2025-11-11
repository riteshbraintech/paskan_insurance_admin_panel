<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'required|string|min:6',
            'gender'      => 'nullable|in:male,female,other',
            'dob'         => 'nullable|date',
            'country_id'  => 'nullable|integer',
            'device_name' => 'nullable|string|max:100',
            'device_type' => 'nullable|string|max:100',
            'device_id'   => 'nullable|string|max:100',
            'firebase_token' => 'nullable|string|max:150',
        ], [
            // 👇 Custom error messages
            'name.required'          => 'Please enter your full name.',
            'email.required'         => 'Email address is required.',
            'email.email'            => 'Please enter a valid email address.',
            'email.unique'           => 'This email is already registered.',
            'password.required'      => 'Please create a password.',
            'password.min'           => 'Password must be at least 6 characters long.',
            'password.confirmed'     => 'Password confirmation does not match.',
            'gender.in'              => 'Gender must be male, female, or other.',
            'dob.date'               => 'Date of birth must be a valid date.',
            'country_id.integer'     => 'Country ID must be a valid integer.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'] ?? null,
            'gender'      => $validated['gender'] ?? 'male',
            'dob'         => $validated['dob'] ?? null,
            'country_id'  => $validated['country_id'] ?? null,
            'password'    => Hash::make($validated['password']),
            'is_active'   => 1,
            'status'      => 'active',
            'device_name' => $validated['device_name'] ?? null,
            'device_type' => $validated['device_type'] ?? null,
            'device_id'   => $validated['device_id'] ?? null,
            'firebase_token' => $validated['firebase_token'] ?? null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'data'    => [
                'user'  => $user,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_number'    => 'required|exists:users,id_number'
        ], [
            'id_number.required'    => 'Please enter your Id Number / Passport Number'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors occurred.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        //  2. Attempt login
        $user = User::where('id_number', $request->id_number)->first();

         // 2️⃣ Log in via session (creates Sanctum cookie)
        // Auth::login($user);
        // $request->session()->regenerate();

        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'Invalid email or password.',
        //     ], 401);
        // }

        // 3. Check if user is active
        // if ($user->is_active != 1 || $user->status != 'active') {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'Your account is not active. Please contact support.',
        //     ], 403);
        // }

        //  4. Create token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'  => base64_encode(json_encode($user)),
                'token' => $token,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        // Delete only the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }
}
