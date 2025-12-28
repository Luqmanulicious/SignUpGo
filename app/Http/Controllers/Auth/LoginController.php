<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Add remember me functionality
        $remember = $request->filled('remember');

        try {
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                // Get the authenticated user
                $user = Auth::user();
                
                // Log successful login
                Log::info('User logged in successfully', ['user_id' => $user->id, 'email' => $user->email]);
                
                return redirect()->intended('/dashboard');
            }

            // Log failed login attempt
            Log::warning('Failed login attempt', ['email' => $request->email]);
            
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'email' => $request->email
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ])->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}