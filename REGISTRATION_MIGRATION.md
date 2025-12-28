# Event Registration System - Database Migration

## ⚠️ IMPORTANT: Coordinate with Event Module Owner

This migration creates the `event_registrations` table that needs to be added to the shared Supabase database.

## Migration File

**Location:** `database/migrations/2025_11_12_162333_create_event_registrations_table.php`

## Table Structure: `event_registrations`

### Columns:

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key |
| `user_id` | BIGINT | Foreign key to users table |
| `event_id` | BIGINT | Foreign key to events table |
| `role` | VARCHAR | User's role: 'jury', 'reviewer', or 'participant' |
| `status` | VARCHAR | Registration status: 'pending', 'approved', or 'rejected' |
| `certificate_path` | VARCHAR (nullable) | File path for jury certificate uploads |
| `certificate_filename` | VARCHAR (nullable) | Original filename of uploaded certificate |
| `application_notes` | TEXT (nullable) | Notes from user during registration |
| `admin_notes` | TEXT (nullable) | Notes from event organizer (for your friend's module) |
| `approved_at` | TIMESTAMP (nullable) | When registration was approved |
| `approved_by` | BIGINT (nullable) | User ID of who approved (for organizer tracking) |
| `rejected_at` | TIMESTAMP (nullable) | When registration was rejected |
| `rejected_reason` | TEXT (nullable) | Reason for rejection (for your friend's module) |
| `created_at` | TIMESTAMP | Created timestamp |
| `updated_at` | TIMESTAMP | Updated timestamp |

### Indexes:
- `user_id` - For querying user's registrations
- `event_id` - For querying event participants
- `status` - For filtering by status
- `role` - For filtering by role

### Constraints:
- **Foreign Keys:**
  - `user_id` → `users.id` (CASCADE on delete)
  - `event_id` → `events.id` (CASCADE on delete)
  - `approved_by` → `users.id` (SET NULL on delete)

- **Unique Constraint:**
  - `(user_id, event_id)` - Each user can only register once per event

## How to Apply Migration

### Option 1: Coordinate with Friend (RECOMMENDED)
Share this migration file with your friend who manages the events module. They should run:

```bash
php artisan migrate
```

This ensures the migration is tracked properly and can be rolled back if needed.

### Option 2: Direct SQL (if needed)
If you need to apply it manually to Supabase, here's the SQL:

```sql
CREATE TABLE event_registrations (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    event_id BIGINT NOT NULL,
    role VARCHAR(255) NOT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    certificate_path VARCHAR(255),
    certificate_filename VARCHAR(255),
    application_notes TEXT,
    admin_notes TEXT,
    approved_at TIMESTAMP,
    approved_by BIGINT,
    rejected_at TIMESTAMP,
    rejected_reason TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_event_registrations_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_event_registrations_event_id 
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_event_registrations_approved_by 
        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT uk_event_registrations_user_event 
        UNIQUE (user_id, event_id)
);

CREATE INDEX idx_event_registrations_user_id ON event_registrations(user_id);
CREATE INDEX idx_event_registrations_event_id ON event_registrations(event_id);
CREATE INDEX idx_event_registrations_status ON event_registrations(status);
CREATE INDEX idx_event_registrations_role ON event_registrations(role);
```

## Features Implemented

### 1. Role-Based Registration
- **Innovation Events:** Users can register as Jury or Participant
- **Conference Events:** Users can register as Jury, Reviewer, or Participant
- Automatic role validation based on event category

### 2. Jury Application Process
- Jury applicants must upload a certificate (PDF, JPG, PNG - max 5MB)
- Certificates stored in `storage/app/certificates/`
- Applications require approval from event organizer
- Status changes: pending → approved/rejected

### 3. Participant Auto-Approval
- Participants and Reviewers are automatically approved
- No certificate required
- Immediate registration confirmation

### 4. User Interface
- **Registration Form:** `/events/{event}/register`
  - Role selection with descriptions
  - Certificate upload for jury
  - Optional application notes
  
- **My Registrations:** `/my-registrations`
  - List all registrations
  - Status badges (pending/approved/rejected)
  - Cancel registration option
  - View event details

- **Event Details:** Registration status shown on event page
  - Pending applications
  - Approved registrations
  - Rejected applications with reason

### 5. Business Logic
- One registration per user per event
- Jury cancellations blocked within 7 days of event
- Certificate files automatically deleted on cancellation
- Proper error handling and logging

## Integration Points for Event Organizer Module

Your friend's event management module can:

1. **View Registrations:**
   ```php
   $registrations = EventRegistration::where('event_id', $eventId)
       ->with('user')
       ->get();
   ```

2. **Approve Jury Applications:**
   ```php
   $registration->update([
       'status' => 'approved',
       'approved_at' => now(),
       'approved_by' => auth()->id(),
       'admin_notes' => 'Approved - credentials verified'
   ]);
   ```

3. **Reject Applications:**
   ```php
   $registration->update([
       'status' => 'rejected',
       'rejected_at' => now(),
       'rejected_reason' => 'Insufficient credentials'
   ]);
   ```

4. **Filter by Role:**
   ```php
   $juries = EventRegistration::where('event_id', $eventId)
       ->where('role', 'jury')
       ->where('status', 'approved')
       ->get();
   ```

## File Storage

Jury certificates are stored in:
```
storage/app/certificates/{timestamp}_{user_id}_{original_filename}
```

To download a certificate in organizer module:
```php
Storage::download($registration->certificate_path);
```

## Testing Checklist

- [ ] Migration applied successfully
- [ ] User can register as Participant (auto-approved)
- [ ] User can apply as Jury (with certificate upload)
- [ ] Jury applications show as pending
- [ ] User can view their registrations
- [ ] User can cancel registrations
- [ ] Cannot register twice for same event
- [ ] Innovation events show 2 roles (Jury, Participant)
- [ ] Conference events show 3 roles (Jury, Reviewer, Participant)
- [ ] Certificate files are stored properly
- [ ] My Registrations page displays correctly

## Next Steps

1. **Share migration file with your friend**
2. **Request them to run the migration**
3. **Test registration flow**
4. **Coordinate on jury approval workflow** (they approve in their module, your module shows status)
5. **Consider adding email notifications** for status changes (future enhancement)

## Notes

- The `approved_by`, `admin_notes`, and `rejected_reason` fields are designed for your friend's organizer module to use
- Your module handles the user-facing registration, their module handles the approval workflow
- Certificate files are private and only accessible to authenticated organizers
