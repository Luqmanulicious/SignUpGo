@extends('layouts.app')

@section('styles')
<style>
    /* Profile Section */
    .profile-section {
        position: fixed !important;
        top: 1rem !important;
        right: 1rem !important;
        left: auto !important;
        z-index: 10000 !important;
    }

    .profile-button {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 50px;
        padding: 0.4rem 0.8rem 0.4rem 0.4rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 200px;
    }

    .profile-button:hover {
        border-color: #3498db;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        transform: translateY(-2px);
    }

    .profile-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
        flex: 1;
    }

    .profile-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.85rem;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 140px;
    }

    .profile-email {
        font-size: 0.7rem;
        color: #7f8c8d;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 140px;
    }

    .profile-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        min-width: 220px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .profile-section.active .profile-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #2c3e50;
        text-decoration: none;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .profile-dropdown-item:first-child {
        border-radius: 12px 12px 0 0;
    }

    .profile-dropdown-item:last-child {
        border-bottom: none;
        border-radius: 0 0 12px 12px;
    }

    .profile-dropdown-item:hover {
        background: #f8f9fa;
        padding-left: 1.25rem;
    }

    .profile-dropdown-item.logout {
        color: #e74c3c;
    }

    .profile-dropdown-item.logout:hover {
        background: #fee;
    }

    .profile-dropdown-icon {
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .profile-section {
            top: 0.5rem;
            right: 0.5rem;
        }

        .profile-info {
            display: none;
        }

        .profile-button {
            padding: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Profile Section -->
@auth
<div class="profile-section" id="profileSection">
    <div class="profile-button" onclick="toggleProfile()">
        <div class="profile-avatar">
            @if(Auth::user()->profile_picture)
                <img src="{{ Auth::user()->profile_picture }}" alt="{{ Auth::user()->name }}">
            @else
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-info">
            <div class="profile-name">{{ Auth::user()->name }}</div>
            <div class="profile-email">{{ Auth::user()->email }}</div>
        </div>
    </div>

    <div class="profile-dropdown">
        <a href="{{ route('dashboard') }}" class="profile-dropdown-item">
            <span class="profile-dropdown-icon">🏠</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('account.index') }}" class="profile-dropdown-item">
            <span class="profile-dropdown-icon">👤</span>
            <span>My Account</span>
        </a>
        <a href="{{ route('registrations.index') }}" class="profile-dropdown-item">
            <span class="profile-dropdown-icon">📝</span>
            <span>My Registrations</span>
        </a>
        <a href="{{ route('events.index') }}" class="profile-dropdown-item">
            <span class="profile-dropdown-icon">🎫</span>
            <span>Browse Events</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
            @csrf
            <button type="submit" class="profile-dropdown-item logout" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-size: 1rem; font-family: inherit;">
                <span class="profile-dropdown-icon">🚪</span>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>

<script>
    function toggleProfile() {
        const profileSection = document.getElementById('profileSection');
        profileSection.classList.toggle('active');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profileSection = document.getElementById('profileSection');
        if (!profileSection.contains(event.target)) {
            profileSection.classList.remove('active');
        }
    });
