<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventPaper;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class RegistrationController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the registration form for an event.
     */
    public function create(Event $event, Request $request)
    {
        // Get fresh event data from database with paper categories
        $event = $event->fresh(['category', 'organizer', 'paperCategories']);
        
        // Check if event allows registration
        if (!$event->can_register) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Registration is not available for this event.');
        }

        // Get pre-selected role and mode from URL parameters
        $selectedRole = $request->input('role');
        $selectedMode = $request->input('mode'); // face_to_face or online
        $availableRoles = $event->available_roles;
        
        // Validate the selected role is available for this event
        if ($selectedRole && !in_array($selectedRole, $availableRoles)) {
            $selectedRole = null;
        }

        // Check if user is already registered for THIS SPECIFIC ROLE
        $existingRegistration = EventRegistration::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->where('role', $selectedRole)
            ->first();
        
        if ($existingRegistration) {
            return redirect()->route('events.show', $event)
                ->with('info', 'You are already registered as ' . ucfirst($selectedRole) . ' for this event.');
        }

        // Check if reviewer registration deadline has passed
        if ($selectedRole === 'reviewer') {
            $now = now();
            $deadlinePassed = false;
            $deadlineText = '';

            if ($event->delivery_mode === 'face_to_face' && $event->f2f_reviewer_registration_deadline) {
                if ($now->gt($event->f2f_reviewer_registration_deadline)) {
                    $deadlinePassed = true;
                    $deadlineText = $event->f2f_reviewer_registration_deadline->format('F d, Y h:i A');
                }
            } elseif ($event->delivery_mode === 'online' && $event->online_reviewer_registration_deadline) {
                if ($now->gt($event->online_reviewer_registration_deadline)) {
                    $deadlinePassed = true;
                    $deadlineText = $event->online_reviewer_registration_deadline->format('F d, Y h:i A');
                }
            } elseif ($event->delivery_mode === 'hybrid') {
                $f2fPassed = $event->f2f_reviewer_registration_deadline && $now->gt($event->f2f_reviewer_registration_deadline);
                $onlinePassed = $event->online_reviewer_registration_deadline && $now->gt($event->online_reviewer_registration_deadline);
                
                if ($f2fPassed && $onlinePassed) {
                    $deadlinePassed = true;
                    $deadlineText = 'both Face-to-Face and Online deadlines have passed';
                }
            } else {
                $f2fPassed = $event->f2f_reviewer_registration_deadline && $now->gt($event->f2f_reviewer_registration_deadline);
                $onlinePassed = $event->online_reviewer_registration_deadline && $now->gt($event->online_reviewer_registration_deadline);
                
                if ($f2fPassed || $onlinePassed) {
                    $deadlinePassed = true;
                    if ($f2fPassed && $event->f2f_reviewer_registration_deadline) {
                        $deadlineText = $event->f2f_reviewer_registration_deadline->format('F d, Y h:i A');
                    } elseif ($onlinePassed && $event->online_reviewer_registration_deadline) {
                        $deadlineText = $event->online_reviewer_registration_deadline->format('F d, Y h:i A');
                    }
                }
            }

            if ($deadlinePassed) {
                return redirect()->route('events.show', $event)
                    ->with('error', 'Reviewer registration deadline has passed (' . $deadlineText . '). Registration is no longer available.');
            }
        }

        // Check if jury registration deadline has passed
        if ($selectedRole === 'jury') {
            $now = now();
            $deadlinePassed = false;
            $deadlineText = '';

            if ($event->delivery_mode === 'face_to_face' && $event->f2f_jury_registration_deadline) {
                if ($now->gt($event->f2f_jury_registration_deadline)) {
                    $deadlinePassed = true;
                    $deadlineText = $event->f2f_jury_registration_deadline->format('F d, Y h:i A');
                }
            } elseif ($event->delivery_mode === 'online' && $event->online_jury_registration_deadline) {
                if ($now->gt($event->online_jury_registration_deadline)) {
                    $deadlinePassed = true;
                    $deadlineText = $event->online_jury_registration_deadline->format('F d, Y h:i A');
                }
            } elseif ($event->delivery_mode === 'hybrid') {
                $f2fPassed = $event->f2f_jury_registration_deadline && $now->gt($event->f2f_jury_registration_deadline);
                $onlinePassed = $event->online_jury_registration_deadline && $now->gt($event->online_jury_registration_deadline);
                
                if ($f2fPassed && $onlinePassed) {
                    $deadlinePassed = true;
                    $deadlineText = 'both Face-to-Face and Online deadlines have passed';
                }
            } else {
                $f2fPassed = $event->f2f_jury_registration_deadline && $now->gt($event->f2f_jury_registration_deadline);
                $onlinePassed = $event->online_jury_registration_deadline && $now->gt($event->online_jury_registration_deadline);
                
                if ($f2fPassed || $onlinePassed) {
                    $deadlinePassed = true;
                    if ($f2fPassed && $event->f2f_jury_registration_deadline) {
                        $deadlineText = $event->f2f_jury_registration_deadline->format('F d, Y h:i A');
                    } elseif ($onlinePassed && $event->online_jury_registration_deadline) {
                        $deadlineText = $event->online_jury_registration_deadline->format('F d, Y h:i A');
                    }
                }
            }

            if ($deadlinePassed) {
                return redirect()->route('events.show', $event)
                    ->with('error', 'Jury registration deadline has passed (' . $deadlineText . '). Registration is no longer available.');
            }
        }

        return view('registrations.create', compact('event', 'availableRoles', 'selectedRole', 'selectedMode'));
    }

    /**
     * Store a new registration.
     */
    public function store(Request $request, Event $event)
    {
        try {
            // Check if event allows registration
            if (!$event->can_register) {
                return back()->with('error', 'Registration is not available for this event.');
            }

            // Check if user is already registered for THIS SPECIFIC ROLE
            $existingRegistration = EventRegistration::where('user_id', Auth::id())
                ->where('event_id', $event->id)
                ->where('role', $request->role)
                ->first();
            
            if ($existingRegistration) {
                return back()->with('info', 'You are already registered as ' . ucfirst($request->role) . ' for this event.');
            }

            // Validate role
            $availableRoles = $event->available_roles;
            
            $rules = [
                'role' => ['required', 'string', 'in:' . implode(',', $availableRoles)],
            ];

            // Add paper submission validation for participant role
            if ($request->role === 'participant') {
                // For hybrid events, require attendance_mode for participants
                if ($event->delivery_mode === 'hybrid') {
                    $rules['attendance_mode'] = 'required|in:face_to_face,online';
                }
                
                $rules['paper_title'] = 'required|string|max:255';
                $rules['paper_abstract'] = 'required|string|max:2000';
                $rules['paper_poster'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240'; // 10MB max
                $rules['paper_video_url'] = 'nullable|url|max:500';
                $rules['paper_category'] = 'required|string|max:255';
                $rules['paper_theme'] = 'required|string|max:255';
            }

            // Add expertise validation for jury role
            if ($request->role === 'jury') {
                $rules['jury_categories'] = 'nullable|array|min:1';
                $rules['jury_categories.*'] = 'string|max:255';
                $rules['jury_themes'] = 'required|array|min:1';
                $rules['jury_themes.*'] = 'string|max:255';
            }

            // Add theme validation for reviewer role in conference events
            if ($request->role === 'reviewer' && stripos($event->event_type, 'conference') !== false) {
                $rules['reviewer_themes'] = 'required|array|min:1';
                $rules['reviewer_themes.*'] = 'string|max:255';
            }

            $customMessages = [
                'attendance_mode.required' => 'Please select how you will attend this hybrid event.',
                'attendance_mode.in' => 'Invalid attendance mode selected.',
                'paper_title.required' => 'Paper title is required for participants.',
                'paper_abstract.required' => 'Abstract is required for participants.',
                'paper_poster.required' => 'Poster file is required for participants.',
                'paper_poster.mimes' => 'Poster must be a JPG, JPEG, PNG, or PDF file.',
                'paper_poster.max' => 'Poster file size must not exceed 10MB.',
                'paper_video_url.url' => 'Please enter a valid URL for the video.',
                'paper_category.required' => 'Please select a product category.',
                'paper_theme.required' => 'Please select a product/paper theme.',
                'jury_categories.min' => 'Please click at least one category box to select your expertise area.',
                'jury_themes.required' => 'Please click at least one theme box - this is required to match you with appropriate submissions.',
                'jury_themes.min' => 'Please click at least one theme box to select your area of expertise.',
                'reviewer_themes.required' => 'Please select at least one theme - this is required to match you with appropriate submissions.',
                'reviewer_themes.min' => 'Please select at least one theme to indicate your area of expertise.',
            ];

            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Get user data from account
            $user = Auth::user();

            // For jury and reviewer roles, check if user has uploaded certificate in their account
            if (in_array($request->role, ['jury', 'reviewer']) && !$user->certificate_path) {
                return back()
                    ->with('error', 'Please upload your certification in Account Settings before registering as ' . ucfirst($request->role) . '.')
                    ->withInput();
            }

            // Check reviewer registration deadline
            if ($request->role === 'reviewer') {
                $now = now();
                $deadlinePassed = false;
                $deadlineText = '';

                if ($event->delivery_mode === 'face_to_face' && $event->f2f_reviewer_registration_deadline) {
                    if ($now->gt($event->f2f_reviewer_registration_deadline)) {
                        $deadlinePassed = true;
                        $deadlineText = $event->f2f_reviewer_registration_deadline->format('F d, Y h:i A');
                    }
                } elseif ($event->delivery_mode === 'online' && $event->online_reviewer_registration_deadline) {
                    if ($now->gt($event->online_reviewer_registration_deadline)) {
                        $deadlinePassed = true;
                        $deadlineText = $event->online_reviewer_registration_deadline->format('F d, Y h:i A');
                    }
                } elseif ($event->delivery_mode === 'hybrid') {
                    // For hybrid, check both deadlines
                    $f2fPassed = $event->f2f_reviewer_registration_deadline && $now->gt($event->f2f_reviewer_registration_deadline);
                    $onlinePassed = $event->online_reviewer_registration_deadline && $now->gt($event->online_reviewer_registration_deadline);
                    
                    if ($f2fPassed && $onlinePassed) {
                        $deadlinePassed = true;
                        $deadlineText = 'both Face-to-Face and Online deadlines have passed';
                    }
                } else {
                    // For events without delivery_mode, check both deadline fields
                    $f2fPassed = $event->f2f_reviewer_registration_deadline && $now->gt($event->f2f_reviewer_registration_deadline);
                    $onlinePassed = $event->online_reviewer_registration_deadline && $now->gt($event->online_reviewer_registration_deadline);
                    
                    if ($f2fPassed || $onlinePassed) {
                        $deadlinePassed = true;
                        if ($f2fPassed && $event->f2f_reviewer_registration_deadline) {
                            $deadlineText = $event->f2f_reviewer_registration_deadline->format('F d, Y h:i A');
                        } elseif ($onlinePassed && $event->online_reviewer_registration_deadline) {
                            $deadlineText = $event->online_reviewer_registration_deadline->format('F d, Y h:i A');
                        }
                    }
                }

                if ($deadlinePassed) {
                    return back()
                        ->with('error', 'Reviewer registration deadline has passed (' . $deadlineText . '). Registration is no longer available.')
                        ->withInput();
                }
            }

            // Check jury registration deadline
            if ($request->role === 'jury') {
                $now = now();
                $deadlinePassed = false;
                $deadlineText = '';

                if ($event->delivery_mode === 'face_to_face' && $event->f2f_jury_registration_deadline) {
                    if ($now->gt($event->f2f_jury_registration_deadline)) {
                        $deadlinePassed = true;
                        $deadlineText = $event->f2f_jury_registration_deadline->format('F d, Y h:i A');
                    }
                } elseif ($event->delivery_mode === 'online' && $event->online_jury_registration_deadline) {
                    if ($now->gt($event->online_jury_registration_deadline)) {
                        $deadlinePassed = true;
                        $deadlineText = $event->online_jury_registration_deadline->format('F d, Y h:i A');
                    }
                } elseif ($event->delivery_mode === 'hybrid') {
                    // For hybrid, check both deadlines
                    $f2fPassed = $event->f2f_jury_registration_deadline && $now->gt($event->f2f_jury_registration_deadline);
                    $onlinePassed = $event->online_jury_registration_deadline && $now->gt($event->online_jury_registration_deadline);
                    
                    if ($f2fPassed && $onlinePassed) {
                        $deadlinePassed = true;
                        $deadlineText = 'both Face-to-Face and Online deadlines have passed';
                    }
                } else {
                    // For events without delivery_mode, check both deadline fields
                    $f2fPassed = $event->f2f_jury_registration_deadline && $now->gt($event->f2f_jury_registration_deadline);
                    $onlinePassed = $event->online_jury_registration_deadline && $now->gt($event->online_jury_registration_deadline);
                    
                    if ($f2fPassed || $onlinePassed) {
                        $deadlinePassed = true;
                        if ($f2fPassed && $event->f2f_jury_registration_deadline) {
                            $deadlineText = $event->f2f_jury_registration_deadline->format('F d, Y h:i A');
                        } elseif ($onlinePassed && $event->online_jury_registration_deadline) {
                            $deadlineText = $event->online_jury_registration_deadline->format('F d, Y h:i A');
                        }
                    }
                }

                if ($deadlinePassed) {
                    return back()
                        ->with('error', 'Jury registration deadline has passed (' . $deadlineText . '). Registration is no longer available.')
                        ->withInput();
                }
            }

            // Determine initial status
            // All registrations need approval from EO (participants need paper approval)
            $status = 'pending';

            // Generate registration code
            $registrationCode = 'REG-' . strtoupper(substr(md5(uniqid()), 0, 8));

            // Create registration
            $registration = EventRegistration::create([
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'registration_code' => $registrationCode,
                'role' => $request->role,
                'attendance_mode' => $event->delivery_mode === 'hybrid' ? $request->attendance_mode : null,
                'status' => $status,
                'phone' => $user->phone,
                'organization' => $user->organization,
                'emergency_contact_name' => null,
                'emergency_contact_phone' => null,
                'certificate_path' => $user->certificate_path, // Pull from user account
                'certificate_filename' => $user->certificate_path ? basename($user->certificate_path) : null,
                'application_notes' => null,
                'approved_at' => null, // Will be set when EO approves
                'jury_categories' => $request->role === 'jury' ? $request->jury_categories : null,
                'jury_themes' => $request->role === 'jury' ? $request->jury_themes : ($request->role === 'reviewer' ? $request->reviewer_themes : null),
            ]);

            Log::info('Event registration created', [
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'role' => $request->role,
                'status' => $status,
            ]);

            // Handle paper submission for participants
            if ($request->role === 'participant') {
                $posterPath = null;
                
                // Upload poster file
                if ($request->hasFile('paper_poster')) {
                    $cloudinary = new CloudinaryService();
                    $result = $cloudinary->uploadPoster(
                        $request->file('paper_poster'),
                        Auth::id(),
                        $event->id
                    );
                    $posterPath = $result['secure_url'];
                    
                    Log::info('Paper poster uploaded', [
                        'user_id' => Auth::id(),
                        'event_id' => $event->id,
                        'url' => $posterPath,
                    ]);
                }

                // All papers are drafts by default and can be edited until deadline
                // Paper is submitted when EO approves the registration
                $paper = EventPaper::create([
                    'event_id' => $event->id,
                    'user_id' => Auth::id(),
                    'product_category' => $request->paper_category,
                    'product_theme' => $request->paper_theme,
                    'title' => $request->paper_title,
                    'abstract' => $request->paper_abstract,
                    'poster_path' => $posterPath,
                    'video_url' => $request->paper_video_url,
                    'status' => 'draft',
                    'submitted_at' => now(),
                ]);

                Log::info('Paper submission created', [
                    'paper_id' => $paper->id,
                    'user_id' => Auth::id(),
                    'event_id' => $event->id,
                    'status' => 'draft',
                ]);
            }

            // Show appropriate message - all registrations are pending
            $message = 'Your registration has been submitted successfully!';
            
            if ($request->role === 'participant') {
                $message .= ' Your paper is now under review by the event organizer. You can edit your paper anytime before the deadline.';
            } else {
                $roleLabel = ucfirst($request->role);
                $message = 'Your ' . $roleLabel . ' application has been submitted and is pending approval from the event organizer.';
            }
            
            return redirect()->route('registrations.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'event_id' => $event->id,
            ]);

            // Show detailed error message for debugging
            $errorMessage = 'Registration failed: ' . $e->getMessage();
            
            return back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Show user's registrations.
     */
    public function index()
    {
        $registrations = EventRegistration::where('user_id', Auth::id())
            ->with(['event', 'event.category'])
            ->latest()
            ->paginate(10);

        // Load papers manually for each registration
        foreach ($registrations as $registration) {
            $registration->paper = EventPaper::where('user_id', $registration->user_id)
                ->where('event_id', $registration->event_id)
                ->first();
        }

        return view('registrations.index', compact('registrations'));
    }

    /**
     * Edit registration (for draft papers only).
     */
    public function edit(EventRegistration $registration)
    {
        // Check if user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Allow editing for pending participants or pending jury members
        if ($registration->status !== 'pending') {
            return redirect()->route('registrations.index')
                ->with('error', 'This registration cannot be edited.');
        }

        if (!in_array($registration->role, ['participant', 'jury'])) {
            return redirect()->route('registrations.index')
                ->with('error', 'This registration cannot be edited.');
        }

        $event = $registration->event;
        
        // For jury members, no paper needed
        if ($registration->role === 'jury') {
            return view('registrations.edit', compact('registration', 'event'));
        }

        // For participants, get the paper submission
        $paper = EventPaper::where('user_id', $registration->user_id)
                          ->where('event_id', $registration->event_id)
                          ->first();

        if (!$paper) {
            return redirect()->route('registrations.index')
                ->with('error', 'Paper not found.');
        }
        
        // Check if past deadline
        $isPastDeadline = false;
        if ($event->delivery_mode === 'face_to_face' && $event->f2f_paper_deadline) {
            $isPastDeadline = now()->gt($event->f2f_paper_deadline);
        } elseif ($event->delivery_mode === 'online' && $event->online_paper_deadline) {
            $isPastDeadline = now()->gt($event->online_paper_deadline);
        } elseif ($event->delivery_mode === 'hybrid') {
            $f2fPast = $event->f2f_paper_deadline ? now()->gt($event->f2f_paper_deadline) : false;
            $onlinePast = $event->online_paper_deadline ? now()->gt($event->online_paper_deadline) : false;
            $isPastDeadline = $f2fPast && $onlinePast;
        }
        
        if ($isPastDeadline) {
            return redirect()->route('registrations.index')
                ->with('error', 'The submission deadline has passed. You can no longer edit your paper.');
        }
        
        return view('registrations.edit', compact('registration', 'event', 'paper'));
    }

    /**
     * Update registration (for draft papers only).
     */
    public function update(Request $request, EventRegistration $registration)
    {
        // Check if user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow editing pending registrations
        if ($registration->status !== 'pending') {
            return redirect()->route('registrations.index')
                ->with('error', 'This registration cannot be edited.');
        }

        // Handle jury expertise update
        if ($registration->role === 'jury') {
            return $this->updateJuryExpertise($request, $registration);
        }

        // Handle participant paper update
        if ($registration->role !== 'participant') {
            return redirect()->route('registrations.index')
                ->with('error', 'This registration cannot be edited.');
        }

        // Get the paper submission
        $paper = EventPaper::where('user_id', $registration->user_id)
                          ->where('event_id', $registration->event_id)
                          ->first();

        if (!$paper) {
            return redirect()->route('registrations.index')
                ->with('error', 'Paper not found.');
        }

        // Validate
        $rules = [
            'paper_title' => 'required|string|max:255',
            'paper_abstract' => 'required|string|max:2000',
            'paper_poster' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'paper_video_url' => 'nullable|url|max:500',
            'paper_category' => 'required|string|max:255',
            'paper_theme' => 'required|string|max:255',
        ];

        $customMessages = [
            'paper_title.required' => 'Paper title is required.',
            'paper_abstract.required' => 'Abstract is required.',
            'paper_poster.mimes' => 'Poster must be a JPG, JPEG, PNG, or PDF file.',
            'paper_poster.max' => 'Poster file size must not exceed 10MB.',
            'paper_video_url.url' => 'Please enter a valid URL for the video.',
            'paper_category.required' => 'Please select a product category.',
            'paper_theme.required' => 'Please select a product/paper theme.',
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update paper details
            $paper->title = $request->paper_title;
            $paper->abstract = $request->paper_abstract;
            $paper->video_url = $request->paper_video_url;
            $paper->product_category = $request->paper_category;
            $paper->product_theme = $request->paper_theme;

            // Handle new poster upload
            if ($request->hasFile('paper_poster')) {
                $cloudinary = new CloudinaryService();
                
                // Delete old poster if exists
                if ($paper->poster_path) {
                    $cloudinary->deleteByUrl($paper->poster_path);
                }

                $result = $cloudinary->uploadPoster(
                    $request->file('paper_poster'),
                    Auth::id(),
                    $paper->event_id
                );
                $paper->poster_path = $result['secure_url'];
            }

            // Paper remains as draft and can be edited until deadline
            // Status will be changed when EO approves
            $paper->status = 'draft';
            $paper->save();

            Log::info('Paper updated', [
                'paper_id' => $paper->id,
                'user_id' => Auth::id(),
                'status' => $paper->status,
            ]);

            return redirect()->route('registrations.index')
                ->with('success', 'Your paper has been updated successfully! You can continue editing until the deadline.');

        } catch (\Exception $e) {
            Log::error('Paper update failed', [
                'error' => $e->getMessage(),
                'paper_id' => $paper->id,
            ]);

            return back()
                ->with('error', 'Failed to update paper. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update jury member expertise.
     */
    private function updateJuryExpertise(Request $request, EventRegistration $registration)
    {
        // Debug: Log incoming request data
        Log::info('Jury update request received', [
            'all_data' => $request->all(),
            'jury_categories' => $request->jury_categories,
            'jury_themes' => $request->jury_themes,
            'registration_id' => $registration->id,
        ]);
        
        $event = $registration->event;
        $isInnovation = stripos($event->event_type, 'innovation') !== false;
        
        // Validate jury expertise
        $rules = [
            'jury_themes' => 'required|array|min:1',
            'application_notes' => 'nullable|string|max:1000',
        ];
        
        $customMessages = [
            'jury_themes.required' => 'Please select at least one theme - this is required to match you with appropriate submissions.',
            'jury_themes.min' => 'Please select at least one theme to indicate your area of expertise.',
            'application_notes.max' => 'Application notes cannot exceed 1000 characters.',
        ];
        
        // For innovation events, categories are also required
        if ($isInnovation) {
            $rules['jury_categories'] = 'required|array|min:1';
            $customMessages['jury_categories.required'] = 'Please select at least one category to indicate your expertise area.';
            $customMessages['jury_categories.min'] = 'Please select at least one category.';
        }
        
        $validator = Validator::make($request->all(), $rules, $customMessages);
        
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Update expertise and notes
            $registration->jury_categories = $isInnovation ? $request->jury_categories : null;
            $registration->jury_themes = $request->jury_themes;
            $registration->application_notes = $request->application_notes;
            $registration->save();
            
            Log::info('Jury registration updated', [
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'categories' => $registration->jury_categories,
                'themes' => $registration->jury_themes,
            ]);
            
            return redirect()->route('registrations.index')
                ->with('success', 'Your registration has been updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Jury registration update failed', [
                'error' => $e->getMessage(),
                'registration_id' => $registration->id,
            ]);
            
            return back()
                ->with('error', 'Failed to update registration. Please try again.')
                ->withInput();
        }
    }

    /**
     * Cancel a registration.
     */
    public function destroy(EventRegistration $registration)
    {
        // Check if user owns this registration
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Don't allow cancellation of approved jury registrations close to event date
        if ($registration->is_jury && $registration->is_approved) {
            $event = $registration->event;
            $daysTillEvent = now()->diffInDays($event->start_date, false);
            
            if ($daysTillEvent <= 7) {
                return redirect()->route('registrations.index')
                    ->with('error', 'Cannot cancel jury registration less than 7 days before the event.');
            }
        }

        try {
            // Delete certificate file if exists
            if ($registration->certificate_path) {
                Storage::disk('public')->delete($registration->certificate_path);
            }

            $eventTitle = $registration->event->title;
            $eventId = $registration->event_id;
            $registration->delete();

            Log::info('Registration cancelled', [
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'event_id' => $eventId,
            ]);

            return redirect()->route('registrations.index')
                ->with('success', 'Registration for "' . $eventTitle . '" has been cancelled.');

        } catch (\Exception $e) {
            Log::error('Registration cancellation failed', [
                'error' => $e->getMessage(),
                'registration_id' => $registration->id,
            ]);

            return redirect()->route('registrations.index')
                ->with('error', 'Failed to cancel registration. Please try again.');
        }
    }
}
