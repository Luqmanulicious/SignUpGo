<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventPaper;use App\Services\CloudinaryService;use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventDashboardController extends Controller
{
    /**
     * Show the event dashboard based on user's role for this event.
     */
    public function show(Event $event, EventRegistration $registration)
    {
        // Refresh to get latest data from database
        $event = $event->fresh(['category', 'organizer']);
        $registration = $registration->fresh();
        
        // Verify the registration belongs to the authenticated user and the event
        if ($registration->user_id !== Auth::id() || $registration->event_id !== $event->id) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You are not registered for this event.');
        }

        // Check if registration is approved/confirmed
        if (!in_array($registration->status, ['approved', 'confirmed'])) {
            return redirect()->route('registrations.index')
                ->with('info', 'Your registration is still pending approval.');
        }

        // Route to appropriate dashboard based on role
        $role = $registration->role;

        switch ($role) {
            case 'jury':
                return view('event-dashboard.jury', compact('event', 'registration'));
            
            case 'participant':
                // Load participant's paper submission
                $paper = EventPaper::where('event_id', $event->id)
                    ->where('user_id', Auth::id())
                    ->with('category')
                    ->first();
                
                // Load paper categories for dropdown
                $paperCategories = \App\Models\PaperCategory::where('event_id', $event->id)
                    ->orderBy('name')
                    ->get();
                
                // Get themes based on event type
                $themes = [];
                if ($event->event_type === 'innovation') {
                    $themes = $event->innovation_theme ?? [];
                } elseif ($event->event_type === 'conference') {
                    $themes = $event->conference_categories ?? [];
                }
                    
                return view('event-dashboard.participant', compact('event', 'registration', 'paper', 'paperCategories', 'themes'));
            
            case 'reviewer':
                // Load assigned participants for this reviewer
                $assignedParticipants = DB::table('jury_mappings as jm')
                    ->join('event_registrations as participant', 'jm.participant_registration_id', '=', 'participant.id')
                    ->join('users as participant_user', 'participant.user_id', '=', 'participant_user.id')
                    ->leftJoin('event_papers as paper', function($join) use ($event) {
                        $join->on('participant.user_id', '=', 'paper.user_id')
                             ->where('paper.event_id', '=', $event->id);
                    })
                    ->where('jm.event_id', $event->id)
                    ->where('jm.reviewer_registration_id', $registration->id)
                    ->select(
                        'jm.id as mapping_id',
                        'jm.status as review_status',
                        'jm.score',
                        'jm.review_notes',
                        'jm.reviewed_at',
                        'participant.id as participant_registration_id',
                        'participant.registration_code as participant_code',
                        'participant_user.name as participant_name',
                        'participant_user.email as participant_email',
                        'paper.id as paper_id',
                        'paper.title as paper_title',
                        'paper.abstract as paper_abstract',
                        'paper.product_category',
                        'paper.product_theme',
                        'paper.poster_path',
                        'paper.video_url',
                        'paper.status as paper_status'
                    )
                    ->get();
                
                // Load rubric categories and items for evaluation
                $rubricCategories = DB::table('rubric_categories')
                    ->where('event_id', $event->id)
                    ->whereRaw('is_active = true')
                    ->orderBy('order')
                    ->get();
                
                // Load all rubric items for this event's categories
                $categoryIds = $rubricCategories->pluck('id');
                $rubricItems = DB::table('rubric_items')
                    ->whereIn('rubric_category_id', $categoryIds)
                    ->whereRaw('is_active = true')
                    ->orderBy('rubric_category_id')
                    ->orderBy('order')
                    ->get();
                
                // Group items by category for easier display
                $rubricsByCategory = $rubricItems->groupBy('rubric_category_id');
                
                // Load submitted scores for completed reviews
                $submittedScores = DB::table('rubric_item_scores as ris')
                    ->join('jury_mappings as jm', 'ris.jury_mapping_id', '=', 'jm.id')
                    ->join('rubric_items as ri', 'ris.rubric_item_id', '=', 'ri.id')
                    ->where('jm.event_id', $event->id)
                    ->where('jm.reviewer_registration_id', $registration->id)
                    ->where('jm.status', 'completed')
                    ->select(
                        'jm.id as mapping_id',
                        'ris.rubric_item_id',
                        'ris.score',
                        'ris.comment',
                        'ri.name as rubric_item_name',
                        'ri.max_score'
                    )
                    ->get()
                    ->groupBy('mapping_id');
                
                return view('event-dashboard.reviewer', compact('event', 'registration', 'assignedParticipants', 'rubricCategories', 'rubricItems', 'rubricsByCategory', 'submittedScores'));
            
            default:
                return redirect()->route('events.show', $event)
                    ->with('error', 'Invalid role for this event.');
        }
    }

    /**
     * Manual check-in for event attendance.
     */
    public function manualCheckIn(Event $event, EventRegistration $registration)
    {
        // Verify the registration belongs to the authenticated user and the event
        if ($registration->user_id !== Auth::id() || $registration->event_id !== $event->id) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found.'
            ], 404);
        }

        // Check if registration is approved/confirmed
        if (!in_array($registration->status, ['approved', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Registration is not approved yet.'
            ], 403);
        }

        // Check if already checked in
        if ($registration->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in at ' . $registration->checked_in_at->format('M d, Y h:i A')
            ], 400);
        }

        // Mark as checked in
        $registration->checked_in_at = Carbon::now();
        $registration->check_in_method = 'manual';
        $registration->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully checked in',
            'checked_in_at' => $registration->checked_in_at->format('M d, Y h:i A')
        ]);
    }

    /**
     * Update participant's paper submission.
     */
    public function updatePaper(Request $request, Event $event, EventRegistration $registration)
    {
        // Verify the registration belongs to the authenticated user
        if ($registration->user_id !== Auth::id() || $registration->event_id !== $event->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Verify user is a participant
        if ($registration->role !== 'participant') {
            return back()->with('error', 'Only participants can submit papers.');
        }

        // Find the paper
        $paper = EventPaper::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$paper) {
            return back()->with('error', 'Paper not found.');
        }

        // Only allow editing draft papers or submitted papers before deadline
        if ($paper->status === 'under_review' || $paper->status === 'approved' || $paper->status === 'rejected') {
            return back()->with('error', 'Cannot edit paper in ' . $paper->status . ' status.');
        }

        // Validate input
        $rules = [
            'paper_title' => 'required|string|max:255',
            'paper_abstract' => 'required|string|max:2000',
            'paper_poster' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'paper_video_url' => 'nullable|url|max:500',
            'paper_category_id' => 'nullable|exists:paper_categories,id',
        ];

        $validated = $request->validate($rules);

        // Handle poster upload if new file provided
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

        // Update paper fields
        $paper->title = $request->paper_title;
        $paper->abstract = $request->paper_abstract;
        $paper->video_url = $request->paper_video_url;
        // Note: paper_category_id is legacy, we now use product_category and product_theme strings
        $paper->paper_category_id = $request->paper_category_id;

        // Handle submission status
        if ($request->has('submit_paper') && $paper->status === 'draft') {
            $paper->status = 'submitted';
            $paper->submitted_at = now();
            $message = 'Paper submitted successfully!';
        } else {
            $message = 'Paper updated successfully!';
        }

        $paper->save();

        return back()->with('success', $message);
    }

    /**
     * Submit review for assigned participant.
     */
    public function submitReview(Request $request, Event $event, EventRegistration $registration)
    {
        // Verify the registration belongs to the authenticated user
        if ($registration->user_id !== Auth::id() || $registration->event_id !== $event->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Verify user is a reviewer
        if ($registration->role !== 'reviewer') {
            return response()->json([
                'success' => false,
                'message' => 'Only reviewers can submit reviews.'
            ], 403);
        }

        // Validate input
        $validated = $request->validate([
            'mapping_id' => 'required|exists:jury_mappings,id',
            'rubric_scores' => 'required|array',
            'rubric_scores.*' => 'required|integer|min:1|max:5',
            'rubric_comments' => 'nullable|array',
            'rubric_comments.*' => 'nullable|string|max:1000',
            'review_notes' => 'nullable|string|max:2000',
        ]);

        // Verify the mapping belongs to this reviewer and event
        $mapping = DB::table('jury_mappings')
            ->where('id', $validated['mapping_id'])
            ->where('event_id', $event->id)
            ->where('reviewer_registration_id', $registration->id)
            ->first();

        if (!$mapping) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found.'
            ], 404);
        }

        // Get participant's paper
        $participantReg = DB::table('event_registrations')
            ->where('id', $mapping->participant_registration_id)
            ->first();
            
        $paper = DB::table('event_papers')
            ->where('event_id', $event->id)
            ->where('user_id', $participantReg->user_id)
            ->first();

        // Calculate total score
        $totalScore = array_sum($validated['rubric_scores']);
        
        // Get max possible score from rubric items
        $rubricItemIds = array_keys($validated['rubric_scores']);
        $rubricItems = DB::table('rubric_items')
            ->whereIn('id', $rubricItemIds)
            ->get();
        $maxScore = $rubricItems->sum('max_score');
        
        // Convert to percentage (0-100)
        $percentageScore = $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;

        // Save individual rubric item scores
        foreach ($validated['rubric_scores'] as $rubricItemId => $score) {
            $rubricComment = $validated['rubric_comments'][$rubricItemId] ?? null;
            
            DB::table('rubric_item_scores')->updateOrInsert(
                [
                    'jury_mapping_id' => $mapping->id,
                    'rubric_item_id' => $rubricItemId,
                ],
                [
                    'event_paper_id' => $paper ? $paper->id : null,
                    'evaluator_id' => Auth::id(),
                    'score' => $score,
                    'comment' => $rubricComment,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Update the mapping with review
        DB::table('jury_mappings')
            ->where('id', $validated['mapping_id'])
            ->update([
                'status' => 'completed',
                'score' => $percentageScore,
                'review_notes' => $validated['review_notes'] ?? 'Evaluation completed based on rubric criteria.',
                'reviewed_at' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully!'
        ]);
    }
    
    /**
     * Show jury evaluation form for a specific participant
     */
    public function showJuryEvaluationForm($juryMappingId)
    {
        // Get the jury mapping with related data
        $juryMapping = DB::table('jury_mappings as jm')
            ->where('jm.id', $juryMappingId)
            ->first();
            
        if (!$juryMapping) {
            return redirect()->back()->with('error', 'Evaluation assignment not found.');
        }
        
        // Get jury registration and verify it belongs to current user
        $juryRegistration = EventRegistration::find($juryMapping->jury_registration_id);
        if (!$juryRegistration || $juryRegistration->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        // Get participant registration
        $participantRegistration = EventRegistration::find($juryMapping->participant_registration_id);
        
        // Get event
        $event = Event::find($juryMapping->event_id);
        
        // Get participant's paper
        $paper = EventPaper::where('event_id', $event->id)
            ->where('user_id', $participantRegistration->user_id)
            ->first();
            
        if (!$paper) {
            return redirect()->back()->with('error', 'Participant has not submitted their work yet.');
        }
        
        // Get rubric categories for this event
        $rubricCategories = DB::table('rubric_categories')
            ->where('event_id', $event->id)
            ->whereRaw('is_active = true')
            ->orderBy('order')
            ->get();
        
        // Get rubric items grouped by category and load existing scores
        $existingScores = [];
        $categoryComments = [];
        
        foreach ($rubricCategories as $category) {
            $category->items = DB::table('rubric_items')
                ->where('rubric_category_id', $category->id)
                ->whereRaw('is_active = true')
                ->orderBy('order')
                ->get();
            
            // Load existing scores for this mapping
            foreach ($category->items as $item) {
                $existingScore = DB::table('rubric_item_scores')
                    ->where('jury_mapping_id', $juryMappingId)
                    ->where('rubric_item_id', $item->id)
                    ->first();
                
                if ($existingScore) {
                    $existingScores[$item->id] = $existingScore->score;
                    // Get category comment (stored in comment field of any item in category)
                    if ($existingScore->comment && !isset($categoryComments[$category->id])) {
                        $categoryComments[$category->id] = $existingScore->comment;
                    }
                }
            }
        }
        
        return view('event-dashboard.jury-evaluate', compact(
            'juryMapping',
            'event',
            'paper',
            'rubricCategories',
            'juryRegistration',
            'existingScores',
            'categoryComments'
        ))->with('registration', $juryRegistration);
    }
    
    /**
     * Submit jury evaluation
     */
    public function submitJuryEvaluation(Request $request, $juryMappingId)
    {
        // Get the jury mapping
        $juryMapping = DB::table('jury_mappings')->where('id', $juryMappingId)->first();
        
        if (!$juryMapping) {
            return redirect()->back()->with('error', 'Evaluation assignment not found.');
        }
        
        // Verify authorization
        $juryRegistration = EventRegistration::find($juryMapping->jury_registration_id);
        if (!$juryRegistration || $juryRegistration->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        // Check if before deadline
        if ($juryRegistration->registration_deadline && now()->gt($juryRegistration->registration_deadline)) {
            return redirect()->back()->with('error', 'The evaluation deadline has passed.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0|max:5',
            'comments' => 'array',
            'comments.*' => 'nullable|string',
            'category_comments' => 'array',
            'category_comments.*' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Get participant's paper
            $participantRegistration = EventRegistration::find($juryMapping->participant_registration_id);
            $paper = EventPaper::where('event_id', $juryMapping->event_id)
                ->where('user_id', $participantRegistration->user_id)
                ->first();
            
            // Save individual rubric item scores with category comments
            $totalScore = 0;
            $maxScore = 0;
            
            // Group items by category to get overall comments
            $categoryItemsMap = [];
            foreach ($validated['scores'] as $rubricItemId => $score) {
                $rubricItem = DB::table('rubric_items')->where('id', $rubricItemId)->first();
                if ($rubricItem) {
                    $categoryItemsMap[$rubricItem->rubric_category_id][] = $rubricItemId;
                }
            }
            
            foreach ($validated['scores'] as $rubricItemId => $score) {
                $rubricItem = DB::table('rubric_items')->where('id', $rubricItemId)->first();
                
                if ($rubricItem) {
                    $totalScore += $score;
                    $maxScore += $rubricItem->max_score ?? 5;
                    
                    // Get overall category comment for this item's category
                    $categoryComment = $validated['category_comments'][$rubricItem->rubric_category_id] ?? null;
                    
                    // Check if record exists
                    $existing = DB::table('rubric_item_scores')
                        ->where('jury_mapping_id', $juryMappingId)
                        ->where('rubric_item_id', $rubricItemId)
                        ->first();
                    
                    if ($existing) {
                        // Update existing record
                        DB::table('rubric_item_scores')
                            ->where('jury_mapping_id', $juryMappingId)
                            ->where('rubric_item_id', $rubricItemId)
                            ->update([
                                'event_paper_id' => $paper ? $paper->id : null,
                                'evaluator_id' => Auth::id(),
                                'score' => $score,
                                'comment' => $categoryComment,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Insert new record
                        DB::table('rubric_item_scores')->insert([
                            'jury_mapping_id' => $juryMappingId,
                            'rubric_item_id' => $rubricItemId,
                            'event_paper_id' => $paper ? $paper->id : null,
                            'evaluator_id' => Auth::id(),
                            'score' => $score,
                            'comment' => $categoryComment,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            
            // Calculate percentage score
            $percentageScore = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;
            
            // Compile all comments into notes
            $allComments = [];
            if (!empty($validated['category_comments'])) {
                foreach ($validated['category_comments'] as $categoryId => $comment) {
                    if ($comment) {
                        $category = DB::table('rubric_categories')->where('id', $categoryId)->first();
                        $allComments[] = "{$category->name}: {$comment}";
                    }
                }
            }
            $notesText = !empty($allComments) ? implode("\n\n", $allComments) : 'Evaluation completed.';
            
            // Update jury mapping status
            DB::table('jury_mappings')
                ->where('id', $juryMappingId)
                ->update([
                    'status' => 'evaluated',
                    'notes' => $notesText,
                    'updated_at' => now(),
                ]);
            
            DB::commit();
            
            return redirect()->route('events.jury-dashboard', $juryRegistration->id)
                ->with('success', 'Evaluation submitted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit evaluation: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show jury dashboard (helper method for direct access)
     */
    public function juryDashboard(EventRegistration $registration)
    {
        // Verify the registration belongs to the authenticated user
        if ($registration->user_id !== Auth::id()) {
            return redirect()->route('events.index')
                ->with('error', 'Unauthorized access.');
        }
        
        // Verify the registration is for a jury role
        if ($registration->role !== 'jury') {
            return redirect()->route('events.index')
                ->with('error', 'This registration is not for a jury role.');
        }
        
        $event = Event::findOrFail($registration->event_id);
        
        return view('event-dashboard.jury', compact('event', 'registration'));
    }
}