</script>
@endauth

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard.registrations') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ← Back to My Registrations
            </a>
        </div>

        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Registration Details</h1>
            <p class="text-gray-600 mt-2">{{ $registration->event->event_name }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Registration Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Registration Status</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-gray-500">Registration Code</span>
                            <p class="font-mono text-lg font-semibold">{{ $registration->registration_code }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-500">Role</span>
                            <p class="mt-1">
                                @if($registration->role === 'participant')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-cyan-100 text-cyan-800">
                                        Participant
                                    </span>
                                @elseif($registration->role === 'jury')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        Jury
                                    </span>
                                @elseif($registration->role === 'reviewer')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        Reviewer
                                    </span>
                                @endif
                            </p>
                        </div>

                        @php
                            $isConference = strtolower($registration->event->event_type) === 'conference';
                            $isParticipant = $registration->role === 'participant';
                            
                            // Check if paper is assigned to reviewers
                            $hasReviewerAssignment = false;
                            if ($registration->paper) {
                                $hasReviewerAssignment = \DB::table('jury_mappings')
                                    ->where('participant_registration_id', $registration->id)
                                    ->exists();
                            }
                        @endphp

                        <div>
                            <span class="text-sm text-gray-500">{{ ($isConference && $isParticipant) ? 'Paper Status' : 'Approval Status' }}</span>
                            <p class="mt-1">
                                @if($isConference && $isParticipant)
                                    <!-- Conference Participant Status -->
                                    @if($registration->rejected_at)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            ❌ Rejected by EO
                                        </span>
                                    @elseif($registration->paper)
                                        @if($registration->paper->status === 'approved')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                ✅ Paper Approved
                                            </span>
                                        @elseif($registration->paper->status === 'rejected')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                ❌ Paper Rejected
                                            </span>
                                        @elseif($registration->paper->status === 'under_review' || $hasReviewerAssignment)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                🔍 In Review
                                            </span>
                                        @elseif($registration->paper->status === 'submitted')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                ⏱ Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                📝 Paper Draft
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            ⏱ Registration Pending
                                        </span>
                                    @endif
                                @else
                                    <!-- Non-Conference or Jury Status -->
                                    @if($registration->approved_at)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            ✓ Approved
                                        </span>
                                    @elseif($registration->rejected_at)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            ✗ Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            ⏱ Pending Approval
                                        </span>
                                    @endif
                                @endif
                            </p>
                        </div>

                        @if($registration->checked_in_at)
                        <div>
                            <span class="text-sm text-gray-500">Check-In Status</span>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    ✓ Checked In
                                </span>
                                <span class="text-sm text-gray-600 ml-2">
                                    on {{ $registration->checked_in_at->format('F j, Y \a\t h:i A') }}
                                </span>
                            </p>
                        </div>
                        @endif

                        @if($registration->rejected_at && $registration->rejected_reason)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <span class="text-sm font-semibold text-red-800">Rejection Reason:</span>
                            <p class="text-sm text-red-700 mt-1">{{ $registration->rejected_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Event Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Details</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500">Event Name</span>
                            <p class="font-semibold">{{ $registration->event->event_name }}</p>
                        </div>
                        
                        <div>
                            <span class="text-sm text-gray-500">Date & Time</span>
                            <p class="font-semibold">
                                {{ \Carbon\Carbon::parse($registration->event->event_date)->format('l, F j, Y') }}
                                @if($registration->event->start_time)
                                    at {{ \Carbon\Carbon::parse($registration->event->start_time)->format('h:i A') }}
                                @endif
                            </p>
                        </div>
                        
                        @if($registration->event->location)
                        <div>
                            <span class="text-sm text-gray-500">Location</span>
                            <p class="font-semibold">{{ $registration->event->location }}</p>
                        </div>
                        @endif
                        
                        @if($registration->event->description)
                        <div>
                            <span class="text-sm text-gray-500">Description</span>
                            <p class="text-gray-700">{{ $registration->event->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- QR Code -->
                @if($registration->approved_at && $registration->qr_image_path)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Your QR Code</h3>
                    
                    <div class="flex justify-center mb-4">
                        <img src="{{ $registration->qr_image_path }}" 
                             alt="Registration QR Code" 
                             class="w-64 h-64 border-4 border-gray-200 rounded-lg">
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-800 text-center">
                            <strong>Scan this QR code at the event</strong> to check in automatically!
                        </p>
                    </div>
                    
                    @if(!$registration->checked_in_at)
                    <div class="text-center">
                        <a href="{{ route('qr.scan.registration', $registration->qr_code) }}" 
                           target="_blank"
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Test QR Code
                        </a>
                    </div>
                    @endif
                    
                    <!-- Download QR Button -->
                    <div class="mt-4 text-center">
                        <a href="{{ $registration->qr_image_path }}" 
                           download="qr-{{ $registration->registration_code }}.png"
                           class="text-sm text-gray-600 hover:text-gray-800 underline">
                            Download QR Code
                        </a>
                    </div>
                </div>
                @elseif($registration->approved_at)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <p class="text-sm text-yellow-800 text-center">
                        Your QR code is being generated. Please refresh this page in a moment.
                    </p>
                </div>
                @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="text-sm text-gray-600 text-center">
                            Your QR code will be generated once your registration is approved by the event organizer.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('events.show', $registration->event->slug) }}" 
                           class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                            View Event Page
                        </a>
                        
                        @if(!$registration->cancelled_at && !$registration->checked_in_at)
                        <form action="{{ route('dashboard.registrations.cancel', $registration) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel this registration?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-100 hover:bg-red-200 text-red-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Cancel Registration
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
