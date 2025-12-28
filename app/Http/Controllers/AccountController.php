<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * Show the user's account page.
     */
    public function index()
    {
        $user = User::find(Auth::id());
        return view('account.index', compact('user'));
    }

    /**
     * Show the edit form for the user's profile.
     */
    public function edit()
    {
        $user = User::find(Auth::id());
        return view('account.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postcode' => ['nullable', 'string', 'max:10'],
            'website' => ['nullable', 'url', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:5120'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'organization' => $request->organization,
                'address' => $request->address,
                'postcode' => $request->postcode,
                'website' => $request->website,
            ];

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $cloudinary = new CloudinaryService();
                
                // Delete old profile picture if exists
                if ($user->profile_picture) {
                    $cloudinary->deleteByUrl($user->profile_picture);
                }
                
                // Upload to Cloudinary
                $result = $cloudinary->uploadProfilePicture($request->file('profile_picture'), $user->id);
                $userData['profile_picture'] = $result['secure_url'];
            }

            // Handle certificate file upload
            if ($request->hasFile('certificate')) {
                $cloudinary = new CloudinaryService();
                
                // Delete old certificate if exists
                if ($user->certificate_path) {
                    $cloudinary->deleteByUrl($user->certificate_path);
                }
                
                // Upload to Cloudinary
                $result = $cloudinary->uploadCertificate($request->file('certificate'), $user->id);
                $userData['certificate_path'] = $result['secure_url'];
            }

            // Handle resume file upload
            if ($request->hasFile('resume')) {
                $cloudinary = new CloudinaryService();
                
                // Delete old resume if exists
                if ($user->resume_path) {
                    $cloudinary->deleteByUrl($user->resume_path);
                }
                
                // Upload to Cloudinary
                $result = $cloudinary->uploadResume($request->file('resume'), $user->id);
                $userData['resume_path'] = $result['secure_url'];
            }

            $user->fill($userData);
            $user->save();

            return redirect()->route('account.index')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return back()->withInput()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Delete uploaded file (certificate or resume).
     */
    public function deleteFile(Request $request)
    {
        $user = User::find(Auth::id());
        $fileType = $request->input('type'); // 'certificate' or 'resume'
        $cloudinary = new CloudinaryService();

        if ($fileType === 'certificate' && $user->certificate_path) {
            $cloudinary->deleteByUrl($user->certificate_path);
            $user->certificate_path = null;
            $user->save();
            return back()->with('success', 'Certificate deleted successfully!');
        }

        if ($fileType === 'resume' && $user->resume_path) {
            $cloudinary->deleteByUrl($user->resume_path);
            $user->resume_path = null;
            $user->save();
            return back()->with('success', 'Resume deleted successfully!');
        }

        return back()->withErrors(['error' => 'File not found.']);
    }

    /**
     * Download/view certificate.
     */
    public function downloadCertificate()
    {
        $user = User::find(Auth::id());
        
        if (!$user->certificate_path) {
            abort(404, 'Certificate not found');
        }

        // Redirect to Cloudinary URL
        return redirect($user->certificate_path);
    }

    /**
     * Download/view resume.
     */
    public function downloadResume()
    {
        $user = User::find(Auth::id());
        
        if (!$user->resume_path) {
            abort(404, 'Resume not found');
        }

        // Redirect to Cloudinary URL
        return redirect($user->resume_path);
    }
}
