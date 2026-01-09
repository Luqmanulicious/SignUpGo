# SignUpGo Test Cases

This document lists all identified test cases for the SignUpGo application, written as user actions and expected behaviors.

## 1. Authentication & Authorization
- [ ] **Guest Actions**
    - Guest navigates to protected pages (dashboard, profile) and is redirected to login.
    - Guest view the public `events` listing.
    - Guest scans an event QR code to view the check-in page.
- [ ] **User Actions**
    - User logs in and accesses their dashboard.
    - User is redirected to login if their session has expired.

## 2. Event Management (EventController)
- [ ] **Browsing Events**
    - User views the event list and sees only "published" events.
    - User sees events sorted by newest first.
    - User scrolls through pages of events (pagination).
- [ ] **Searching & Filtering**
    - User searches for an event by title or description.
    - User filters events by Category, Date Range, or Type (Free/Paid).
- [ ] **Viewing Details**
    - User clicks on an event and views the full details (organizer, category, description).
    - User sees the event poster image.

## 3. Registration System (RegistrationController)
- [ ] **Registration Eligibility**
    - User attempts to register for a draft/closed event (should be blocked).
    - User attempts to register after the global deadline (should be blocked).
    - **Reviewer**: User attempts to register after the specific Reviewer deadline.
    - **Jury**: User attempts to register after the specific Jury deadline.
    - User attempts to register when the event is full (Waitlist check).
- [ ] **Duplicate Logic**
    - User attempts to register for the same event and role again (should be blocked).
    - User registers for a *different* role in the same event (if allowed).
- [ ] **Submitting Registration**
    - **Participant**: Fills in paper title, abstract, and uploads a file.
    - **Hybrid Event**: Participant selects their attendance mode (Online/F2F).
    - **Jury**: Selects their expert themes and uploads a certificate if required.
    - **Reviewer**: Selects their expert themes and uploads a certificate if required.
- [ ] **Check-In**
    - Staff manually checks in a user via the dashboard.
    - System prevents checking in the same user twice.

## 4. Role-Specific Workflows (EventDashboardController)
- [ ] **Participant Workflow**
    - **Paper Management**:
        - Participant updates their paper details while it is still a 'draft'.
        - Participant attempts to edit a submitted/approved paper (should be blocked/restricted).
        - Participant replaces an existing paper file with a new one.
    - **Payment**:
        - Participant uploads a payment receipt.
        - System updates status to `pending` and clears old rejection notes.
- [ ] **Reviewer Workflow**
    - ** assignments**: Reviewer views their list of assigned participants.
    - **Evaluations**:
        - Reviewer submits a review for an assigned participant.
        - Reviewer edits an existing review (loads previous scores).
        - System calculates total score vs max score automatically.
        - System prevents reviewer from reviewing unassigned participants.
- [ ] **Jury Workflow**
    - **Assignments**: Jury views their list of assigned participants.
    - **Evaluations**:
        - Jury opens the evaluation form and sees rubric items.
        - Jury submits evaluation scores (0-5) and comments.
        - Jury attempts to edit evaluation after deadline (should be blocked).

## 5. QR Check-In System (QrCheckInController)
- [ ] **Scanning**
    - User scans an invalid QR code (sees error message).
    - User scans a QR code for an unapproved registration (sees "not approved").
    - User scans a QR code too early (before event date).
    - User scans a valid QR code on the day of the event (sees confirmation).
- [ ] **Confirming Check-In**
    - User confirms check-in on the scan page.
    - System marks the user as `checked_in`.

## 6. Feedback System (FeedbackController)
- [ ] **Eligibility**
    - User attempts to submit feedback before the event ends (should be blocked).
    - **Conference Participant**: Attempts feedback without checking in (should be blocked).
    - **Innovation Participant**: Attempts feedback without approved payment (should be blocked).
    - **Reviewer**: Attempts feedback with incomplete evaluations (should be blocked).
- [ ] **Submission**
    - User submits feedback ratings and comments.
    - User attempts to submit feedback a second time (should be blocked).

## 7. Account & Certificates
- [ ] **Profile Management**
    - User updates their profile information (name, phone, etc.).
    - User uploads a Resume, Certificate, or Profile Picture.
    - User deletes an uploaded file.
- [ ] **Certificates**
    - User views their list of earned certificates.
    - User downloads a specific certificate file.
    - User attempts to download another user's certificate (should be blocked).

## 8. Mailbox & Notifications
- [ ] **Using Mailbox**
    - User views their notifications list.
    - User marks a notification as read.
    - User deletes a notification.

## 9. API & Services
- [ ] **Public API**
    - External system requests a list of registrations with filters.
    - External system requests registration statistics.
- [ ] **File Services**
    - System uploads files to Cloudinary folders.
    - System deletes files from Cloudinary when records are removed.
