<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch all certificates for the authenticated user with event details and certificate info
        // Exclude rejected registrations and rejected presentations
        $certificates = DB::table('event_registrations')
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->leftJoin('event_categories', 'events.category_id', '=', 'event_categories.id')
            ->where('event_registrations.user_id', $user->id)
            ->whereNotIn('event_registrations.status', ['rejected', 'cancelled'])
            ->where(function($query) {
                $query->whereNull('event_registrations.presentation_status')
                      ->orWhere('event_registrations.presentation_status', '!=', 'rejected');
            })
            ->whereNotNull('event_registrations.certificate_path')
            ->whereNotNull('event_registrations.certificate_filename')
            ->select(
                'event_registrations.id',
                'event_registrations.certificate_path',
                'event_registrations.certificate_filename',
                'event_registrations.role as registration_role',
                'event_registrations.created_at as generated_at',
                'event_registrations.updated_at as downloaded_at',
                'events.title as event_name',
                'events.start_date as event_date',
                'event_categories.name as event_category',
                'users.name as participant_name'
            )
            ->orderBy('event_registrations.updated_at', 'desc')
            ->get();
        
        return view('certificates.index', compact('certificates'));
    }
    
    public function download($id)
    {
        $user = Auth::user();
        
        // Fetch the certificate and verify ownership
        // Exclude rejected registrations and rejected presentations
        $registration = DB::table('event_registrations')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where(function($query) {
                $query->whereNull('presentation_status')
                      ->orWhere('presentation_status', '!=', 'rejected');
            })
            ->whereNotNull('certificate_path')
            ->whereNotNull('certificate_filename')
            ->first();
        
        if (!$registration) {
            return redirect()->route('certificates.index')
                ->with('error', 'Certificate not found or access denied.');
        }
        
        // Update downloaded_at timestamp
        DB::table('event_registrations')
            ->where('id', $id)
            ->update(['updated_at' => now()]);
        
        // Generate storage URL for the certificate
        $certificateUrl = \Storage::url($registration->certificate_path . '/' . $registration->certificate_filename);
        
        return redirect($certificateUrl);
    }
}
