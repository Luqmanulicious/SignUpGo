# Feedback Access Implementation

## Overview
This document details the role-based feedback button access implementation for the SignUpGO event management system.

## Access Requirements by Role

### 1. Conference Participants
**Location**: `resources/views/event-dashboard/participant.blade.php` (Lines 721-726)

**Access Conditions**:
- ✅ Must have checked in (`$registration->checked_in_at` exists)
- ✅ Event must have ended (`$eventEnded`)
- ✅ Is a conference event (`$isConference`)

**Button Location**: Inside the Attendance section, after check-in confirmation

```blade
@if ($eventEnded)
    <a href="{{ route('feedback.create', $registration) }}" ...>
        💬 Submit Event Feedback
    </a>
@endif
```

---

### 2. Innovation Participants
**Location**: `resources/views/event-dashboard/participant.blade.php` (Lines 786-805)

**Access Conditions**:
- ✅ Payment must be approved (`$paymentApproved`)
- ✅ Event must have ended (`$eventEnded`)
- ✅ Is an innovation event (`!$isConference`)

**Button Location**: Separate feedback section in sidebar

```blade
@if (!$isConference && $eventEnded && $paymentApproved)
    <div style="background: white; padding: 2rem; ...">
        <h3>Event Feedback</h3>
        <a href="{{ route('feedback.create', $registration) }}" ...>
            💬 Submit Event Feedback
        </a>
    </div>
@endif
```

---

### 3. Jury Members
**Location**: `resources/views/event-dashboard/jury.blade.php` (Lines 397-401)

**Access Conditions**:
- ✅ Must have checked in (`$registration->checked_in_at` exists)
- ✅ Event must have ended (`$eventEnded`)

**Button Location**: Inside the Attendance section, after check-in confirmation

```blade
@if ($eventEnded)
    <a href="{{ route('feedback.create', $registration) }}" ...>
        💬 Submit Event Feedback
    </a>
@endif
```

---

### 4. Reviewers
**Location**: `resources/views/event-dashboard/reviewer.blade.php` (Lines 260-281)

**Access Conditions**:
- ✅ All assigned evaluations must be completed (`$allEvaluationsCompleted`)
- ✅ Event must have ended (`$eventEnded`)
- ✅ Must have at least one assigned participant

**Logic**:
```php
@php
    $eventEnded = \Carbon\Carbon::now()->isAfter($event->end_date);
    $allEvaluationsCompleted = $assignedParticipants->count() > 0 && 
        $assignedParticipants->where('review_status', 'completed')->count() === $assignedParticipants->count();
@endphp
```

**Button Location**: Separate feedback section after statistics grid

```blade
@if($eventEnded && $allEvaluationsCompleted)
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); ...">
        <h3>Share Your Feedback</h3>
        <p>You've completed all your evaluations! Help us improve by sharing your experience.</p>
        <a href="{{ route('feedback.create', $registration) }}" ...>
            💬 Submit Event Feedback
        </a>
    </div>
@endif
```

---

## Key Variables

### Event Status
- `$eventEnded` - Checks if current time is after event's end_date
- `$eventStarted` - Checks if current time is after event's start_date

### Event Type Detection
- `$isConference` - True if event has conference_categories populated
- Innovation events: Have innovation_categories and innovation_theme

### Role-Specific Variables

**Conference Participants & Jury:**
- `$registration->checked_in_at` - Timestamp of check-in (null if not checked in)

**Innovation Participants:**
- `$paymentApproved` - True if payment_status === 'approved'

**Reviewers:**
- `$assignedParticipants` - Collection of participants assigned to reviewer
- `review_status` - Status of each evaluation ('completed', 'pending', etc.)
- `$allEvaluationsCompleted` - Calculated boolean checking if all reviews are complete

---

## Testing Checklist

### Conference Participant
- [ ] Feedback button NOT visible before check-in
- [ ] Feedback button NOT visible before event ends
- [ ] Feedback button visible after check-in AND event ends

### Innovation Participant
- [ ] Feedback button NOT visible before payment approval
- [ ] Feedback button NOT visible before event ends
- [ ] Feedback button visible after payment approval AND event ends

### Jury Member
- [ ] Feedback button NOT visible before check-in
- [ ] Feedback button NOT visible before event ends
- [ ] Feedback button visible after check-in AND event ends

### Reviewer
- [ ] Feedback button NOT visible if any evaluations incomplete
- [ ] Feedback button NOT visible before event ends
- [ ] Feedback button visible after all evaluations complete AND event ends
- [ ] Feedback section shows correct completion message

---

## UI/UX Consistency

All feedback buttons use the same styling for consistency:
- **Button Color**: Green (`#27ae60`)
- **Hover Color**: Dark Green (`#229954`)
- **Icon**: 💬 emoji
- **Text**: "Submit Event Feedback"
- **Style**: Rounded corners, shadow, transition on hover

---

## Route Reference

All feedback buttons link to:
```blade
route('feedback.create', $registration)
```

This passes the `$registration` model to identify the user's specific event registration.

---

## Implementation Date
**Completed**: Current session
**Files Modified**: 
1. `resources/views/event-dashboard/participant.blade.php`
2. `resources/views/event-dashboard/jury.blade.php`
3. `resources/views/event-dashboard/reviewer.blade.php`

---

## Notes

- **No Check-in for Reviewers**: Reviewers don't need to check in, only complete evaluations
- **Event Type Matters**: Conference vs Innovation participants have different access logic
- **All Conditions Must Be Met**: Feedback is only available when ALL conditions for each role are satisfied
- **Graceful Degradation**: Buttons only appear when conditions are met; no error messages needed
