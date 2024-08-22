<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        $user = User::where('email', '=', $data['email'])->first();
        if (!$user || !\Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'invalid user',
            ], 401);
        }
        $token = $user->createToken('Auth');
        return response()->json([
            'access_token' => $token->accessToken,
            'user'  => $user,
            'refresh_token' => $token
        ], 200);
    }
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_token' => Str::random(64),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('AuthToken')->accessToken;

        return response()->json([
            'message' => 'User registered successfully. Please check your email for verification.',
            'access_token' => $token,
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = User::where('verification_token', $request->token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification token'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $user->markEmailAsVerified();
        $user->verification_token = null;
        $user->save();

        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

          // Debug: Log the entire request
    Log::info('Request:', $request->all());

    // Debug: Log the authorization header
    Log::info('Authorization Header:', [$request->header('Authorization')]);

    $user = auth('api')->user();

    // Debug: Log user information
    Log::info('User:', [$user]);

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
    
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }
    
        $user->verification_token = Str::random(64);
        $user->save();
    
        $user->sendEmailVerificationNotification();
    
        return response()->json(['message' => 'Verification link sent']);
    }
    public function deactivateAccount(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password is incorrect'], 403);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete the user
        $user->delete();

        return response()->json(['message' => 'Account deactivated successfully']);
    }

    public function reactivateAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::withTrashed()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->trashed()) {
            return response()->json(['message' => 'Account is already active'], 400);
        }

        $user->restore();

        $token = $user->createToken('Auth')->accessToken;

        return response()->json([
            'message' => 'Account reactivated successfully',
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function permanentlyDeleteAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'admin_password' => 'required|string',
        ]);

        $admin = $request->user();

        if (!Hash::check($request->admin_password, $admin->password)) {
            return response()->json(['message' => 'Admin password is incorrect'], 403);
        }

        $userToDelete = User::withTrashed()->where('email', $request->email)->first();

        if (!$userToDelete) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Revoke all tokens
        $userToDelete->tokens()->delete();

        // Permanently delete the user
        $userToDelete->forceDelete();

        return response()->json(['message' => 'Account permanently deleted']);
    }

    public function getDeactivatedUsers(Request $request)
    {
        // This method should only be accessible by admins
        if (!$request->user()->isAdmin() && !$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $deactivatedUsers = User::onlyTrashed()->get();

        return response()->json(['deactivated_users' => $deactivatedUsers]);
    }
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email'])
            : response()->json(['message' => 'Unable to send reset link'], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully'])
            : response()->json(['message' => 'Unable to reset password'], 400);
    }
    public function getProfile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $request->user()->id,
            'profile_picture' => 'nullable|image|max:1024', // max 1MB
        ]);

        $user = $request->user();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
            dd('$data');
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
}
