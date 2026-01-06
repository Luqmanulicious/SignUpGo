@extends('layouts.app')

@section('title', 'Register for ' . $event->title)

@php
use Illuminate\Support\Facades\Storage;
@endphp

<!-- Toast Notification -->
<x-toast-notification />

@section('styles')
<style>
    .container { 
        max-width: 800px;
        width: 100%;
    }
    
    .card { 
        background: white; 
        padding: 2rem; 
        border-radius: 8px; 
        box-shadow: 0 2px 6px rgba(0,0,0,0.06); 
    }
    
    .event-header {
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 2rem;
    }
    
    .event-header h2 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
        font-size: 1.5rem;
    }
    
    .event-header .meta {
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        font-weight: 500;
    }
    
    .form-group label.required::after {
        content: ' *';
        color: #e74c3c;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .role-option {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .role-option:hover {
        border-color: #3498db;
        background: #f8fafc;
    }
    
    .role-option input[type="radio"] {
        margin-top: 0.25rem;
        margin-right: 1rem;
        cursor: pointer;
    }
    
    .role-option.selected {
        border-color: #3498db;
        background: #e3f2fd;
    }
    
    .role-info h4 {
        margin: 0 0 0.25rem 0;
        color: #2c3e50;
        font-size: 1.1rem;
    }
    
    .role-info p {
        margin: 0;
        color: #7f8c8d;
        font-size: 0.9rem;
    }
    
    .certificate-upload {
        display: none;
        margin-top: 1rem;
        padding: 1rem;
        background: #e2dcf2;
        border-radius: 6px;
        border-left: 4px solid #310598;
    }
    
    .certificate-upload.show {
        display: block;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }
    
    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }
    
    .alert-info {
        background: #d1ecf1;
        border-left: 4px solid #17a2b8;
        color: #0c5460;
    }
    
    .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn:hover {
        background: #2980b9;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background: #95a5a6;
    }
    
    .btn-secondary:hover {
        background: #7f8c8d;
    }
    
    .btn-danger {
        background: #e74c3c;
    }
    
    .btn-danger:hover {
        background: #c0392b;
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .help-text {
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-top: 0.25rem;
    }
    
    .error-text {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .span {
        color: rgb(29, 0, 159);
    }

    .expertise-option {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .expertise-option:hover {
        border-color: #3498db;
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.1);
    }

    .expertise-option input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        flex-shrink: 0;
    }

    .expertise-option.selected {
        border-color: #3498db;
        background: #e3f2fd;
    }

    .expertise-label {
        font-size: 1rem;
        color: #2c3e50;
        font-weight: 500;
        flex: 1;
    }

    .expertise-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 0.75rem;
    }

</style>
@endsection

@section('content')
<div class="container">
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('events.show', $event) }}" style="display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; background: #6c7778; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">
            ← Back to Event Details
        </a>
    </div>

    <div class="card">
        <div class="event-header">
            <h2>{{ $event->title }}</h2>
            <p class="meta">
                {{ $event->event_type }} Event • 
                @php
                    try {
                        $start = $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'TBA';
                    } catch (\Exception $e) {
                        $start = 'TBA';
                    }
                @endphp
                {{ $start }}
            </p>
        </div>

        <h3 style="margin-top: 0; color: #2c3e50;">Register for this Event</h3>

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <form action="{{ route('registrations.store', $event) }}" method="POST" enctype="multipart/form-data" id="registrationForm" onsubmit="console.log('Form submitting...', new FormData(this));">
            @csrf

