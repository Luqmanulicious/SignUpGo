@extends('layouts.app')

@section('title', 'Participant Dashboard - ' . $event->title)

@section('content')
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
                    style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Your Role</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">👤 Participant</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); padding: 1.5rem; border-radius: 8px; color: white;">
                    <div style="font-size: 0.9rem; opacity: 0.9;">Registration</div>
                    <div style="font-size: 1.8rem; font-weight: bold; margin-top: 0.5rem;">✓ Confirmed</div>
                </div>
                <div
                    style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 1.5rem; border-radius: 8px; color: white;">
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
                    <h3 style="margin: 0; color: #2c3e50; font-size: 1.3rem;"> Product Submission</h3>
                    @php
                        // Check if before submission deadline
                        $canEdit = true;
                        if ($event->end_date) {
                            $canEdit = now()->lte($event->end_date);
                        }
                    @endphp
                </div>

                @if ($paper && $paper->title)
                    <!-- Display Submitted Product -->
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
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
                                    @if ($paper->poster_path)
                                        <div class="poster-preview">
                                            <h4>Poster</h4>
                                            <img src="{{ $paper->poster_path }}" alt="Product Poster">
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
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Category *</label>
                                <select name="product_category" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Category --</option>
                                    @foreach($paperCategories as $category)
                                        <option value="{{ $category->name }}" {{ $paper->product_category === $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Theme *</label>
                                <select name="product_theme" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Theme --</option>
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme }}" {{ $paper->product_theme === $theme ? 'selected' : '' }}>
                                            {{ $theme }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

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
                                    Poster (Leave empty to keep current)</label>
                                <input type="file" name="poster" accept="image/*"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
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
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Category *</label>
                                <select name="product_category" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Category --</option>
                                    @foreach($paperCategories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Theme *</label>
                                <select name="product_theme" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Theme --</option>
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme }}">{{ $theme }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Abstract
                                    *</label>
                                <textarea name="abstract" rows="4" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem; resize: vertical;"></textarea>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Poster
                                    *</label>
                                <input type="file" name="poster" accept="image/*" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
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
            </div>

            <!-- Payment Section moved to sidebar -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2c3e50; font-size: 1.1rem;">Payment</h3>

                    @php
                        // Check if event requires payment
                        $requiresPayment = !$event->is_free && $event->registration_fee > 0;
                        // Check if already paid (you can add actual payment status check here)
                        $isPaid = false; // TODO: Check actual payment status from database
                    @endphp

                    @if ($requiresPayment)
                        @if ($isPaid)
                            <div style="background: #d4edda; padding: 1.5rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✓</div>
                                <h4 style="margin: 0 0 0.5rem 0; color: #155724; font-size: 1rem;">Payment Completed</h4>
                            </div>
                        @else
                            <div style="background: #fff3cd; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                                <p style="margin: 0; color: #856404; font-size: 0.85rem;">
                                    ⚠️ Payment Required
                                </p>
                            </div>

                            <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                                <div style="font-weight: 600; color: #2c3e50; margin-bottom: 0.5rem; font-size: 0.9rem;">
                                    Fee</div>
                                <div style="font-size: 1.3rem; font-weight: bold; color: #2c3e50;">
                                    {{ $event->currency ?? 'RM' }} {{ number_format($event->registration_fee, 2) }}
                                </div>
                            </div>

                            <button type="button"
                                style="width: 100%; padding: 0.75rem; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer;"
                                onclick="alert('Payment gateway integration coming soon')">
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

                <!-- Attendance Section -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    @php
                        $eventStarted = $event->start_date && now()->gte($event->start_date);
                        $eventEnded = $event->end_date && now()->gt($event->end_date);
                        $eventActive = $eventStarted && !$eventEnded;
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

                    @if ($registration->checked_in_at)
                        <div style="background: #c8e6c9; padding: 1.5rem; border-radius: 8px; text-align: center;">
                            <p style="margin: 0; color: #1b5e20; font-weight: 600; font-size: 1.1rem;">✓ Checked In</p>
                            <p style="margin: 0.5rem 0 0 0; color: #2e7d32; font-size: 0.85rem;">
                                {{ $registration->checked_in_at->format('M d, Y h:i A') }}</p>

                            @if ($eventEnded)
                                <a href="{{ route('feedback.create', $registration) }}"
                                    style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s;"
                                    onmouseover="this.style.background='#229954'"
                                    onmouseout="this.style.background='#27ae60'">
                                    💬 Submit Event Feedback
                                </a>
                            @endif
                        </div>
                    @elseif(!$eventStarted)
                        <div style="background: #fff3cd; padding: 2rem; border-radius: 8px; text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📅</div>
                            <p style="margin: 0; color: #856404; font-weight: 600;">Attendance check-in will be available
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
                        @if ($presentationQR)
                            <div style="text-align: center; margin-bottom: 1.5rem;">
                                <p style="margin: 0 0 1rem 0; color: #2c3e50; font-weight: 600;">Option 1: Scan QR Code</p>
                                <img src="{{ $presentationQR->qr_image_url }}" alt="Attendance QR Code"
                                    style="width: 100%; max-width: 250px; border: 4px solid #43e97b; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin-bottom: 1rem;">
                                <p style="margin: 0 0 0 0; color: #7f8c8d; font-size: 0.9rem;">Scan this QR code at the
                                    event venue</p>
                            </div>

                            <div
                                style="border-top: 2px solid #e0e0e0; padding-top: 1.5rem; margin-top: 1.5rem; text-align: center;">
                                <p style="margin: 0 0 1rem 0; color: #2c3e50; font-weight: 600;">Option 2: Manual Check-In
                                </p>
                            @else
                                <div
                                    style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; text-align: center; margin-bottom: 1.5rem;">
                                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⏳</div>
                                    <p style="margin: 0; color: #856404;">QR code is being generated...</p>
                                </div>

                                <div style="border-top: 2px solid #e0e0e0; padding-top: 1.5rem; text-align: center;">
                                    <p style="margin: 0 0 1rem 0; color: #2c3e50; font-weight: 600;">Manual Check-In</p>
                        @endif

                        <button id="manualCheckInBtn"
                            style="width: 100%; padding: 0.75rem; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                            ✓ Mark Attendance Manually
                        </button>
                        <div id="checkInMessage"
                            style="display: none; margin-top: 0.75rem; padding: 0.75rem; border-radius: 6px; text-align: center;">
                        </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
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
@endsection
