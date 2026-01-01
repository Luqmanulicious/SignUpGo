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
        
        // Fetch all certificates for the authenticated user with event category and registration role
        $certificates = DB::table('generated_certificates')
            ->leftJoin('events', 'generated_certificates.event_id', '=', 'events.id')
            ->leftJoin('event_categories', 'events.category_id', '=', 'event_categories.id')
            ->leftJoin('event_registrations', function($join) use ($user) {
                $join->on('event_registrations.event_id', '=', 'generated_certificates.event_id')
                     ->where('event_registrations.user_id', '=', $user->id);
            })
            ->where('generated_certificates.user_id', $user->id)
            ->select('generated_certificates.*', 'event_categories.name as event_category', 'event_registrations.role as registration_role')
            ->orderBy('generated_certificates.created_at', 'desc')
            ->get();
        
        return view('certificates.index', compact('certificates'));
    }
    
    public function download($id)
    {
        $user = Auth::user();
        
        // Fetch the certificate and verify ownership
        $certificate = DB::table('generated_certificates')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$certificate) {
            return redirect()->route('certificates.index')
                ->with('error', 'Certificate not found or access denied.');
        }
        
        // Update downloaded_at timestamp
        DB::table('generated_certificates')
            ->where('id', $id)
            ->update(['downloaded_at' => now()]);
        
        // Redirect to the certificate URL
        return redirect($certificate->certificate_url);
    }
}
