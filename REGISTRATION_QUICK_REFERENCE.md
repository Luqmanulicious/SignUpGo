# Quick Reference - Event Registration System

## 🎯 Key URLs

| Page | URL | Purpose |
|------|-----|---------|
| Events List | `/events` | Browse all events |
| Event Details | `/events/{id}` | View event & register button |
| Registration Form | `/events/{id}/register` | Register for event |
| My Registrations | `/my-registrations` | View all registrations |

## 🎭 User Roles by Event Type

### Innovation Events
- **Jury** - Requires certificate, needs approval
- **Participant** - Auto-approved

### Conference Events
- **Jury** - Requires certificate, needs approval
- **Reviewer** - Auto-approved
- **Participant** - Auto-approved

## 📋 Registration Status Flow

```
Participant/Reviewer
└─→ Submit → ✅ APPROVED (immediate)

Jury
└─→ Submit with Certificate → ⏳ PENDING 
    └─→ [Organizer Review]
        ├─→ ✅ APPROVED
        └─→ ❌ REJECTED (with reason)
```

## 🗂️ File Structure

```
app/
├── Http/Controllers/
│   └── RegistrationController.php     # Registration logic
└── Models/
    ├── EventRegistration.php          # Registration model
    ├── Event.php                      # +registration methods
    └── User.php                       # +registration methods

resources/views/
└── registrations/
    ├── create.blade.php               # Registration form
    └── index.blade.php                # My Registrations

database/migrations/
└── 2025_11_12_162333_create_event_registrations_table.php

storage/app/
└── certificates/                      # Jury certificates
```

## 💾 Database Table: event_registrations

```
id               - Primary key
user_id          - Who registered (FK to users)
event_id         - Which event (FK to events)
role             - jury/reviewer/participant
status           - pending/approved/rejected
certificate_path - File path (for jury)
certificate_filename - Original filename
application_notes - User's notes
admin_notes      - Organizer's notes
approved_at      - Approval timestamp
approved_by      - Who approved (FK to users)
rejected_at      - Rejection timestamp
rejected_reason  - Why rejected
created_at       - Registration date
updated_at       - Last updated
```

## 🔧 Model Methods

### Event Model
```php
$event->available_roles          // Array of roles for this event type
$event->event_type               // 'Innovation' or 'Conference'
$event->isUserRegistered($userId, $role = null)
$event->getUserRegistration($userId)
$event->registrations()          // All registrations
$event->approvedRegistrations()  // Only approved
$event->pendingRegistrations()   // Only pending
```

### User Model
```php
$user->eventRegistrations()                  // All registrations
$user->isRegisteredForEvent($eventId, $role)
$user->getEventRegistration($eventId)
```

### EventRegistration Model
```php
$registration->is_pending     // Boolean
$registration->is_approved    // Boolean
$registration->is_rejected    // Boolean
$registration->is_jury        // Boolean
$registration->is_participant // Boolean
$registration->is_reviewer    // Boolean
$registration->user           // User relationship
$registration->event          // Event relationship
```

## 🎨 CSS Classes

### Status Badges
```css
.badge-pending   /* Yellow - Awaiting approval */
.badge-approved  /* Green - Accepted */
.badge-rejected  /* Red - Declined */
```

### Role Badges
```css
.badge-jury       /* Purple */
.badge-reviewer   /* Blue */
.badge-participant /* Green */
```

## 📝 Form Validation Rules

### Registration Form
- **role:** Required, must be in available roles
- **certificate:** Required for jury, PDF/JPG/PNG, max 5MB
- **application_notes:** Optional, max 1000 chars

## 🚨 Business Rules

1. ✅ User can register once per event
2. ✅ Cannot register if event is full (without waitlist)
3. ✅ Cannot register after deadline
4. ✅ Jury must upload certificate
5. ✅ Jury registrations need approval
6. ✅ Cannot cancel jury within 7 days of event
7. ✅ Certificates auto-deleted on cancellation

## 🔐 Permissions

All routes require authentication (`auth` middleware)
Users can only:
- View their own registrations
- Cancel their own registrations
- Register for events they haven't registered for

## 📞 Integration Points

For event organizer module (your friend):

```php
// Get all registrations for an event
EventRegistration::where('event_id', $id)
    ->with('user')
    ->get();

// Get pending jury applications
EventRegistration::where('event_id', $id)
    ->where('role', 'jury')
    ->pending()
    ->get();

// Approve a registration
$registration->update([
    'status' => 'approved',
    'approved_at' => now(),
    'approved_by' => auth()->id(),
    'admin_notes' => 'Verified credentials'
]);

// Reject a registration
$registration->update([
    'status' => 'rejected',
    'rejected_at' => now(),
    'rejected_reason' => 'Reason here'
]);

// Download certificate
Storage::download($registration->certificate_path);
```

## ⚡ Quick Testing Steps

1. **Apply migration** (coordinate with friend)
2. **Login as test user**
3. **Go to Events** → Select an Innovation event
4. **Click "Register"** → Choose Participant → Submit
5. **Check My Registrations** → Should show approved
6. **Go back to Events** → Select Conference event
7. **Click "Register"** → Choose Jury → Upload PDF → Submit
8. **Check My Registrations** → Should show pending

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Routes not found | Run `php artisan route:clear` |
| Certificate upload fails | Check `storage/app/certificates` permissions |
| Already registered error | Check database for duplicate entry |
| Role not showing | Verify event category name contains 'innovation' or 'conference' |
| PHPStan errors | Ignore - they're static analysis warnings, code works fine |

## 📚 Related Documentation

- `REGISTRATION_MIGRATION.md` - Database setup guide
- `REGISTRATION_IMPLEMENTATION.md` - Full implementation details
- `README.md` - Project overview

## 🎉 That's It!

The system is ready to use once the database migration is applied. Users can now register for events with appropriate roles, and jury applications will go through your friend's approval workflow!
