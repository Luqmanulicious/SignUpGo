@extends('layouts.app')

@section('title', 'Participant Dashboard - ' . $event->title)

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

        /* Ensure modal displays as overlay */
        #paymentModal.modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        #paymentModal.modal.show {
            display: block !important;
        }

        #paymentModal .modal-backdrop {
            z-index: 9998;
        }

        /* Payment Modal Styling */
        #paymentModal .modal-dialog {
            max-width: 600px;
        }

        #paymentModal .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        #paymentModal .modal-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem 2rem;
            border: none;
        }

        #paymentModal .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        #paymentModal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 1;
        }

        #paymentModal .btn-close:hover {
            opacity: 0.8;
        }

        #paymentModal .modal-body {
            padding: 2rem;
        }

        .payment-info-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 5px solid #28a745;
        }

        .payment-info-box h5 {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .payment-fee-display {
            background: white;
            padding: 1.25rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .payment-fee-display .label {
            font-weight: 600;
            color: #495057;
            font-size: 1rem;
        }

        .payment-fee-display .amount {
            font-size: 1.75rem;
            font-weight: bold;
            color: #28a745;
        }

        .payment-qr-section {
            text-align: center;
            margin: 1.5rem 0;
            padding: 2rem 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .payment-qr-section h5 {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 1.25rem;
            font-weight: 600;
        }

        .payment-qr-section img {
            max-width: 280px;
            width: 100%;
            border: 4px solid #28a745;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            padding: 10px;
            background: white;
        }

        .payment-qr-section p {
            margin-top: 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .payment-upload-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
        }

        .payment-upload-section h5 {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .payment-upload-section .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.95rem;
        }

        .payment-upload-section .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.95rem;
        }

        .payment-upload-section .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
        }

        .payment-upload-section .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .payment-warning-box {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 1.25rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            text-align: center;
        }

        .payment-warning-box p {
            margin: 0;
            color: #856404;
            font-size: 0.95rem;
        }

        .poster-preview {
            text-align: center;
            margin-top: 1rem;
        }

        .poster-preview h4 {
            margin: 0 0 1rem 0;
            color: #2c3e50;
        }

        .poster-preview img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <!-- Profile Section -->
    {{-- @auth
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
    @endauth --}}

    <a href="{{ route('registrations.index') }}"
        style="padding: 0.75rem 1.5rem; background: #6c7778; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
        ← Back to My Registrations
    </a>
    <div class="container" style="max-width: 1400px; padding: 2rem;">
        <div
            style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="margin: 0 0 0.5rem 0; color: #2c3e50;"> Participant Dashboard</h1>
                    <h2 style="margin: 0; color: #7f8c8d; font-size: 1.3rem; font-weight: 500;">{{ $event->title }}</h2>
                </div>

            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1.5rem;">
                <div
                    style="background: linear-gradient(180deg, #1e803e 0%, #009a7e 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Your Role</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">👤 Participant</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Registration</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">✓ Confirmed</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #ed6f95 0%, #840129 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Submission Status</div>
                    @php
                        $hasSubmission = $paper && $paper->title;
                    @endphp
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">
                        {{ $hasSubmission ? '✓ Submitted' : '⏳ Pending' }}</div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div
                style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 1rem 1.5rem; border-radius: 6px; margin-bottom: 2rem; color: #721c24;">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <!-- Product Submission Section -->
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="margin: 0; color: #2c3e50; font-size: 1.3rem;">
                        @php
                            // Determine if Innovation event based on populated fields
                            $isInnovation = !empty($event->innovation_categories) || !empty($event->innovation_theme);
                        @endphp
                        {{ $isInnovation ? '🎨 Product Submission' : '📄 Paper Submission' }}
                    </h3>
                    @php
                        // Only innovation participants can edit submissions before deadline
                        // Conference participants cannot edit in participant dashboard
                        $canEdit = false;
                        if ($isInnovation && $event->end_date) {
                            $canEdit = now()->lte($event->end_date);
                        } elseif ($isInnovation) {
                            $canEdit = true;
                        }
                    @endphp
                </div>

                @if ($paper && $paper->title)
                    <!-- Display Submitted Product -->
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        
                        {{-- Paper Status Display for Conference Events --}}
                        @if(!$isInnovation && $registration->presentation_status)
                            @php
                                $statusConfig = [
                                    'selected' => [
                                        'bg' => 'linear-gradient(135deg, #27ae60 0%, #229954 100%)',
                                        'icon' => '✓',
                                        'title' => 'Paper Selected',
                                        'message' => 'Congratulations! Your paper has been selected for presentation.',
                                        'shadow' => 'rgba(39, 174, 96, 0.3)'
                                    ],
                                    'rejected' => [
                                        'bg' => 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)',
                                        'icon' => '✗',
                                        'title' => 'Paper Not Selected',
                                        'message' => 'Unfortunately, your paper was not selected for presentation at this time.',
                                        'shadow' => 'rgba(231, 76, 60, 0.3)'
                                    ],
                                    'pending' => [
                                        'bg' => 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                                        'icon' => '⏳',
                                        'title' => 'Under Review',
                                        'message' => 'Your paper is currently under review by the organizers.',
                                        'shadow' => 'rgba(243, 156, 18, 0.3)'
                                    ]
                                ];
                                
                                $status = strtolower($registration->presentation_status);
                                $config = $statusConfig[$status] ?? $statusConfig['pending'];
                            @endphp
                            
                            <div style="background: {{ $config['bg'] }}; color: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 4px 12px {{ $config['shadow'] }};">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="font-size: 2.5rem;">{{ $config['icon'] }}</div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.5rem 0; color: white; font-size: 1.1rem; font-weight: 700;">
                                            {{ $config['title'] }}
                                        </h4>
                                        <p style="margin: 0; font-size: 0.9rem; opacity: 0.95;">
                                            {{ $config['message'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 1rem 0; color: #2c3e50; font-size: 1.2rem;">{{ $paper->title }}</h4>

                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                                    @if ($paper->product_category)
                                        <span
                                            style="padding: 0.35rem 0.75rem; background: #e0e7ff; color: #3730a3; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                            {{ $paper->product_category }}
                                        </span>
                                    @endif
                                    @if ($paper->product_theme)
                                        <span
                                            style="padding: 0.35rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                            {{ $paper->product_theme }}
                                        </span>
                                    @endif
                                </div>

                                @if ($paper->abstract)
                                    <div style="margin-bottom: 1rem;">
                                        <h5
                                            style="margin: 0 0 0.5rem 0; color: #4b5563; font-size: 0.9rem; font-weight: 600;">
                                            Abstract:</h5>
                                        <p style="margin: 0; color: #6b7280; font-size: 0.9rem; line-height: 1.6;">
                                            {{ $paper->abstract }}</p>
                                    </div>
                                @endif

                                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1rem;">
                                    @php
                                        // Get the correct file path based on event type
                                        $filePath = $isInnovation ? $paper->poster_path : $paper->paper_path;
                                    @endphp
                                    
                                    @if ($filePath)
                                        <div style="width: 100%; margin-top: 1rem;">
                                            <h4 style="margin: 0 0 1rem 0; color: #2c3e50;">{{ $isInnovation ? '🎨 Poster Preview' : '📄 Paper Preview' }}</h4>
                                            
                                            @php
                                                $fileUrl = strtolower($filePath);
                                                $isPDF = str_contains($fileUrl, '.pdf');
                                                $isWordDoc = str_contains($fileUrl, '.doc') || str_contains($fileUrl, '.docx') || str_contains($fileUrl, '.tmp');
                                                $isImage = preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $fileUrl);
                                            @endphp
                                            
                                            <div style="border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: #f8f9fa;">
                                                @if($isImage || $isInnovation)
                                                    {{-- Display images directly --}}
                                                    <img src="{{ $filePath }}" alt="{{ $isInnovation ? 'Product Poster' : 'Paper' }}" 
                                                         style="width: 100%; height: auto; display: block;">
                                                @elseif($isPDF)
                                                    {{-- Display PDF in iframe --}}
                                                    <iframe src="{{ $filePath }}#toolbar=0&navpanes=0&scrollbar=1" 
                                                            style="width: 100%; height: 600px; border: none;">
                                                    </iframe>
                                                @elseif($isWordDoc)
                                                    {{-- Use Google Docs Viewer for Word documents --}}
                                                    <iframe src="https://docs.google.com/viewer?url={{ urlencode($filePath) }}&embedded=true" 
                                                            style="width: 100%; height: 600px; border: none;">
                                                    </iframe>
                                                @endif
                                            </div>
                                            
                                            {{-- Download button --}}
                                            <div style="margin-top: 0.75rem; text-align: center;">
                                                <a href="{{ $filePath }}" download 
                                                   style="display: inline-block; padding: 0.75rem 1.5rem; background: #3498db; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                                    📥 Download {{ $isInnovation ? 'Poster' : 'Paper' }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($paper->video_url)
                                        <a href="{{ $paper->video_url }}" target="_blank"
                                            style="padding: 0.5rem 1rem; background: #8b5cf6; color: white; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                            🎥 Watch Video
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($canEdit)
                            <div style="border-top: 2px solid #e5e7eb; margin-top: 1.5rem; padding-top: 1.5rem;">
                                <button type="button"
                                    onclick="document.getElementById('editSubmissionForm').style.display='block'; this.style.display='none';"
                                    style="padding: 0.75rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    ✏️ Edit Submission
                                </button>
                            </div>
                        @else
                            <div style="background: #fff3cd; padding: 1rem; border-radius: 6px; margin-top: 1rem;">
                                <p style="margin: 0; color: #856404; font-size: 0.85rem;">🔒 Submission deadline has passed.
                                    Editing is no longer available.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Edit Submission Form (Hidden by default) -->
                    <div id="editSubmissionForm" style="display: none;">
                        <form method="POST" action="{{ route('event.paper.update', [$event, $registration]) }}"
                            enctype="multipart/form-data" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                            @csrf
                            <h4 style="margin: 0 0 1rem 0; color: #2c3e50;">Edit Your Submission</h4>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Title
                                    *</label>
                                <input type="text" name="title" value="{{ $paper->title }}" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Category
                                    *</label>
                                <select name="product_category" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}"
                                            {{ $paper->product_category === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($isInnovation)
                                <div style="margin-bottom: 1rem;">
                                    <label
                                        style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Theme
                                        *</label>
                                    <select name="product_theme" required
                                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                        <option value="">-- Select Theme --</option>
                                        @foreach ($themes as $theme)
                                            <option value="{{ $theme }}"
                                                {{ $paper->product_theme === $theme ? 'selected' : '' }}>
                                                {{ $theme }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Abstract
                                    *</label>
                                <textarea name="abstract" rows="4" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem; resize: vertical;">{{ $paper->abstract }}</textarea>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Update
                                    {{ $isInnovation ? 'Poster' : 'Paper' }} (Leave empty to keep current)</label>
                                <input type="file" name="poster" accept="{{ $isInnovation ? 'image/*' : '.doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf' }}"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                @if(!$isInnovation)
                                    <small style="color: #6c757d; font-size: 0.85rem;">Accepted formats: DOC, DOCX, PDF</small>
                                @endif
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Video
                                    URL</label>
                                <input type="url" name="video_url" value="{{ $paper->video_url }}"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;"
                                    placeholder="https://youtube.com/watch?v=...">
                            </div>

                            <div style="display: flex; gap: 0.75rem;">
                                <button type="submit"
                                    style="padding: 0.75rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    💾 Update Submission
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('editSubmissionForm').style.display='none';"
                                    style="padding: 0.75rem 1.5rem; background: #6b7280; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- No Submission Yet -->
                    <div style="background: #fff3cd; padding: 2rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
                        <h4 style="margin: 0 0 0.5rem 0; color: #856404;">No Submission Yet</h4>
                        <p style="margin: 0 0 1.5rem 0; color: #856404; font-size: 0.9rem;">Please submit your product
                            details below.</p>

                        @if ($canEdit)
                            <button type="button"
                                onclick="document.getElementById('newSubmissionForm').style.display='block'; this.parentElement.style.display='none';"
                                style="padding: 0.75rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                ➕ Create Submission
                            </button>
                        @else
                            <p style="margin: 0; color: #dc2626; font-weight: 600;">Submission deadline has passed.</p>
                        @endif
                    </div>

                    <!-- New Submission Form (Hidden by default) -->
                    <div id="newSubmissionForm" style="display: none;">
                        <form method="POST" action="{{ route('event.paper.update', [$event, $registration]) }}"
                            enctype="multipart/form-data"
                            style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                            @csrf
                            <h4 style="margin: 0 0 1rem 0; color: #2c3e50;">Create Your Submission</h4>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Title
                                    *</label>
                                <input type="text" name="title" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Category
                                    *</label>
                                <select name="product_category" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($isInnovation)
                                <div style="margin-bottom: 1rem;">
                                    <label
                                        style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Theme
                                        *</label>
                                    <select name="product_theme" required
                                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                        <option value="">-- Select Theme --</option>
                                        @foreach ($themes as $theme)
                                            <option value="{{ $theme }}">{{ $theme }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Abstract
                                    *</label>
                                <textarea name="abstract" rows="4" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem; resize: vertical;"></textarea>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">{{ $isInnovation ? 'Poster' : 'Paper' }}
                                    *</label>
                                <input type="file" name="poster" accept="{{ $isInnovation ? 'image/*' : '.doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf' }}" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                @if(!$isInnovation)
                                    <small style="color: #6c757d; font-size: 0.85rem;">Accepted formats: DOC, DOCX, PDF</small>
                                @endif
                            </div>


                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Video
                                    URL</label>
                                <input type="url" name="video_url"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;"
                                    placeholder="https://youtube.com/watch?v=...">
                            </div>

                            <div style="display: flex; gap: 0.75rem;">
                                <button type="submit"
                                    style="padding: 0.75rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    💾 Submit
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('newSubmissionForm').style.display='none'; this.parentElement.parentElement.previousElementSibling.style.display='block';"
                                    style="padding: 0.75rem 1.5rem; background: #6b7280; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Presentation Details Section (Blocked Until Payment) -->
                @php
                    $requiresPayment = !$event->is_free && $event->registration_fee > 0;
                    $paymentStatus = $registration->payment_status ?? null;
                    $paymentApproved = $paymentStatus === 'approved';
                    $isPending = $paymentStatus === 'pending';
                    $isRejected = $paymentStatus === 'rejected';
                    $canViewPresentationDetails = !$requiresPayment || $paymentApproved;
                @endphp

                @if ($requiresPayment && !$paymentApproved && $paper && $paper->title)
                    @if ($isPending)
                        <div
                            style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 2rem; border-radius: 12px; text-align: center; color: white; margin-top: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
                            <h4 style="margin: 0 0 0.5rem 0; color: white; font-size: 1.2rem;">Payment Under Review</h4>
                            <p style="margin: 0; font-size: 0.95rem; opacity: 0.9;">
                                Your payment is being verified. Presentation details will be unlocked once approved.
                            </p>
                        </div>
                    @else
                        <div
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; text-align: center; color: white; margin-top: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🔒</div>
                            <h4 style="margin: 0 0 0.5rem 0; color: white; font-size: 1.2rem;">Presentation Details Locked
                            </h4>
                            <p style="margin: 0 0 1.5rem 0; font-size: 0.95rem; opacity: 0.9;">
                                Complete your payment to view presentation schedule, venue details, and event materials
                            </p>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#paymentModal"
                                style="padding: 0.75rem 1.5rem; background: white; color: #667eea; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: all 0.3s;">
                                💳 Make Payment Now
                            </button>
                        </div>
                    @endif
                @elseif($canViewPresentationDetails && $paper && $paper->title)
                    <!-- Presentation Details - Only shown after payment -->
                    <div style="border-top: 2px solid #e5e7eb; margin-top: 2rem; padding-top: 2rem;">
                        <h4 style="margin: 0 0 1rem 0; color: #2c3e50; font-size: 1.1rem;">📍 Presentation Details</h4>
                        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px;">
                            <div style="margin-bottom: 1rem;">
                                <strong style="color: #4b5563;">Presentation Date:</strong>
                                <span
                                    style="color: #6b7280;">{{ $event->start_date ? $event->start_date->format('M d, Y h:i A') : 'TBA' }}</span>
                            </div>
                            
                            @if(in_array($event->delivery_mode, ['online', 'hybrid']) && $event->online_platform_url)
                                <div style="margin-bottom: 1rem;">
                                    <strong style="color: #4b5563;">Online Platform:</strong>
                                    <a href="{{ $event->online_platform_url }}" target="_blank" 
                                       style="color: #3498db; text-decoration: underline;">{{ $event->online_platform_url }}</a>
                                </div>
                            @endif
                            
                            @if(in_array($event->delivery_mode, ['face_to_face', 'hybrid']))
                                <div style="margin-bottom: 1rem;">
                                    <strong style="color: #4b5563;">Venue:</strong>
                                    <span style="color: #6b7280;">{{ $event->venue_name ?? 'TBA' }}</span>
                                </div>
                                @if ($event->venue_address)
                                    <div style="margin-bottom: 1rem;">
                                        <strong style="color: #4b5563;">Address:</strong>
                                        <span style="color: #6b7280;">{{ $event->venue_address }}</span>
                                    </div>
                                @endif
                            @endif
                            <div
                                style="background: #e0f2fe; padding: 1rem; border-radius: 6px; border-left: 4px solid #0284c7; margin-top: 1rem;">
                                <p style="margin: 0; color: #075985; font-size: 0.9rem;">
                                    ℹ️ Please arrive 15 minutes before your scheduled presentation time. Bring a valid ID
                                    for verification.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment Section moved to sidebar -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.1rem;">Payment</h3>

                    @php
                        // Use $isPaid alias for better readability in this section
                        $isPaid = $paymentApproved;
                    @endphp

                    @if ($requiresPayment)
                        @if ($isPaid)
                            <div style="background: #d4edda; padding: 1.5rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✓</div>
                                <h4 style="margin: 0 0 0.5rem 0; color: #155724; font-size: 1rem;">Payment Approved</h4>
                                <p style="margin: 0; font-size: 0.85rem; color: #155724;">
                                    Approved on
                                    {{ $registration->payment_approved_at ? \Carbon\Carbon::parse($registration->payment_approved_at)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                        @elseif($isPending)
                            <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⏳</div>
                                <h4 style="margin: 0 0 0.5rem 0; color: #856404; font-size: 1rem;">Payment Pending Review
                                </h4>
                                <p style="margin: 0; font-size: 0.85rem; color: #856404;">
                                    Your payment receipt is being reviewed by the event organizer
                                </p>
                                @if ($registration->payment_receipt_path)
                                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #ffc107;">
                                        <a href="{{ $registration->payment_receipt_path }}" target="_blank"
                                            style="display: inline-block; padding: 0.5rem 1rem; background: white; color: #856404; text-decoration: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 2px solid #ffc107;">
                                            📄 View Submitted Receipt
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @elseif($isRejected)
                            <div style="background: #f8d7da; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem;">
                                <div style="text-align: center; margin-bottom: 1rem;">
                                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✗</div>
                                    <h4 style="margin: 0 0 0.5rem 0; color: #721c24; font-size: 1rem;">Payment Rejected
                                    </h4>
                                    @if ($registration->payment_notes)
                                        <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: #721c24;">
                                            <strong>Reason:</strong> {{ $registration->payment_notes }}
                                        </p>
                                        <br>
                                        <p style="margin: 0 0 0.5rem 0; color: #721c24; font-size: 0.8rem;">You must resubmit your payment to proceed with your registration.</p>
                                    @endif
                                </div>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#paymentModal"
                                    style="width: 100%; padding: 0.75rem; background: #dc3545; color: white; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                                    🔄 Resubmit Payment
                                </button>
                            </div>
                        @else
                            <div style="background: #fff3cd; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                                <p style="margin: 0; color: #856404; font-size: 0.85rem;">
                                    ⚠️ Payment Required
                                </p>
                            </div>

                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                                <div style="font-weight: 600; color: #2c3e50; margin-bottom: 0.5rem; font-size: 0.9rem;">
                                    Registration Fee</div>
                                <div style="font-size: 1.3rem; font-weight: bold; color: #2c3e50;">
                                    {{ $event->currency ?? 'RM' }} {{ number_format($event->registration_fee, 2) }}
                                </div>
                            </div>

                            <button type="button" data-bs-toggle="modal" data-bs-target="#paymentModal"
                                style="width: 100%; padding: 0.75rem; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                💳 Pay Now
                            </button>
                        @endif
                    @else
                        <div style="background: #d4edda; padding: 1.5rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎉</div>
                            <h4 style="margin: 0; color: #155724; font-size: 1rem;">Free Event</h4>
                        </div>
                    @endif
                </div>

                {{-- Awards Section - Display participant awards --}}
                @php
                    // Check if participant has received any awards
                    $participantAwards = collect();
                    $showAwardSection = false;
                    
                    // Check if the tables exist before querying
                    if (\Illuminate\Support\Facades\Schema::hasTable('participant_awards') && 
                        \Illuminate\Support\Facades\Schema::hasTable('event_awards')) {
                        $participantAwards = \DB::table('participant_awards as pa')
                            ->join('event_awards as ea', 'pa.event_award_id', '=', 'ea.id')
                            ->where('pa.registration_id', $registration->id)
                            ->where('pa.event_id', $event->id)
                            ->select(
                                'ea.id as award_id',
                                'ea.name as award_name',
                                'ea.display_name',
                                'ea.award_type',
                                'ea.description',
                                'ea.color',
                                'pa.rank',
                                'pa.final_score',
                                'pa.created_at as awarded_at'
                            )
                            ->orderBy('pa.created_at', 'desc')
                            ->get();
                    }
                    
                    $showAwardSection = $participantAwards->count() > 0;
                @endphp

                @if($showAwardSection)
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.1rem; text-align: center;">🏆 Awards</h3>
                        
                        @foreach($participantAwards as $award)
                            @php
                                // Determine background color based on award type or custom color
                                $bgColor = '#d4af37'; // Default gold
                                $shadowColor = 'rgba(212, 175, 55, 0.3)';
                                
                                // Use custom color if provided
                                if (!empty($award->color)) {
                                    $bgColor = 'linear-gradient(135deg, ' . $award->color . ' 0%, ' . $award->color . 'cc 100%)';
                                    $shadowColor = $award->color . '4d';
                                } elseif(str_contains(strtolower($award->award_type ?? ''), 'silver') || str_contains(strtolower($award->award_name ?? ''), 'silver')) {
                                    $bgColor = 'linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%)';
                                    $shadowColor = 'rgba(192, 192, 192, 0.3)';
                                } elseif(str_contains(strtolower($award->award_type ?? ''), 'gold') || str_contains(strtolower($award->award_name ?? ''), 'gold')) {
                                    $bgColor = 'linear-gradient(135deg, #ffd700 0%, #d4af37 100%)';
                                    $shadowColor = 'rgba(255, 215, 0, 0.3)';
                                } elseif(str_contains(strtolower($award->award_type ?? ''), 'bronze') || str_contains(strtolower($award->award_name ?? ''), 'bronze')) {
                                    $bgColor = 'linear-gradient(135deg, #cd7f32 0%, #b5651d 100%)';
                                    $shadowColor = 'rgba(205, 127, 50, 0.3)';
                                }
                                
                                // Display name or fallback to name
                                $displayName = $award->display_name ?? $award->award_name;
                            @endphp
                            
                            <div style="background: {{ $bgColor }}; padding: 1.5rem; border-radius: 8px; text-align: center; margin-bottom: 1rem; box-shadow: 0 4px 12px {{ $shadowColor }};">
                                <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                                    @if(str_contains(strtolower($award->award_type ?? ''), 'gold') || str_contains(strtolower($award->award_name), 'gold'))
                                        🥇
                                    @elseif(str_contains(strtolower($award->award_type ?? ''), 'silver') || str_contains(strtolower($award->award_name), 'silver'))
                                        🥈
                                    @elseif(str_contains(strtolower($award->award_type ?? ''), 'bronze') || str_contains(strtolower($award->award_name), 'bronze'))
                                        🥉
                                    @elseif(str_contains(strtolower($award->award_type ?? ''), 'excellence') || str_contains(strtolower($award->award_name), 'excellence'))
                                        ⭐
                                    @else
                                        🏆
                                    @endif
                                </div>
                                <h4 style="margin: 0 0 0.5rem 0; color: #1a1a1a; font-size: 1.1rem; font-weight: 700; text-shadow: 0 1px 2px rgba(255,255,255,0.3);">
                                    {{ $displayName }}
                                </h4>
                                @if($award->award_type)
                                    <p style="margin: 0 0 0.5rem 0; color: #2c2c2c; font-size: 0.85rem; font-weight: 600;">
                                        {{ ucfirst($award->award_type) }}
                                    </p>
                                @endif
                                @if($award->description)
                                    <p style="margin: 0 0 0.5rem 0; color: #2c2c2c; font-size: 0.85rem;">
                                        {{ $award->description }}
                                    </p>
                                @endif
                                {{-- @if($award->final_score)
                                    <p style="margin: 0 0 0.5rem 0; color: #1a1a1a; font-size: 0.9rem; font-weight: 600;">
                                        Score: {{ number_format($award->final_score, 2) }}
                                    </p>
                                @endif --}}
                                <p style="margin: 0.5rem 0 0 0; color: #1a1a1a; font-size: 0.8rem;">
                                    Awarded on: {{ \Carbon\Carbon::parse($award->awarded_at)->format('M d, Y') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Attendance Section - Only for Conference Events -->
                @php
                    // Determine if Conference event based on populated fields
                    $isConference = !empty($event->conference_categories);
                    
                    // Define event timing variables (used by both conference and innovation participants)
                    $eventStarted = $event->start_date && now()->gte($event->start_date);
                    $eventEnded = $event->end_date && now()->gt($event->end_date);
                    $eventActive = $eventStarted && !$eventEnded;
                @endphp

                @if ($isConference)
                    <div
                        style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        @php
                            // QR code functionality temporarily disabled
                            $presentationQR = null;

                            // Check if jury has evaluated this participant
                            $juryEvaluations = \DB::table('jury_mappings')
                                ->where('participant_registration_id', $registration->id)
                                ->where('event_id', $event->id)
                                ->where('status', 'evaluated')
                                ->get();

                            $totalJuries = \DB::table('jury_mappings')
                                ->where('participant_registration_id', $registration->id)
                                ->where('event_id', $event->id)
                                ->count();

                            $evaluationsReceived = $juryEvaluations->count();
                        @endphp

                        <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.3rem; text-align: center;">Attendance
                        </h3>

                        @php
                            $hasFeedback = \App\Models\Feedback::where('event_registration_id', $registration->id)->exists();
                        @endphp

                        @if ($registration->checked_in_at)
                            <div style="background: #c8e6c9; padding: 1.5rem; border-radius: 8px; text-align: center;">
                                <p style="margin: 0; color: #1b5e20; font-weight: 600; font-size: 1.1rem;">✓ Checked In</p>
                                <p style="margin: 0.5rem 0 0 0; color: #2e7d32; font-size: 0.85rem;">
                                    {{ $registration->checked_in_at->format('M d, Y h:i A') }}</p>

                                @if ($eventEnded)
                                    @if($hasFeedback)
                                        <div style="background: #d4edda; padding: 1rem; border-radius: 8px; margin-top: 1rem; border-left: 4px solid #28a745;">
                                            <p style="margin: 0 0 0.5rem 0; color: #155724; font-weight: 600;">✓ Feedback Submitted</p>
                                            <a href="{{ route('feedback.show', $registration) }}"
                                                style="display: inline-block; padding: 0.75rem 1.5rem; background: #28a745; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                                                onmouseover="this.style.background='#218838'"
                                                onmouseout="this.style.background='#28a745'">
                                                📋 View Your Feedback
                                            </a>
                                        </div>
                                    @else
                                        <div style="background: #e8d9f7; padding: 1rem; border-radius: 8px; margin-top: 1rem; border-left: 4px solid #9b59b6;">
                                            <p style="margin: 0 0 0.5rem 0; color: #6c3483; font-weight: 600;">Share Your Experience</p>
                                            <p style="margin: 0 0 1rem 0; color: #7d3c98; font-size: 0.9rem;">Help us improve by providing feedback</p>
                                            <a href="{{ route('feedback.create', $registration) }}"
                                                style="display: inline-block; padding: 0.75rem 1.5rem; background: #9b59b6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                                                onmouseover="this.style.background='#8e44ad'"
                                                onmouseout="this.style.background='#9b59b6'">
                                                💬 Submit Event Feedback
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @elseif(!$eventStarted)
                            <div style="background: #fff3cd; padding: 2rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📅</div>
                                <p style="margin: 0; color: #856404; font-weight: 600;">Attendance check-in will be
                                    available
                                    during the event</p>
                                <p style="margin: 0.5rem 0 0 0; color: #856404; font-size: 0.85rem;">
                                    Event starts: {{ $event->start_date->format('M d, Y h:i A') }}</p>
                            </div>
                        @elseif($eventEnded)
                            <div style="background: #ffebee; padding: 2rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔒</div>
                                <p style="margin: 0; color: #c62828; font-weight: 600;">Attendance check-in is no longer
                                    available</p>
                                <p style="margin: 0.5rem 0 0 0; color: #d32f2f; font-size: 0.85rem;">
                                    Event ended: {{ $event->end_date->format('M d, Y h:i A') }}</p>
                            </div>
                        @else
                            <button id="manualCheckInBtn"
                                style="width: 100%; padding: 0.75rem; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                                ✓ Mark Attendance
                            </button>
                            <div id="checkInMessage"
                                style="display: none; margin-top: 0.75rem; padding: 0.75rem; border-radius: 6px; text-align: center;">
                            </div>
                    </div>
                @endif
                @endif

                <!-- Feedback Section - For Innovation Participants -->
                @if (!$isConference && $eventEnded && $paymentApproved)
                    @php
                        $hasFeedback = \App\Models\Feedback::where('event_registration_id', $registration->id)->exists();
                    @endphp
                    
                    <div
                        style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.3rem; text-align: center;">Event Feedback</h3>
                        
                        @if($hasFeedback)
                            <div style="background: #d4edda; padding: 1.5rem; border-radius: 8px; text-align: center; border-left: 4px solid #28a745;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✓</div>
                                <p style="margin: 0 0 0.5rem 0; color: #155724; font-weight: 600;">Feedback Submitted</p>
                                <p style="margin: 0 0 1.5rem 0; color: #1e7e34; font-size: 0.85rem;">
                                    Thank you for sharing your experience!
                                </p>
                                <a href="{{ route('feedback.show', $registration) }}"
                                    style="display: inline-block; padding: 0.75rem 1.5rem; background: #28a745; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                                    onmouseover="this.style.background='#218838'"
                                    onmouseout="this.style.background='#28a745'">
                                    📋 View Your Feedback
                                </a>
                            </div>
                        @else
                            <div style="background: #e8d9f7; padding: 1.5rem; border-radius: 8px; text-align: center; border-left: 4px solid #9b59b6;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💬</div>
                                <p style="margin: 0 0 0.5rem 0; color: #6c3483; font-weight: 600;">Share Your Experience</p>
                                <p style="margin: 0 0 1.5rem 0; color: #7d3c98; font-size: 0.85rem;">
                                    The event has ended. We'd love to hear your feedback!
                                </p>
                                <a href="{{ route('feedback.create', $registration) }}"
                                    style="display: inline-block; padding: 0.75rem 1.5rem; background: #9b59b6; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                                    onmouseover="this.style.background='#8e44ad'"
                                    onmouseout="this.style.background='#9b59b6'">
                                    💬 Submit Event Feedback
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Ensure Bootstrap Modal is properly initialized
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure the modal backdrop has proper z-index
            var paymentModal = document.getElementById('paymentModal');
            if (paymentModal && typeof bootstrap !== 'undefined') {
                // Initialize Bootstrap modal
                new bootstrap.Modal(paymentModal);
            }
        });

        document.getElementById('manualCheckInBtn')?.addEventListener('click', function() {
            const btn = this;
            const messageDiv = document.getElementById('checkInMessage');

            btn.disabled = true;
            btn.textContent = 'Processing...';
            btn.style.background = '#95a5a6';

            // Get fresh CSRF token
            fetch('/csrf-token')
                .then(response => response.json())
                .then(tokenData => {
                    const csrfToken = tokenData.token || '{{ csrf_token() }}';

                    return fetch('{{ route('event.check-in', [$event, $registration]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                })
                .catch(() => {
                    // If CSRF endpoint fails, use the existing token
                    return fetch('{{ route('event.check-in', [$event, $registration]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                })
                .then(response => {
                    if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    if (!response.ok) {
                        throw new Error('Server returned status ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        messageDiv.style.display = 'block';
                        messageDiv.style.background = '#d4edda';
                        messageDiv.style.color = '#155724';
                        messageDiv.textContent = '✓ ' + data.message + ' at ' + data.checked_in_at;

                        btn.style.background = '#27ae60';
                        btn.textContent = '✓ Checked In Successfully!';

                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        throw new Error(data.message || 'Check-in failed');
                    }
                })
                .catch(error => {
                    messageDiv.style.display = 'block';
                    messageDiv.style.background = '#f8d7da';
                    messageDiv.style.color = '#721c24';
                    messageDiv.textContent = '✗ ' + error.message;

                    btn.disabled = false;
                    btn.textContent = '✓ Mark Attendance Manually';
                    btn.style.background = '#27ae60';
                });
        });
    </script>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">💳 Event Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($isPending)
                        <div style="background: #fff3cd; padding: 2rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #856404;">Payment Already Submitted</h4>
                            <p style="margin: 0; color: #856404;">
                                Your payment receipt is currently under review. Please wait for the organizer to verify your
                                payment.
                            </p>
                            @if ($registration->payment_receipt_path)
                                <div style="margin-top: 1.5rem;">
                                    <a href="{{ $registration->payment_receipt_path }}" target="_blank"
                                        style="display: inline-block; padding: 0.75rem 1.5rem; background: #856404; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                        📄 View Submitted Receipt
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="payment-info-box">
                            <h5>{{ $event->title }}</h5>
                            <div class="payment-fee-display">
                                <span class="label">Registration Fee:</span>
                                <span class="amount">
                                    {{ $event->currency ?? 'RM' }} {{ number_format($event->registration_fee, 2) }}
                                </span>
                            </div>
                        </div>

                        @if ($paymentSettings)
                            {{-- Display Payment QR Code --}}
                            @if ($paymentSettings->qr_code_url)
                                <div class="payment-qr-section">
                                    <h5>Scan QR Code to Pay</h5>
                                    <img src="{{ $paymentSettings->qr_code_url }}" alt="Payment QR Code">
                                    <p>Scan this QR code with your banking app to complete the payment</p>
                                </div>
                            @endif

                            {{-- Display Bank Transfer Details --}}
                            @if ($paymentSettings->bank_name || $paymentSettings->account_number)
                                <div
                                    style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; margin: 1.5rem 0; border-left: 5px solid #28a745;">
                                    <h5 style="font-size: 1.1rem; color: #2c3e50; margin-bottom: 1rem; font-weight: 600;">
                                        🏦 Bank Transfer Details
                                    </h5>
                                    <div style="background: white; padding: 1.25rem; border-radius: 8px;">
                                        @if ($paymentSettings->bank_name)
                                            <div
                                                style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                                                <span style="color: #6c757d; font-weight: 600;">Bank Name:</span>
                                                <span
                                                    style="color: #2c3e50; font-weight: 600;">{{ $paymentSettings->bank_name }}</span>
                                            </div>
                                        @endif
                                        @if ($paymentSettings->account_number)
                                            <div
                                                style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                                                <span style="color: #6c757d; font-weight: 600;">Account Number:</span>
                                                <span
                                                    style="color: #2c3e50; font-weight: 600; font-family: monospace; font-size: 1.05rem;">{{ $paymentSettings->account_number }}</span>
                                            </div>
                                        @endif
                                        @if ($paymentSettings->account_holder_name)
                                            <div
                                                style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                                                <span style="color: #6c757d; font-weight: 600;">Account Holder:</span>
                                                <span
                                                    style="color: #2c3e50; font-weight: 600;">{{ $paymentSettings->account_holder_name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Display Payment Instructions --}}
                            {{-- @if ($paymentSettings->payment_instructions)
                                <div style="background: #e7f3ff; padding: 1.25rem; border-radius: 8px; margin: 1.5rem 0; border-left: 5px solid #0284c7;">
                                    <h5 style="font-size: 1rem; color: #0369a1; margin-bottom: 0.75rem; font-weight: 600;">
                                        📋 Payment Instructions
                                    </h5>
                                    <p style="margin: 0; color: #0c4a6e; font-size: 0.9rem; line-height: 1.6; white-space: pre-line;">{{ $paymentSettings->payment_instructions }}</p>
                                </div>
                            @endif --}}
                        @else
                            {{-- Fallback to old event payment_qr_code if payment_settings not configured --}}
                            @if ($event->payment_qr_code)
                                <div class="payment-qr-section">
                                    <h5>Scan QR Code to Pay</h5>
                                    <img src="{{ Storage::url($event->payment_qr_code) }}" alt="Payment QR Code">
                                    <p>Scan this QR code with your banking app to complete the payment</p>
                                </div>
                            @else
                                <div class="payment-warning-box">
                                    <p>⚠️ Payment information not available. Please contact the event organizer.</p>
                                </div>
                            @endif
                        @endif

                        <form action="{{ route('event.payment.submit', [$event, $registration]) }}" method="POST"
                            enctype="multipart/form-data" class="payment-upload-section">
                            @csrf
                            <h5>Upload Payment Receipt</h5>
                            <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 1.25rem;">
                                After completing the payment, please upload your payment receipt for verification.
                            </p>

                            <div class="mb-3">
                                <label for="paymentReceipt" class="form-label">
                                    Payment Receipt <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="file" class="form-control" id="paymentReceipt" name="payment_receipt"
                                    required accept="image/*,.pdf">
                                <div class="form-text">Accepted formats: JPG, PNG, PDF (Max 5MB)</div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill">
                                    📤 Submit Payment Proof
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
