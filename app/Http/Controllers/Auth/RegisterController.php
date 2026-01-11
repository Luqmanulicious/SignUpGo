<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postcode' => ['nullable', 'string', 'max:10'],
            'website' => ['nullable', 'url', 'max:255'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
        ]);

        try {
            // First check if the email already exists
            if (User::where('email', $request->email)->exists()) {
                return back()->withInput()->withErrors(['email' => 'This email is already registered.']);
            }

            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'organization' => $request->organization,
                'address' => $request->address,
                'postcode' => $request->postcode,
                'website' => $request->website,
            ];

            // Handle certificate file upload
            if ($request->hasFile('certificate')) {
                $cloudinary = new CloudinaryService();
                $result = $cloudinary->upload($request->file('certificate'), 'certificates', [
                    'public_id' => 'certificate_temp_' . time(),
                    'tags' => ['certificate', 'registration'],
                    'resource_type' => 'raw',
                    'use_filename' => false,
                    'unique_filename' => false,
                ]);
                $userData['certificate_path'] = $result['secure_url'];
            }

            // Handle resume file upload
            if ($request->hasFile('resume')) {
                $cloudinary = new CloudinaryService();
                $result = $cloudinary->upload($request->file('resume'), 'resumes', [
                    'public_id' => 'resume_temp_' . time(),
                    'tags' => ['resume', 'registration'],
                    'resource_type' => 'raw',
                    'use_filename' => false,
                    'unique_filename' => false,
                ]);
                $userData['resume_path'] = $result['secure_url'];
            }

            $user = User::create($userData);

            // Skip role assignment for now until we confirm database structure
            Log::info('User created successfully: ' . $user->email);

            DB::commit();
            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'Registration failed. Please try again.']);
        }
    }
}