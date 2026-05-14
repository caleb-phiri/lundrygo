<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Update last login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
            
            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'rider') {
                return redirect()->intended(route('rider.dashboard'));
            }
            
            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
    
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }
    
    public function sendResetLink(Request $request)
    {
        // Implement password reset logic
        return back()->with('status', 'Password reset link sent!');
    }
    
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }
    
    public function reset(Request $request)
    {
        // Implement password reset logic
        return redirect()->route('login')->with('status', 'Password reset successful!');
    }
}