<div class="form-group">
                <label class="required">Your Role</label>
                
                @if(isset($selectedRole) && $selectedRole)
                    {{-- Role is pre-selected, show as readonly --}}
                    <div style="background: #e3f2fd; padding: 1rem; border-radius: 6px; border-left: 4px solid #3498db;">
                        <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #2c3e50;">
                            {{ ucfirst($selectedRole) }}
                            @if($selectedRole === 'jury')
                                🎓
                            @elseif($selectedRole === 'reviewer')
                                📝
                            @else
                                👤
                            @endif
                        </p>
                        <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #555;">
                            @if($selectedRole === 'jury')
                                Evaluate and judge submissions. Requires certification and organizer approval.
                            @elseif($selectedRole === 'reviewer')
                                Review and provide feedback on submissions.
                            @elseif($selectedRole === 'participant')
                                Participate and submit your work to the event.
                            @endif
                        </p>
                    </div>
                    <input type="hidden" name="role" value="{{ $selectedRole }}" required>
                @else
                    {{-- No pre-selected role, show selection --}}
                    <p class="help-text">Choose how you want to participate in this event.</p>

                    @foreach($availableRoles as $role)
                        <div class="role-option" onclick="selectRole('{{ $role }}')">
                            <input 
                                type="radio" 
                                name="role" 
                                value="{{ $role }}" 
                                id="role_{{ $role }}"
                                {{ old('role') == $role ? 'checked' : '' }}
                                required
                            >
                            <div class="role-info">
                                <h4>{{ ucfirst($role) }}</h4>
                                <p>
                                    @if($role === 'jury')
                                        Evaluate and judge submissions. Requires certification and organizer approval.
                                    @elseif($role === 'reviewer')
                                        Review and provide feedback on submissions.
                                    @elseif($role === 'participant')
                                        Participate and submit your work to the event.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif

                @error('role')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <!-- User Information Display (Read-only, auto-filled from account) -->
            <div class="form-group">
                <label>Your Information</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <!-- Name Box -->
                    <div style="background: white; padding: 1rem; border: 2px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 0.85rem; color: #1976d2; font-weight: 600; margin-bottom: 0.5rem;">Name</div>
                        <div style="font-size: 1rem; color: #2c3e50; font-weight: 500;">{{ Auth::user()->name }}</div>
                    </div>
                    
                    <!-- Email Box -->
                    <div style="background: white; padding: 1rem; border: 2px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 0.85rem; color: #1976d2; font-weight: 600; margin-bottom: 0.5rem;">Email</div>
                        <div style="font-size: 1rem; color: #2c3e50; font-weight: 500;">{{ Auth::user()->email }}</div>
                    </div>
                    
                    <!-- Phone Box -->
                    <div style="background: white; padding: 1rem; border: 2px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 0.85rem; color: #1976d2; font-weight: 600; margin-bottom: 0.5rem;">Phone Number</div>
                        <div style="font-size: 1rem; color: #2c3e50; font-weight: 500;">{{ Auth::user()->phone ?: 'Not provided' }}</div>
                    </div>
                    
                    <!-- Job Title Box -->
                    <div style="background: white; padding: 1rem; border: 2px solid #e3f2fd; border-radius: 8px;">
                        <div style="font-size: 0.85rem; color: #1976d2; font-weight: 600; margin-bottom: 0.5rem;">Job Title</div>
                        <div style="font-size: 1rem; color: #2c3e50; font-weight: 500;">{{ Auth::user()->job_title ?: 'Not provided' }}</div>
                    </div>
                    
                    <!-- Organization Box (Full Width) -->
                    <div style="background: white; padding: 1rem; border: 2px solid #e3f2fd; border-radius: 8px; grid-column: 1 / -1;">
                        <div style="font-size: 0.85rem; color: #1976d2; font-weight: 600; margin-bottom: 0.5rem;">Organization/Institution</div>
                        <div style="font-size: 1rem; color: #2c3e50; font-weight: 500;">{{ Auth::user()->organization ?: 'Not provided' }}</div>
                    </div>
                </div>
                
                <div style="margin-top: 1rem; padding: 1rem; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 6px; font-size: 0.9rem; color: #856404;">
                    ℹ️ To update your information, visit <a href="{{ route('account.index') }}" style="color: #856404; font-weight: 600; text-decoration: underline;">Account Settings</a>
                </div>
                
                <!-- Hidden fields to submit the data -->
                <input type="hidden" name="phone" value="{{ Auth::user()->phone }}">
                <input type="hidden" name="organization" value="{{ Auth::user()->organization }}">
            </div>          

            <!-- Attendance Mode Selection (Only for Participants in Hybrid Events) -->
            @if($event->delivery_mode === 'hybrid')
            <div id="attendanceModeSection" class="form-group" style="{{ isset($selectedRole) ? '' : 'display: none;' }}">
                <label class="required">How will you attend this event?</label>
                
                @if(isset($selectedMode) && $selectedMode)
                    {{-- Mode is pre-selected, show as readonly --}}
                    <div style="background: #e8f5e9; padding: 1rem; border-radius: 6px; border-left: 4px solid #4caf50;">
                        <p style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #2c3e50;">
                            @if($selectedMode === 'face_to_face')
                                🏛️ Face-to-Face
                            @else
                                💻 Online
                            @endif
                        </p>
                        <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #555;">
                            @if($selectedMode === 'face_to_face')
                                Attend the event in person at {{ $event->venue_name }}
                            @else
                                Participate remotely via online platform
                            @endif
                        </p>
                    </div>
                    <input type="hidden" name="attendance_mode" value="{{ $selectedMode }}" required>
                @else
                    <p class="help-text">This event offers both face-to-face and online participation. Please select your preferred mode.</p>

                    <div class="role-option" onclick="selectAttendanceMode('face_to_face')">
                        <input 
                            type="radio" 
                            name="attendance_mode" 
                            value="face_to_face" 
                            id="attendance_face_to_face"
                            {{ old('attendance_mode') == 'face_to_face' ? 'checked' : '' }}
                        >
                        <div class="role-info">
                            <h4>🏛️ Face-to-Face</h4>
                            <p>Attend the event in person at {{ $event->venue_name }}</p>
                            @if($event->f2f_paper_deadline)
                                <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #e74c3c;">
                                    <strong>Paper Deadline:</strong> {{ \Carbon\Carbon::parse($event->f2f_paper_deadline)->format('M d, Y h:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="role-option" onclick="selectAttendanceMode('online')">
                        <input 
                            type="radio" 
                            name="attendance_mode" 
                            value="online" 
                            id="attendance_online"
                            {{ old('attendance_mode') == 'online' ? 'checked' : '' }}
                        >
                        <div class="role-info">
                            <h4>💻 Online</h4>
                            <p>Participate remotely via online platform</p>
                            @if($event->online_paper_deadline)
                                <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #e74c3c;">
                                    <strong>Paper Deadline:</strong> {{ \Carbon\Carbon::parse($event->online_paper_deadline)->format('M d, Y h:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                @error('attendance_mode')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>
            @endif

            <!-- Certification info for jury/reviewer role (read-only from account) -->
            <div id="certificateSection" class="certificate-upload" style="{{ (isset($selectedRole) && in_array($selectedRole, ['jury', 'reviewer'])) ? 'display: block;' : '' }}">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Your Certification</label>
                    @if(Auth::user()->certificate_path)
                        <div style="background: white; padding: 1rem; border: 2px solid #d4edda; border-radius: 8px;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 2rem;">📄</span>
                                <div style="flex: 1;">
                                    <div style="font-size: 0.9rem; color: #176e2c; font-weight: 600;">Certificate Uploaded</div>
                                    <div style="font-size: 0.85rem; color: #6c757d; margin-top: 0.25rem;">{{ basename(parse_url(Auth::user()->certificate_path, PHP_URL_PATH)) }}</div>
                                </div>
                                <a href="{{ Auth::user()->certificate_path }}" target="_blank" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    View Certificate
                                </a>
                            </div>
                        </div>
                        @if(Auth::user()->resume_path)
                            <div style="background: white; padding: 1rem; border: 2px solid #e3f2fd; border-radius: 8px; margin-top: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span style="font-size: 2rem;">📋</span>
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.9rem; color: #1565c0; font-weight: 600;">Resume/CV Uploaded</div>
                                        <div style="font-size: 0.85rem; color: #6c757d; margin-top: 0.25rem;">{{ basename(parse_url(Auth::user()->resume_path, PHP_URL_PATH)) }}</div>
                                    </div>
                                    <a href="{{ Auth::user()->resume_path }}" target="_blank" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                        View Resume
                                    </a>
                                </div>
                            </div>
                        @endif
                        <p class="help-text" style="margin-top: 0.75rem;">✓ Your certification will be reviewed by the event organizer. To update, visit <a href="{{ route('account.index') }}" style="color: #1d0038; font-weight: 600;">Account Settings</a></p>
                    @else
                        <div style="background: #f8d7da; padding: 1rem; border: 2px solid #dc3545; border-radius: 8px; color: #721c24;">
                            <strong>⚠️ Certificate Required</strong>
                            <p style="margin: 0.5rem 0 0 0;">You must upload your certification in <a href="{{ route('account.index') }}" style="color: #721c24; font-weight: 600; text-decoration: underline;">Account Settings</a> before registering as {{ ucfirst($selectedRole ?? 'Jury/Reviewer') }}.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Jury Expertise Section: Categories and Themes -->
            <div id="juryExpertiseSection" class="form-group" style="{{ (isset($selectedRole) && $selectedRole === 'jury') ? 'display: block;' : 'display: none;' }}">
                <label class="required">Areas of Expertise</label>
                <p class="help-text" style="margin-bottom: 1.5rem;">Select the categories and themes you have expertise in. You can select multiple options.</p>
                
                @php
                    $isInnovation = stripos($event->event_type, 'innovation') !== false;
                    $isConference = stripos($event->event_type, 'conference') !== false;
                    
                    $categories = [];
                    $themes = [];
                    
                    // Innovation events: show both categories and themes
                    if ($isInnovation) {
                        // Handle innovation_categories
                        $innovationCategories = $event->innovation_categories;
                        if (is_string($innovationCategories)) {
                            $innovationCategories = json_decode($innovationCategories, true) ?? [];
                        }
                        if (is_array($innovationCategories) && count($innovationCategories) > 0) {
                            $categories = $innovationCategories;
                        }
                        
                        // Handle innovation_theme
                        $innovationTheme = $event->innovation_theme;
                        if (is_string($innovationTheme)) {
                            $innovationTheme = json_decode($innovationTheme, true) ?? [];
                        }
                        if (is_array($innovationTheme) && count($innovationTheme) > 0) {
                            $themes = $innovationTheme;
                        }
                    }
                    
                    // Conference events: show themes only (no categories)
                    if ($isConference) {
                        $conferenceCategories = $event->conference_categories;
                        if (is_string($conferenceCategories)) {
                            $conferenceCategories = json_decode($conferenceCategories, true) ?? [];
                        }
                        if (is_array($conferenceCategories) && count($conferenceCategories) > 0) {
                            $themes = $conferenceCategories;
                        }
                    }
                @endphp
                
                {{-- Innovation Events: Show Categories --}}
                @if($isInnovation && count($categories) > 0)
                <div style="margin-bottom: 2rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #2c3e50; font-size: 1.1rem;">Product Categories <span style="color: #e74c3c;">*</span></h4>
                    <div class="expertise-grid">
                        @foreach($categories as $index => $category)
                        <label class="expertise-option" onclick="toggleExpertise(event, 'category_{{ $index }}')" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="jury_categories[]" 
                                value="{{ $category }}" 
                                id="category_{{ $index }}"
                                {{ is_array(old('jury_categories')) && in_array($category, old('jury_categories')) ? 'checked' : '' }}
                            >
                            <span class="expertise-label">{{ $category }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('jury_categories')
                        <div class="error-text" style="margin-top: 0.75rem; padding: 0.75rem; background: #fee; border-left: 3px solid #e74c3c; border-radius: 4px;">
                            <strong>⚠️ Required:</strong> You must click on at least one category box above to select your expertise area.
                        </div>
                    @enderror
                </div>
                @endif
                
                {{-- Show Themes (for both Innovation and Conference) --}}
                @if(count($themes) > 0)
                <div>
                    <h4 style="margin: 0 0 1rem 0; color: #2c3e50; font-size: 1.1rem;">{{ $isInnovation ? 'Product' : 'Paper' }} Themes <span style="color: #e74c3c;">*</span></h4>
                    <div class="expertise-grid">
                        @foreach($themes as $index => $theme)
                        <label class="expertise-option" onclick="toggleExpertise(event, 'theme_{{ $index }}')" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="jury_themes[]" 
                                value="{{ $theme }}" 
                                id="theme_{{ $index }}"
                                {{ is_array(old('jury_themes')) && in_array($theme, old('jury_themes')) ? 'checked' : '' }}
                            >
                            <span class="expertise-label">{{ $theme }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('jury_themes')
                        <div class="error-text" style="margin-top: 0.75rem; padding: 0.75rem; background: #fee; border-left: 3px solid #e74c3c; border-radius: 4px;">
                            <strong>⚠️ Required:</strong> You must click on at least one theme box above. We need this to assign you the right submissions to review.
                        </div>
                    @enderror
                </div>
                @endif
            </div>

            <!-- Reviewer Theme Selection Section (For Conference Events Only) -->
            <div id="reviewerThemeSection" class="form-group" style="{{ (isset($selectedRole) && $selectedRole === 'reviewer') ? 'display: block;' : 'display: none;' }}">
                @php
                    $isConferenceEvent = stripos($event->event_type, 'conference') !== false;
                    $conferenceThemes = [];
                    
                    if ($isConferenceEvent) {
                        $conferenceCategories = $event->conference_categories;
                        if (is_string($conferenceCategories)) {
                            $conferenceCategories = json_decode($conferenceCategories, true) ?? [];
                        }
                        if (is_array($conferenceCategories) && count($conferenceCategories) > 0) {
                            $conferenceThemes = $conferenceCategories;
                        }
                    }
                @endphp
                
                @if($isConferenceEvent && count($conferenceThemes) > 0)
                <div>
                    <label class="required">Paper Themes</label>
                    <p class="help-text" style="margin-bottom: 1.5rem;">Select the themes you have expertise in. You can select multiple options.</p>
                    <div class="expertise-grid">
                        @foreach($conferenceThemes as $index => $theme)
                        <label class="expertise-option" onclick="toggleExpertise(event, 'reviewer_theme_{{ $index }}')" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="reviewer_themes[]" 
                                value="{{ $theme }}" 
                                id="reviewer_theme_{{ $index }}"
                                {{ is_array(old('reviewer_themes')) && in_array($theme, old('reviewer_themes')) ? 'checked' : '' }}
                            >
                            <span class="expertise-label">{{ $theme }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('reviewer_themes')
                        <div class="error-text" style="margin-top: 0.75rem; padding: 0.75rem; background: #fee; border-left: 3px solid #e74c3c; border-radius: 4px;">
                            <strong>⚠️ Required:</strong> You must select at least one theme for your expertise area.
                        </div>
                    @enderror
                </div>
                @endif
            </div>

            <!-- Product/Paper Submission Section (Only for Participants) -->
            <div id="paperSection" style="display: none;">
                @php
                    $isInnovationType = stripos($event->event_type, 'innovation') !== false;
                    $submissionLabel = $isInnovationType ? 'Product' : 'Paper';
                @endphp
                <h4 style="margin: 2rem 0 1rem 0; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 0.5rem;">
                    {{ $submissionLabel }} Submission <span style="color: #e74c3c;">*</span>
                </h4>
                <p class="help-text" style="margin-bottom: 1.5rem;">As a participant, you must submit your {{ strtolower($submissionLabel) }} along with your registration. You can save it as a draft and edit it later before final submission.</p>

                @php
                    $isInnovation = stripos($event->event_type, 'innovation') !== false;
                    $isConference = stripos($event->event_type, 'conference') !== false;
                    
                    $categories = [];
                    $themes = [];
                    
                    // Innovation events: show both categories and themes
                    if ($isInnovation) {
                        // Handle innovation_categories
                        $innovationCategories = $event->innovation_categories;
                        if (is_string($innovationCategories)) {
                            $innovationCategories = json_decode($innovationCategories, true) ?? [];
                        }
                        if (is_array($innovationCategories) && count($innovationCategories) > 0) {
                            $categories = $innovationCategories;
                        }
                        
                        // Handle innovation_theme
                        $innovationTheme = $event->innovation_theme;
                        if (is_string($innovationTheme)) {
                            $innovationTheme = json_decode($innovationTheme, true) ?? [];
                        }
                        if (is_array($innovationTheme) && count($innovationTheme) > 0) {
                            $themes = $innovationTheme;
                        }
                    }
                    
                    // Conference events: show themes only (no categories)
                    if ($isConference) {
                        $conferenceCategories = $event->conference_categories;
                        if (is_string($conferenceCategories)) {
                            $conferenceCategories = json_decode($conferenceCategories, true) ?? [];
                        }
                        if (is_array($conferenceCategories) && count($conferenceCategories) > 0) {
                            $themes = $conferenceCategories;
                        }
                    }
                @endphp
                
                {{-- Innovation Events Only: Show Categories --}}
                @if($isInnovation && count($categories) > 0)
                <div class="form-group">
                    <label class="required" for="paper_category">Product Category</label>
                    <select name="paper_category" id="paper_category" class="form-control">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('paper_category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    <p class="help-text">Choose the category that best fits your product.</p>
                    @error('paper_category')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                @endif
                
                {{-- Show Themes (for both Innovation and Conference) --}}
                @if(count($themes) > 0)
                <div class="form-group">
                    <label class="required" for="paper_theme">{{ $isInnovationType ? 'Product' : 'Paper' }} Theme</label>
                    <select name="paper_theme" id="paper_theme" class="form-control">
                        <option value="">Select a theme</option>
                        @foreach($themes as $theme)
                            <option value="{{ $theme }}" {{ old('paper_theme') == $theme ? 'selected' : '' }}>
                                {{ $theme }}
                            </option>
                        @endforeach
                    </select>
                    <p class="help-text">Choose the theme that best fits your {{ $isInnovationType ? 'product' : 'paper' }}.</p>
                    @error('paper_theme')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                @endif

                <div class="form-group">
                    <label class="required" for="paper_title">{{ $isInnovationType ? 'Product' : 'Paper' }} Title</label>
                    <input 
                        type="text" 
                        name="paper_title" 
                        id="paper_title"
                        class="form-control"
                        placeholder="Enter your {{ $isInnovationType ? 'product' : 'paper' }} title"
                        value="{{ old('paper_title') }}"
                        maxlength="255"
                    >
                    @error('paper_title')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="required" for="paper_abstract">Abstract</label>
                    <textarea 
                        name="paper_abstract" 
                        id="paper_abstract"
                        class="form-control" 
                        rows="6"
                        maxlength="2000"
                        placeholder="Provide a brief abstract of your {{ $isInnovationType ? 'product' : 'paper' }} (max 2000 characters)"
                    >{{ old('paper_abstract') }}</textarea>
                    <p class="help-text">{{ $isInnovationType ? 'Briefly describe your product, its features, and key innovations.' : 'Briefly describe your research, methodology, and key findings.' }}</p>
                    @error('paper_abstract')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="required" for="paper_poster">Upload {{ $isInnovationType ? 'Poster' : 'Paper' }}</label>
                    <input 
                        type="file" 
                        name="paper_poster" 
                        id="paper_poster"
                        class="form-control"
                        accept="{{ $isInnovationType ? 'image/*' : '.doc,.docx,.pdf' }}"
                    >
                    <p class="help-text">{{ $isInnovationType ? 'Upload your presentation poster (JPG, PNG, or PDF - Max 10MB)' : 'Upload your paper document (DOC, DOCX, or PDF - Max 10MB)' }}</p>
                    @error('paper_poster')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="paper_video_url">Video URL (Optional)</label>
                    <input 
                        type="url" 
                        name="paper_video_url" 
                        id="paper_video_url"
                        class="form-control"
                        placeholder="https://youtube.com/watch?v=..."
                        value="{{ old('paper_video_url') }}"
                    >
                    <p class="help-text">Provide a link to your video presentation (YouTube, Google Drive, Vimeo, etc.)</p>
                    @error('paper_video_url')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">
                    Submit Registration
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleExpertise(event, checkboxId) {
        event.preventDefault(); // Prevent default label behavior
        
        const checkbox = document.getElementById(checkboxId);
        const label = event.currentTarget;
        
        // Don't toggle if user clicked directly on the checkbox
        if (event.target === checkbox) {
            // Checkbox will toggle itself, just update visual
            setTimeout(() => {
                if (checkbox.checked) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            }, 0);
            return;
        }
        
        // Toggle checkbox when clicking on label
        checkbox.checked = !checkbox.checked;
        
        // Trigger change event for form validation
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Toggle visual selection
        if (checkbox.checked) {
            label.classList.add('selected');
        } else {
            label.classList.remove('selected');
        }
    }
    
    function selectRole(role) {
        // Update radio button
        document.getElementById('role_' + role).checked = true;
        
        // Update visual selection
        document.querySelectorAll('.role-option').forEach(option => {
            option.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
        
        // Show/hide certificate section based on role
        const certificateSection = document.getElementById('certificateSection');
        const juryExpertiseSection = document.getElementById('juryExpertiseSection');
        const reviewerThemeSection = document.getElementById('reviewerThemeSection');
        
        if (role === 'jury' || role === 'reviewer') {
            certificateSection.classList.add('show');
        } else {
            certificateSection.classList.remove('show');
        }
        
        // Show/hide jury expertise section for jury role only
        if (role === 'jury') {
            if (juryExpertiseSection) {
                juryExpertiseSection.style.display = 'block';
            }
            if (reviewerThemeSection) {
                reviewerThemeSection.style.display = 'none';
            }
        } else if (role === 'reviewer') {
            if (juryExpertiseSection) {
                juryExpertiseSection.style.display = 'none';
            }
            if (reviewerThemeSection) {
                reviewerThemeSection.style.display = 'block';
            }
        } else {
            if (juryExpertiseSection) {
                juryExpertiseSection.style.display = 'none';
            }
            if (reviewerThemeSection) {
                reviewerThemeSection.style.display = 'none';
            }
        }

        // Show/hide paper section and set required fields for participants only
        const paperSection = document.getElementById('paperSection');
        const paperTitle = document.getElementById('paper_title');
        const paperAbstract = document.getElementById('paper_abstract');
        const paperPoster = document.getElementById('paper_poster');
        const paperCategory = document.getElementById('paper_category');
        const paperTheme = document.getElementById('paper_theme');
        
        @if($event->delivery_mode === 'hybrid')
        const attendanceModeSection = document.getElementById('attendanceModeSection');
        const attendanceF2F = document.getElementById('attendance_face_to_face');
        const attendanceOnline = document.getElementById('attendance_online');
        @endif
        
        if (role === 'participant') {
            paperSection.style.display = 'block';
            if (paperTitle) paperTitle.required = true;
            if (paperAbstract) paperAbstract.required = true;
            if (paperPoster) paperPoster.required = true;
            if (paperCategory) paperCategory.required = true;
            if (paperTheme) paperTheme.required = true;
            
            @if($event->delivery_mode === 'hybrid')
            // Show attendance mode for participants in hybrid events
            if (attendanceModeSection) {
                attendanceModeSection.style.display = 'block';
                if (attendanceF2F) attendanceF2F.required = true;
                if (attendanceOnline) attendanceOnline.required = true;
            }
            @endif
        } else {
            paperSection.style.display = 'none';
            if (paperTitle) {
                paperTitle.required = false;
                paperTitle.value = '';
            }
            if (paperAbstract) {
                paperAbstract.required = false;
                paperAbstract.value = '';
            }
            if (paperPoster) {
                paperPoster.required = false;
                paperPoster.value = '';
            }
            if (paperCategory) {
                paperCategory.required = false;
                paperCategory.value = '';
            }
            if (paperTheme) {
                paperTheme.required = false;
                paperTheme.value = '';
            }
            document.getElementById('paper_video_url').value = '';
            
            @if($event->delivery_mode === 'hybrid')
            // Hide attendance mode for non-participants
            if (attendanceModeSection) {
                attendanceModeSection.style.display = 'none';
                if (attendanceF2F) {
                    attendanceF2F.required = false;
                    attendanceF2F.checked = false;
                    attendanceF2F.removeAttribute('required');
                }
                if (attendanceOnline) {
                    attendanceOnline.required = false;
                    attendanceOnline.checked = false;
                    attendanceOnline.removeAttribute('required');
                }
            }
            @endif
        }
    }
    
    function selectAttendanceMode(mode) {
        // Update radio button
        document.getElementById('attendance_' + mode).checked = true;
        
        // Update visual selection
        const attendanceOptions = document.querySelectorAll('[onclick^="selectAttendanceMode"]');
        attendanceOptions.forEach(option => {
            option.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($selectedRole) && $selectedRole)
            // Role is pre-selected from URL
            const preSelectedRole = '{{ $selectedRole }}';
            const certificateSection = document.getElementById('certificateSection');
            const juryExpertiseSection = document.getElementById('juryExpertiseSection');
            const reviewerThemeSection = document.getElementById('reviewerThemeSection');
            
            if (preSelectedRole === 'jury' || preSelectedRole === 'reviewer') {
                certificateSection.classList.add('show');
            }
            
            // Show jury expertise section if role is jury
            if (preSelectedRole === 'jury' && juryExpertiseSection) {
                juryExpertiseSection.style.display = 'block';
            }
            
            // Show reviewer theme section if role is reviewer
            if (preSelectedRole === 'reviewer' && reviewerThemeSection) {
                reviewerThemeSection.style.display = 'block';
            }
            
            if (preSelectedRole === 'jury' || preSelectedRole === 'reviewer') {
                certificateSection.classList.add('show');
                
                @if($event->delivery_mode === 'hybrid')
                // Ensure attendance mode is hidden and not required for jury/reviewer
                const attendanceModeSection = document.getElementById('attendanceModeSection');
                if (attendanceModeSection) {
                    attendanceModeSection.style.display = 'none';
                    const attendanceF2F = document.getElementById('attendance_face_to_face');
                    const attendanceOnline = document.getElementById('attendance_online');
                    if (attendanceF2F) {
                        attendanceF2F.required = false;
                        attendanceF2F.removeAttribute('required');
                    }
                    if (attendanceOnline) {
                        attendanceOnline.required = false;
                        attendanceOnline.removeAttribute('required');
                    }
                }
                @endif
            }

            // Handle paper section for pre-selected participant role
            if (preSelectedRole === 'participant') {
                const paperSection = document.getElementById('paperSection');
                const paperTitle = document.getElementById('paper_title');
                const paperAbstract = document.getElementById('paper_abstract');
                const paperPoster = document.getElementById('paper_poster');
                const paperCategory = document.getElementById('paper_category');
                const paperTheme = document.getElementById('paper_theme');
                
                paperSection.style.display = 'block';
                if (paperTitle) paperTitle.required = true;
                if (paperAbstract) paperAbstract.required = true;
                if (paperPoster) paperPoster.required = true;
                if (paperCategory) paperCategory.required = true;
                if (paperTheme) paperTheme.required = true;
                
                @if($event->delivery_mode === 'hybrid')
                // Show attendance mode for participants in hybrid events
                const attendanceModeSection = document.getElementById('attendanceModeSection');
                if (attendanceModeSection) {
                    attendanceModeSection.style.display = 'block';
                    const attendanceF2F = document.getElementById('attendance_face_to_face');
                    const attendanceOnline = document.getElementById('attendance_online');
                    if (attendanceF2F) attendanceF2F.required = true;
                    if (attendanceOnline) attendanceOnline.required = true;
                }
                @endif
            }
        @else
            // Check for old input or manually selected role
            const selectedRole = document.querySelector('input[name="role"]:checked');
            if (selectedRole) {
                selectRole(selectedRole.value);
            }
            
            @if($event->delivery_mode === 'hybrid')
            // If no role selected yet, ensure attendance mode is hidden
            if (!selectedRole) {
                const attendanceModeSection = document.getElementById('attendanceModeSection');
                if (attendanceModeSection) {
                    attendanceModeSection.style.display = 'none';
                    const attendanceF2F = document.getElementById('attendance_face_to_face');
                    const attendanceOnline = document.getElementById('attendance_online');
                    if (attendanceF2F) {
                        attendanceF2F.required = false;
                        attendanceF2F.removeAttribute('required');
                    }
                    if (attendanceOnline) {
                        attendanceOnline.required = false;
                        attendanceOnline.removeAttribute('required');
                    }
                }
            }
            @endif
        @endif
        
        // Initialize checkbox visual states
        document.querySelectorAll('.expertise-option input[type=\"checkbox\"]').forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.closest('.expertise-option').classList.add('selected');
            }
        });
    });
</script>
@endsection
