<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventPaper;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        
        // Check if registration is rejected or cancelled
        if (in_array($registration->status, ['rejected', 'cancelled'])) {
            return redirect()->route('registrations.index')
                ->with('error', 'Your registration has been rejected. You cannot access the event dashboard.');
        }
        
        // Check if presentation/paper is rejected (for conference participants)
        if ($registration->presentation_status === 'rejected') {
            return redirect()->route('registrations.index')
                ->with('error', 'Your paper/presentation was not selected. You cannot access the event dashboard.');
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
                
                // Get categories and themes based on which fields are populated
                $categories = [];
                $themes = [];
                
                // Check if innovation fields are populated (Innovation Event)
                if (!empty($event->innovation_categories) || !empty($event->innovation_theme)) {
                    // Innovation events have both categories and themes
                    $categories = $event->innovation_categories ?? [];
                    $themes = $event->innovation_theme ?? [];
                } 
                // Otherwise check conference fields (Conference Event)
                elseif (!empty($event->conference_categories)) {
                    // Conference events only have categories (no theme)
                    $categories = $event->conference_categories ?? [];
                }
                
                // Load payment settings for this event
                $paymentSettings = DB::table('payment_settings')
                    ->where('event_id', $event->id)
                    ->first();
                    
                return view('event-dashboard.participant', compact('event', 'registration', 'paper', 'paperCategories', 'categories', 'themes', 'paymentSettings'));
            
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
                    ->where('jm.jury_registration_id', $registration->id)
                    ->select(
                        'jm.id as mapping_id',
                        'jm.status as review_status',
                        'jm.notes as review_notes',
                        'jm.updated_at as reviewed_at',
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
                        'paper.paper_path',
                        'paper.paper_theme',
                        'paper.video_url',
                        'paper.status as paper_status'
                    )
                    ->get();
                
                // Calculate scores for each assignment and verify actual scores exist
                foreach ($assignedParticipants as $assignment) {
                    // Check if there are actual scores in rubric_item_scores table
                    $scores = DB::table('rubric_item_scores as ris')
                        ->join('rubric_items as ri', 'ris.rubric_item_id', '=', 'ri.id')
                        ->where('ris.jury_mapping_id', $assignment->mapping_id)
                        ->select('ris.score', 'ri.max_score')
                        ->get();
                    
                    if ($scores->isNotEmpty()) {
                        // Has actual scores - calculate percentage
                        $totalScore = $scores->sum('score');
                        $maxScore = $scores->sum('max_score');
                        $assignment->score = $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
                        
                        // Ensure status is completed
                        if ($assignment->review_status !== 'completed') {
                            DB::table('jury_mappings')
                                ->where('id', $assignment->mapping_id)
                                ->update(['status' => 'completed', 'updated_at' => now()]);
                            $assignment->review_status = 'completed';
                        }
                    } else {
                        // No actual scores - reset status to pending
                        $assignment->score = null;
                        if ($assignment->review_status === 'completed') {
                            DB::table('jury_mappings')
                                ->where('id', $assignment->mapping_id)
                                ->update(['status' => 'pending', 'notes' => null, 'updated_at' => now()]);
                            $assignment->review_status = 'pending';
                            $assignment->review_notes = null;
                        }
                    }
                }
                
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
                    ->where('jm.jury_registration_id', $registration->id)
                    ->where('jm.status', 'completed')
                    ->select(
                        'jm.id as mapping_id',
                        'ris.rubric_item_id',
                        'ris.score',
                        'ris.comment',
                        'ri.name as rubric_item_name',
                        'ri.max_score',
                        'ri.rubric_category_id'
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
        $isInnovation = !empty($event->innovation_categories) || !empty($event->innovation_theme);
        
        $rules = [
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|max:2000',
            'video_url' => 'nullable|url|max:500',
            'product_category' => $isInnovation ? 'required|string|max:255' : 'nullable|string|max:255',
            'product_theme' => 'nullable|string|max:255',
        ];
        
        // File validation based on event type
        if ($isInnovation) {
            $rules['poster'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'; // Images for innovation
        } else {
            $rules['poster'] = 'nullable|file|mimes:doc,docx,pdf|max:10240'; // Word/PDF for conference
        }

        $validated = $request->validate($rules);

        // Handle poster upload if new file provided
        if ($request->hasFile('poster')) {
            $cloudinary = new CloudinaryService();
            
            // Delete old poster if exists
            if ($paper->poster_path) {
                $cloudinary->deleteByUrl($paper->poster_path);
            }

            $result = $cloudinary->uploadPoster(
                $request->file('poster'),
                Auth::id(),
                $paper->event_id
            );
            $paper->poster_path = $result['secure_url'];
        }

        // Update paper fields
        $paper->title = $request->title;
        $paper->abstract = $request->abstract;
        $paper->video_url = $request->video_url;
        $paper->product_category = $request->product_category;
        $paper->product_theme = $request->product_theme;

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
     * Submit payment receipt for event registration.
     */
    public function submitPayment(Request $request, Event $event, EventRegistration $registration)
    {
        // Verify the registration belongs to the authenticated user
        if ($registration->user_id !== Auth::id() || $registration->event_id !== $event->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Validate payment receipt
        $request->validate([
            'payment_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        try {
            // Initialize Cloudinary service
            $cloudinary = new CloudinaryService();

            // Delete old payment receipt if exists and is being replaced
            if ($registration->payment_receipt_path) {
                try {
                    $cloudinary->deleteByUrl($registration->payment_receipt_path);
                } catch (\Exception $e) {
                    // Log but don't fail if deletion fails
                    \Log::warning('Failed to delete old payment receipt', [
                        'registration_id' => $registration->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Upload payment receipt to Cloudinary
            $result = $cloudinary->uploadPaymentReceipt(
                $request->file('payment_receipt'),
                $registration->id,
                $event->id
            );

            // Update registration with payment details
            $registration->payment_receipt_path = $result['secure_url'];
            $registration->payment_status = 'pending';
            $registration->payment_submitted_at = now();
            $registration->payment_notes = null; // Clear any previous rejection notes
            $registration->save();

            return back()->with('success', 'Payment receipt submitted successfully! Your payment is being reviewed by the event organizer.');
            
        } catch (\Exception $e) {
            \Log::error('Payment receipt upload failed', [
                'registration_id' => $registration->id,
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to upload payment receipt. Please try again.');
        }
    }

    /**
     * Submit review for assigned participant.
     */
    public function submitReview(Request $request, Event $event, EventRegistration $registration)
    {
        Log::info('Submit review started', [
            'event_id' => $event->id,
            'registration_id' => $registration->id,
            'auth_user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Verify the registration belongs to the authenticated user
        if ($registration->user_id !== Auth::id() || $registration->event_id !== $event->id) {
            Log::warning('Unauthorized review submission attempt');
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

        // Check if evaluations have been finalized/submitted
        if (!is_null($registration->evaluations_submitted_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Your evaluations have been finalized and can no longer be edited. Submitted on ' . 
                             \Carbon\Carbon::parse($registration->evaluations_submitted_at)->format('M d, Y h:i A')
            ], 403);
        }

        // Validate input with new structure
        $validated = $request->validate([
            'mapping_id' => 'required|exists:jury_mappings,id',
            'rubric_scores' => 'required|array',
            'rubric_scores.*' => 'required|integer|min:0',
            'category_comments' => 'nullable|array',
            'category_comments.*' => 'nullable|string|max:2000',
            'review_notes' => 'nullable|string|max:5000',
        ]);

        Log::info('Validation passed', ['validated' => $validated]);

        // Verify the mapping belongs to this reviewer and event
        $mapping = DB::table('jury_mappings')
            ->where('id', $validated['mapping_id'])
            ->where('event_id', $event->id)
            ->where('jury_registration_id', $registration->id)
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

        Log::info('Starting review save process', [
            'mapping_id' => $validated['mapping_id'],
            'rubric_scores_count' => count($validated['rubric_scores']),
            'has_paper' => !is_null($paper)
        ]);

        // Use transaction to ensure data integrity
        DB::beginTransaction();
        try {
            // Delete old scores if this mapping already has scores (update scenario)
            DB::table('rubric_item_scores')
                ->where('jury_mapping_id', $validated['mapping_id'])
                ->delete();

            // Loop through all rubric scores and save each one individually
            $totalScore = 0;
            $maxScore = 0;
            
            foreach ($validated['rubric_scores'] as $rubricItemId => $score) {
                $rubricItem = DB::table('rubric_items')->where('id', $rubricItemId)->first();
                
                if ($rubricItem) {
                    $totalScore += $score;
                    $maxScore += $rubricItem->max_score ?? 5;
                    
                    // Get category comment for this item's category
                    $categoryComment = $validated['category_comments'][$rubricItem->rubric_category_id] ?? null;
                    
                    DB::table('rubric_item_scores')->insert([
                        'jury_mapping_id' => $validated['mapping_id'],
                        'rubric_item_id' => $rubricItemId,
                        'event_paper_id' => $paper ? $paper->id : null,
                        'evaluator_id' => Auth::id(),
                        'score' => $score,
                        'comment' => $categoryComment,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    Log::info('Saved rubric score', [
                        'mapping_id' => $validated['mapping_id'],
                        'rubric_item_id' => $rubricItemId,
                        'score' => $score
                    ]);
                }
            }

            // Compile all category comments into notes
            $allComments = [];
            if (!empty($validated['category_comments'])) {
                foreach ($validated['category_comments'] as $categoryId => $comment) {
                    if (!empty(trim($comment))) {
                        $category = DB::table('rubric_categories')->where('id', $categoryId)->first();
                        if ($category) {
                            $allComments[] = "{$category->name}: {$comment}";
                        }
                    }
                }
            }
            
            // Add overall review notes if provided
            if (!empty($validated['review_notes'])) {
                $allComments[] = "Overall Notes: {$validated['review_notes']}";
            }
            
            $notesText = !empty($allComments) ? implode("\n\n", $allComments) : 'Evaluation completed.';

            // Update the jury mapping with review status
            DB::table('jury_mappings')
                ->where('id', $validated['mapping_id'])
                ->update([
                    'status' => 'completed',
                    'notes' => $notesText,
                    'updated_at' => now()
                ]);

            DB::commit();
            
            $percentageScore = $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
            
            Log::info('Review submitted successfully', [
                'mapping_id' => $validated['mapping_id'],
                'total_scores_saved' => count($validated['rubric_scores']),
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'percentage' => $percentageScore
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your evaluation has been saved successfully!',
                'scores_saved' => count($validated['rubric_scores']),
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'percentage' => $percentageScore
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to submit review', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_id' => $event->id,
                'registration_id' => $registration->id,
                'mapping_id' => $validated['mapping_id'] ?? null
            ]);
            
            // Determine specific error message
            $errorMessage = 'Unable to save your evaluation. Please try again.';
            
            if (str_contains($e->getMessage(), 'foreign key')) {
                $errorMessage = 'Invalid rubric item detected. Please contact the administrator.';
            } elseif (str_contains($e->getMessage(), 'duplicate')) {
                $errorMessage = 'This evaluation has already been submitted. Please refresh the page.';
            } elseif (str_contains($e->getMessage(), 'connection')) {
                $errorMessage = 'Database connection error. Please check your internet connection.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get existing review scores for editing
     */
    public function getReviewScores(Event $event, $mappingId)
    {
        // Verify the mapping exists and belongs to authenticated user
        $mapping = DB::table('jury_mappings as jm')
            ->join('event_registrations as er', 'jm.jury_registration_id', '=', 'er.id')
            ->where('jm.id', $mappingId)
            ->where('jm.event_id', $event->id)
            ->where('er.user_id', Auth::id())
            ->select('jm.*')
            ->first();

        if (!$mapping) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found or unauthorized.'
            ], 404);
        }

        // Get all scores for this mapping
        $scores = DB::table('rubric_item_scores as ris')
            ->where('jury_mapping_id', $mappingId)
            ->select('rubric_item_id', 'score', 'comment')
            ->get();

        // Parse review notes to extract overall notes
        $reviewNotes = '';
        if ($mapping->notes) {
            // Extract "Overall Notes:" part if exists
            if (strpos($mapping->notes, 'Overall Notes:') !== false) {
                $parts = explode('Overall Notes:', $mapping->notes, 2);
                $reviewNotes = trim($parts[1]);
            }
        }

        return response()->json([
            'success' => true,
            'scores' => $scores,
            'review_notes' => $reviewNotes
        ]);
    }
    
    /**
     * Get existing review for editing
     */
    public function getReview(Request $request, Event $event, $mappingId)
    {
        // Verify the mapping exists and belongs to this reviewer
        $mapping = DB::table('jury_mappings')
            ->where('id', $mappingId)
            ->where('event_id', $event->id)
            ->first();

        if (!$mapping) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found.'
            ], 404);
        }

        // Get rubric item scores
        $scores = DB::table('rubric_item_scores')
            ->where('jury_mapping_id', $mappingId)
            ->get()
            ->pluck('score', 'rubric_item_id')
            ->toArray();
            
        // Get rubric item comments
        $comments = DB::table('rubric_item_scores')
            ->where('jury_mapping_id', $mappingId)
            ->whereNotNull('comment')
            ->get()
            ->pluck('comment', 'rubric_item_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'scores' => $scores,
            'comments' => $comments,
            'review_notes' => $mapping->review_notes
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
        $overallComments = '';
        
        // Extract overall comments from notes if they exist
        if ($juryMapping->notes) {
            if (preg_match('/Overall Comments: (.+?)(?=\n\n|$)/s', $juryMapping->notes, $matches)) {
                $overallComments = trim($matches[1]);
            }
        }
        
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
            'categoryComments',
            'overallComments'
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
        
        // Check if evaluations have been submitted (read-only mode)
        if ($juryRegistration && !is_null($juryRegistration->evaluations_submitted_at)) {
            return redirect()->back()->with('error', 'Evaluations have been finalized and cannot be modified.');
        }
        if (!$juryRegistration || $juryRegistration->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        // Check if before deadline
        if ($juryRegistration->registration_deadline && now()->gt($juryRegistration->registration_deadline)) {
            return redirect()->back()->with('error', 'The evaluation deadline has passed.');
        }
        
        // Validate the request - basic validation first
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|integer|min:0',
            'comments' => 'array',
            'comments.*' => 'nullable|string',
            'category_comments' => 'array',
            'category_comments.*' => 'nullable|string',
            'overall_comments' => 'nullable|string',
        ]);
        
        // Custom validation: check each score against its rubric item's max_score
        foreach ($validated['scores'] as $rubricItemId => $score) {
            $rubricItem = DB::table('rubric_items')->where('id', $rubricItemId)->first();
            if ($rubricItem) {
                $maxScore = $rubricItem->max_score ?? 5;
                if ($score > $maxScore) {
                    return redirect()->back()
                        ->with('error', "Score for '{$rubricItem->name}' cannot exceed {$maxScore}.")
                        ->withInput();
                }
            }
        }
        
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
            
            // Add overall comments if provided
            if (!empty($validated['overall_comments'])) {
                $allComments[] = "Overall Comments: {$validated['overall_comments']}";
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
                ->with('success', 'Evaluation saved successfully! You can edit this later until you submit all evaluations.');
                
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

    /**
     * Submit all jury evaluations (final confirmation)
     */
    public function submitAllJuryEvaluations(Request $request, EventRegistration $registration)
    {
        try {
            // Verify the registration belongs to the authenticated user
            if ($registration->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
            
            // Verify the registration is for a jury role
            if ($registration->role !== 'jury') {
                return response()->json([
                    'success' => false,
                    'message' => 'This registration is not for a jury role.'
                ], 403);
            }

            // Get all assigned mappings for this jury
            $assignedMappings = DB::table('jury_mappings')
                ->where('jury_registration_id', $registration->id)
                ->where('event_id', $registration->event_id)
                ->get();
            
            $totalAssigned = $assignedMappings->count();
            
            // Count completed evaluations
            $evaluationsCompleted = DB::table('jury_mappings')
                ->where('jury_registration_id', $registration->id)
                ->where('event_id', $registration->event_id)
                ->where('status', 'evaluated')
                ->count();
            
            // Check if all evaluations are complete
            if ($evaluationsCompleted < $totalAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => "Please complete all evaluations before submitting. {$evaluationsCompleted}/{$totalAssigned} completed."
                ], 400);
            }

            // Update registration to mark evaluations as submitted
            DB::table('event_registrations')
                ->where('id', $registration->id)
                ->update([
                    'evaluations_submitted_at' => now(),
                    'updated_at' => now()
                ]);

            $event = Event::find($registration->event_id);
            DB::table('user_notifications')->insert([
                'user_id' => $event->organizer_id ?? 1, // Event organizer
                'title' => 'Jury Evaluations Submitted',
                'message' => "Jury member {$registration->user->name} has submitted all evaluations for {$event->title}.",
                'type' => 'eo_notification',
                'priority' => 'normal',
                'event_id' => $event->id,
                'is_read' => DB::raw('false'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All evaluations have been successfully submitted! The event organizer has been notified.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to submit jury evaluations: ' . $e->getMessage(), [
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'We encountered an issue while submitting your evaluations. Please try again in a moment. If the problem persists, please contact support.'
            ], 500);
        }
    }

    public function submitAllReviewerEvaluations(Request $request, EventRegistration $registration)
    {
        try {
            // Verify the registration belongs to the authenticated user
            if ($registration->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
            
            // Verify the registration is for a reviewer role
            if ($registration->role !== 'reviewer') {
                return response()->json([
                    'success' => false,
                    'message' => 'This registration is not for a reviewer role.'
                ], 403);
            }

            // Get all assigned mappings for this reviewer
            $assignedMappings = DB::table('jury_mappings')
                ->where('jury_registration_id', $registration->id)
                ->where('event_id', $registration->event_id)
                ->get();
            
            $totalAssigned = $assignedMappings->count();
            
            // Count completed evaluations (status = 'completed')
            $evaluationsCompleted = DB::table('jury_mappings')
                ->where('jury_registration_id', $registration->id)
                ->where('event_id', $registration->event_id)
                ->where('status', 'completed')
                ->count();
            
            // Check if all evaluations are complete
            if ($evaluationsCompleted < $totalAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => "Please complete all evaluations before submitting. {$evaluationsCompleted}/{$totalAssigned} completed."
                ], 400);
            }

            // Update registration to mark evaluations as submitted
            DB::table('event_registrations')
                ->where('id', $registration->id)
                ->update([
                    'evaluations_submitted_at' => now(),
                    'updated_at' => now()
                ]);

            // Create notification for event organizer
            $event = Event::find($registration->event_id);
            DB::table('user_notifications')->insert([
                'user_id' => $event->organizer_id ?? 1,
                'title' => 'Reviewer Evaluations Submitted',
                'message' => "Reviewer {$registration->user->name} has submitted all evaluations for {$event->title}.",
                'type' => 'eo_notification',
                'priority' => 'normal',
                'event_id' => $event->id,
                'is_read' => DB::raw('false'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All evaluations have been successfully submitted! The event organizer has been notified.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to submit reviewer evaluations: ' . $e->getMessage(), [
                'registration_id' => $registration->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'We encountered an issue while submitting your evaluations. Please try again in a moment. If the problem persists, please contact support.'
            ], 500);
        }
    }
}