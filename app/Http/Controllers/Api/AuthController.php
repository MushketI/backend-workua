<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role_id' => 'required|integer|exists:roles,id|in:1,2',
            'phone' => 'required|regex:/^(\+380)\d{9}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => [
                    'code' => 422,
                    'message' => 'Ошибка данных, проверьте пожалуйста данные',
                ],
                'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        if ($request->role_id === 1) {
            Candidate::create([
                'user_id' => $user->id,
            ]);
        }

        if ($request->role_id === 2) {
            Employer::create([
                'user_id' => $user->id,
            ]);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'User registered successfully',
            ],
            'user' => $user->load('role'),
            'token' => $token,
        ], 200);
    }


    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => 'Invalid email',
                ]
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => 'Invalid password',
                ],
            ], 401);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'User login successfully',
            ],
            'user' => $user->load('role'),
            'token' => $token,
        ]);
    }



    public function logout(Request $request)
    {

        $user = $request->user();
        $user->currentAccessToken()->delete(); // Удалить токен

        return response()->json(['status' => [
            'code' => 200,
            'message' => 'User logged out successfully',
        ]], 200);
    }

    public function edit(Request $request, $id )
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'User not found'
                ]
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'User updated successfully',
            ],
            'user' => $user,
        ]);
    }

    // Метод для отправки ссылки для сброса пароля
    public function forgotPassword(Request $request)
    {
        // Валидация email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $response = Password::sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Check your email for password reset link'
                ],
            ], 200);
        }

        return response()->json([
            'status' => [
                'code' => 400,
                'message' => 'Ошибка при отправке ссылки для сброса пароля'
            ],
        ], 400);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'User not found'
                ]], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => [
                'code' => 200,
                'message' => 'Good',
            ]
        ], 200);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = auth('sanctum')->user();

        if ($request->hasFile('image')) {

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');

            $imageUrl = Storage::disk('public')->url($imagePath);

            $user->image = $imageUrl;
            $user->save();
        }

        return response()->json([
            'message' => 'Аватар загружен',
            'image' => $imageUrl,
        ]);
    }

}
