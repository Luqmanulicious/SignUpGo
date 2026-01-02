@extends('layouts.app')

@section('title', 'Participant Dashboard - ' . $event->title)

@section('styles')
    <style>
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
                            $isInnovation = stripos($event->event_type, 'innovation') !== false;
                        @endphp
                        {{ $isInnovation ? '🎨 Product Submission' : '📄 Paper Submission' }}
                    </h3>
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
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Category
                                    *</label>
                                <select name="product_category" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($paperCategories as $category)
                                        <option value="{{ $category->name }}"
                                            {{ $paper->product_category === $category->name ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

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
                                    style="display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 600; font-size: 0.9rem;">Category
                                    *</label>
                                <select name="product_category" required
                                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 0.9rem;">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($paperCategories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

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
                            <h4 style="margin: 0 0 0.5rem 0; color: white; font-size: 1.2rem;">Presentation Details Locked</h4>
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
                            <div style="margin-bottom: 1rem;">
                                <strong style="color: #4b5563;">Venue:</strong>
                                <span style="color: #6b7280;">{{ $event->venue_name ?? 'Online' }}</span>
                            </div>
                            @if ($event->venue_address)
                                <div style="margin-bottom: 1rem;">
                                    <strong style="color: #4b5563;">Address:</strong>
                                    <span style="color: #6b7280;">{{ $event->venue_address }}</span>
                                </div>
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

                <!-- Attendance Section - Only for Conference Events -->
                @php
                    $isConference = stripos($event->event_type ?? '', 'conference') !== false;
                @endphp

                @if ($isConference)
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
                            </div>
                        @else
                            <div
                                style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; text-align: center; margin-bottom: 1.5rem;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">⏳</div>
                                <p style="margin: 0; color: #856404;">QR code is being generated...</p>
                            </div>

                            <div style="border-top: 2px solid #e0e0e0; padding-top: 1.5rem; text-align: center;">
                                <p style="margin: 0 0 1rem 0; color: #2c3e50; font-weight: 600;">Manual Check-In</p>
                            </div>
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
                                Your payment receipt is currently under review. Please wait for the organizer to verify your payment.
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

                        @if ($event->payment_qr_code)
                            <div class="payment-qr-section">
                                <h5>Scan QR Code to Pay</h5>
                                <img src="{{ Storage::url($event->payment_qr_code) }}" alt="Payment QR Code">
                                <p>Scan this QR code with your banking app to complete the payment</p>
                            </div>
                        @else
                            <div class="payment-warning-box">
                                <p>⚠️ Payment QR code not available. Please contact the event organizer.</p>
                            </div>
                        @endif

                        <form action="{{ route('event.payment.submit', [$event, $registration]) }}"
                            method="POST" enctype="multipart/form-data" class="payment-upload-section">
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
