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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
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
                    @if (Auth::user()->profile_picture)
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
                    <button type="submit" class="profile-dropdown-item logout"
                        style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-size: 1rem; font-family: inherit;">
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
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Event Registrations</h1>

            @if ($registrations->isEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No registrations yet</h3>
                    <p class="text-gray-600 mb-6">You haven't registered for any events.</p>
                    <a href="{{ route('events.index') }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Browse Events
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($registrations as $registration)
                        <div
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-xl font-semibold text-gray-900">
                                            {{ $registration->event->event_name }}
                                        </h3>

                                        <!-- Role Badge -->
                                        @if ($registration->role === 'participant')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                                Participant
                                            </span>
                                        @elseif($registration->role === 'jury')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                Jury
                                            </span>
                                        @elseif($registration->role === 'reviewer')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Reviewer
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-3">
                                        <div>
                                            <span class="font-medium">Date:</span>
                                            {{ \Carbon\Carbon::parse($registration->event->event_date)->format('M j, Y') }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Code:</span>
                                            <span class="font-mono">{{ $registration->registration_code }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Registered:</span>
                                            {{ $registration->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <!-- Status Badges -->
                                    <div class="flex gap-2 flex-wrap">
                                        @php
                                            $isConference =
                                                strtolower($registration->event->event_type) === 'conference';
                                            $isParticipant = $registration->role === 'participant';

                                            // Check if paper is assigned to reviewers
                                            $hasReviewerAssignment = false;
                                            if ($registration->paper) {
                                                $hasReviewerAssignment = \DB::table('jury_mappings')
                                                    ->where('participant_registration_id', $registration->id)
                                                    ->exists();
                                            }
                                        @endphp

                                        @if ($isConference && $isParticipant)
                                            <!-- Conference Participant Status -->
                                            @if ($registration->rejected_at)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    ❌ Rejected by EO
                                                </span>
                                            @elseif($registration->paper)
                                                @if ($registration->paper->status === 'approved')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✅ Paper Approved
                                                    </span>
                                                @elseif($registration->paper->status === 'rejected')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        ❌ Paper Rejected
                                                    </span>
                                                @elseif($registration->paper->status === 'under_review' || $hasReviewerAssignment)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        🔍 In Review
                                                    </span>
                                                @elseif($registration->paper->status === 'submitted')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        ⏱ Pending
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        📝 Paper Draft
                                                    </span>
                                                @endif
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    ⏱ Registration Pending
                                                </span>
                                            @endif
                                        @else
                                            <!-- Non-Conference or Jury Status -->
                                            @if ($registration->approved_at)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ✓ Approved
                                                </span>
                                            @elseif($registration->rejected_at)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    ✗ Rejected
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    ⏱ Pending
                                                </span>
                                            @endif
                                        @endif

                                        <!-- Check-In Status (for all event types) -->
                                        @if ($registration->checked_in_at)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                ✓ Checked In
                                            </span>
                                        @endif


                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-2 ml-4">
                                    <a href="{{ route('dashboard.registrations.show', $registration) }}"
                                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-200 text-center whitespace-nowrap">
                                        View Details
                                    </a>

                                    @php
                                        $eventEnded =
                                            $registration->event->end_date && now()->gt($registration->event->end_date);
                                    @endphp
                                    @if ($registration->checked_in_at && $eventEnded)
                                        <a href="{{ route('feedback.create', $registration) }}"
                                            class="inline-block bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-200 text-center whitespace-nowrap">
                                            💬 Feedback
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
