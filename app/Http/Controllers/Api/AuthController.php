<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Rate limiting
        $key = 'register.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many registration attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'phone' => 'nullable|string|max:20|unique:users',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|max:2048',
            'terms_accepted' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            // Handle profile image upload
            $profileImage = null;
            if ($request->hasFile('profile_image')) {
                $profileImage = $request->file('profile_image')->store('profiles', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'profile_image' => $profileImage,
                'role' => 'customer',
                'email_verified_at' => null,
                'is_active' => true,
                'registration_ip' => $request->ip(),
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Create email verification token
            $user->sendEmailVerificationNotification();

            // Create Sanctum token
            $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

            RateLimiter::hit($key, 3600);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please verify your email.',
                'user' => $this->formatUserData($user),
                'token' => $token,
                'requires_verification' => true,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key);
            
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact support.'
            ], 403);
        }

        // Check email verification
        if (config('auth.require_email_verification', true) && !$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email address before logging in.',
                'requires_verification' => true,
                'email' => $user->email,
            ], 403);
        }

        // Revoke existing tokens if needed
        if ($request->revoke_existing ?? false) {
            $user->tokens()->delete();
        }

        // Create new token
        $tokenExpiry = $request->remember_me ? now()->addDays(30) : now()->addDays(7);
        $token = $user->createToken(
            $request->device_name ?? 'web_app',
            ['*'],
            $tokenExpiry
        )->plainTextToken;

        // Update last login information
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        RateLimiter::clear($key);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $this->formatUserData($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $tokenExpiry->toIso8601String(),
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();
            
            // Optionally revoke all tokens
            if ($request->all_devices) {
                $request->user()->tokens()->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed'
            ], 500);
        }
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Load relationships if needed
        if ($request->has('with')) {
            $with = explode(',', $request->with);
            $user->load($with);
        }
        
        return response()->json([
            'success' => true,
            'user' => $this->formatUserData($user),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|max:2048',
            'current_password' => 'required_if:password,exists|current_password',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Update fields
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($user->profile_image) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $user->profile_image = $request->file('profile_image')->store('profiles', 'public');
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $this->formatUserData($user),
        ]);
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        
        // Delete current token
        $user->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token', ['*'], now()->addDays(7))->plainTextToken;
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addDays(7)->toIso8601String(),
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Rate limiting
        $key = 'password_reset.' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many password reset attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }
        
        $status = Password::sendResetLink(
            $request->only('email')
        );
        
        RateLimiter::hit($key, 3600);
        
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unable to send reset link'
        ], 500);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()],
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
                
                // Revoke all tokens after password reset
                $user->tokens()->delete();
            }
        );
        
        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. Please login with your new password.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid token or email'
        ], 400);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }
        
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }
        
        $user->markEmailAsVerified();
        
        // Send welcome notification
        $this->notificationService->send(
            $user,
            'welcome',
            'Welcome to ' . config('app.name'),
            'Your email has been verified. Start using our services!',
            [],
            ['priority' => 'high']
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }
        
        // Rate limiting
        $key = 'email_verification.' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many resend attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }
        
        $user->sendEmailVerificationNotification();
        RateLimiter::hit($key, 3600);
        
        return response()->json([
            'success' => true,
            'message' => 'Verification link sent to your email'
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()],
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Revoke all tokens except current
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|current_password',
            'confirmation' => 'required|accepted',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Delete user data
        $user->tokens()->delete();
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    /**
     * Get active sessions
     */
    public function getSessions(Request $request)
    {
        $tokens = $request->user()->tokens()
            ->where('expires_at', '>', now())
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'device_name' => $token->name,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                    'expires_at' => $token->expires_at,
                    'is_current' => $token->id === $token->currentAccessToken()->id,
                ];
            });
        
        return response()->json([
            'success' => true,
            'sessions' => $tokens
        ]);
    }

    /**
     * Revoke a specific session
     */
    public function revokeSession(Request $request, $tokenId)
    {
        $token = $request->user()->tokens()->find($tokenId);
        
        if (!$token) {
            return response()->json(['message' => 'Session not found'], 404);
        }
        
        if ($token->id === $request->user()->currentAccessToken()->id) {
            return response()->json(['message' => 'Cannot revoke current session'], 400);
        }
        
        $token->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Session revoked successfully'
        ]);
    }

    /**
     * Format user data for API response
     */
    protected function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'role' => $user->role,
            'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
            'email_verified' => $user->hasVerifiedEmail(),
            'is_active' => $user->is_active,
            'created_at' => $user->created_at->toIso8601String(),
            'updated_at' => $user->updated_at->toIso8601String(),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
        ];
    }
}