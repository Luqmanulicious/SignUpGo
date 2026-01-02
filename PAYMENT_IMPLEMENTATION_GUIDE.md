# Payment System Implementation Guide

## Overview
Implemented a complete payment workflow for event participants requiring payment approval before accessing presentation details.

---

## Database Changes

### 1. Events Table
**Migration**: `2026_01_02_000002_add_payment_qr_code_to_events.php`

Added column:
- `payment_qr_code` (string, nullable) - Stores path to payment QR code image

**Usage**: Event Organizer uploads QR code for participants to scan and make payment.

### 2. Event Registrations Table
**Migration**: `2026_01_02_000001_add_payment_fields_to_event_registrations.php`

Added columns:
- `payment_receipt_path` (string, nullable) - Participant's uploaded payment receipt
- `payment_status` (enum: pending/approved/rejected, nullable) - Payment approval status
- `payment_submitted_at` (timestamp, nullable) - When participant submitted receipt
- `payment_approved_at` (timestamp, nullable) - When EO approved payment
- `payment_notes` (text, nullable) - EO notes for approval/rejection reason

---

## Run Migrations

```bash
php artisan migrate
```

---

## Required Routes

Add to `routes/web.php`:

```php
Route::middleware(['auth'])->group(function () {
    // Payment submission
    Route::post('/events/{event}/registrations/{registration}/payment', 
        [App\Http\Controllers\EventDashboardController::class, 'submitPayment'])
        ->name('event.payment.submit');
});
```

---

## Required Controller Method

Add to `EventDashboardController.php`:

```php
public function submitPayment(Request $request, Event $event, EventRegistration $registration)
{
    $request->validate([
        'payment_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
    ]);

    // Store payment receipt
    $path = $request->file('payment_receipt')->store('payment-receipts', 'public');

    // Update registration
    $registration->update([
        'payment_receipt_path' => $path,
        'payment_status' => 'pending',
        'payment_submitted_at' => now(),
    ]);

    return redirect()->route('event.dashboard', [$event, $registration])
        ->with('success', 'Payment receipt submitted successfully! Awaiting organizer approval.');
}
```

---

## Features Implemented

### 1. Payment Modal
- **Trigger**: Click "Pay Now" button
- **Displays**:
  - Event name
  - Total registration fee
  - Payment QR code (if uploaded by EO)
  - Upload form for payment receipt
- **Submission**: Stores receipt and sets status to 'pending'

### 2. Payment Status Display
Shows different states in sidebar:

**Unpaid**:
- Shows "Payment Required" warning
- Displays fee amount
- "Pay Now" button

**Pending**:
- Shows "Payment Pending Review" with ⏳ icon
- Link to view submitted receipt
- Waiting message

**Rejected**:
- Shows "Payment Rejected" with ✗ icon
- Displays EO rejection reason
- "Resubmit Payment" button

**Approved**:
- Shows "Payment Approved" with ✓ icon
- Displays approval date

### 3. Presentation Details Access Control
- **Before Payment**: Shows locked message "You need to pay the registration fee to view presentation details"
- **After Payment Approval**: Shows full product/paper submission section

### 4. Free Events
- No payment required
- Shows "Free Event" badge 🎉
- Full access to presentation details

---

## EO Side (To Be Implemented)

The Event Organizer needs:

### 1. Upload Payment QR Code
**Location**: Event creation/edit form
- Field: `payment_qr_code` (file upload)
- Accepts: JPG, PNG
- Recommended: Add in event form under "Registration Settings"

### 2. Payment Approval Dashboard
**New View Needed**: `resources/views/organizer/payment-approvals.blade.php`

Features needed:
- List all registrations with `payment_status = 'pending'`
- Display participant name, event, fee amount
- View payment receipt (image/PDF viewer)
- Approve/Reject buttons
- Text field for rejection notes

**Controller Actions Needed**:
```php
// List pending payments
public function paymentApprovals(Event $event)
{
    $pendingPayments = EventRegistration::where('event_id', $event->id)
        ->where('payment_status', 'pending')
        ->with('user')
        ->get();
    
    return view('organizer.payment-approvals', compact('event', 'pendingPayments'));
}

// Approve payment
public function approvePayment(EventRegistration $registration)
{
    $registration->update([
        'payment_status' => 'approved',
        'payment_approved_at' => now(),
    ]);
    
    // Optional: Send notification to participant
    
    return back()->with('success', 'Payment approved successfully!');
}

// Reject payment
public function rejectPayment(Request $request, EventRegistration $registration)
{
    $request->validate([
        'payment_notes' => 'required|string|max:500',
    ]);
    
    $registration->update([
        'payment_status' => 'rejected',
        'payment_notes' => $request->payment_notes,
    ]);
    
    // Optional: Send notification to participant
    
    return back()->with('success', 'Payment rejected with reason provided.');
}
```

---

## Testing Checklist

### Participant Side:
- [ ] Click "Pay Now" opens modal
- [ ] Modal shows event name and fee
- [ ] QR code displays correctly (if uploaded by EO)
- [ ] Can upload payment receipt (JPG/PNG/PDF)
- [ ] Submit shows "Pending Review" status
- [ ] Can view submitted receipt
- [ ] Presentation details are locked before payment
- [ ] Presentation details unlock after approval
- [ ] Rejected payment shows reason
- [ ] Can resubmit after rejection

### EO Side (To Implement):
- [ ] Can upload payment QR code in event form
- [ ] Can see list of pending payments
- [ ] Can view payment receipts
- [ ] Can approve payments
- [ ] Can reject with reason
- [ ] Participant receives notification

---

## Workflow Summary

1. **EO Setup**: Uploads payment QR code when creating/editing event
2. **Participant Registers**: Registration created with `payment_status = null`
3. **Dashboard Access**: Participant can access dashboard but sees locked presentation details
4. **Payment**: Participant clicks "Pay Now", scans QR, uploads receipt
5. **Status Update**: `payment_status = 'pending'`, `payment_submitted_at` recorded
6. **EO Review**: EO views pending payments, checks receipt
7. **Approval/Rejection**:
   - **Approve**: `payment_status = 'approved'`, `payment_approved_at` recorded
   - **Reject**: `payment_status = 'rejected'`, reason stored in `payment_notes`
8. **Access Granted**: After approval, participant can view presentation details

---

## Additional Recommendations

### 1. Notifications
Implement email/in-app notifications for:
- Payment receipt submitted (to EO)
- Payment approved (to Participant)
- Payment rejected (to Participant with reason)

### 2. Payment History
Add payment history view for participants to track all their payment submissions and statuses.

### 3. Bulk Actions
For EO: Allow bulk approval of multiple payments at once.

### 4. Payment Receipt Preview
In the modal, show preview of selected file before upload.

### 5. Deadline
Consider adding `payment_deadline` to events table if payment must be done by certain date.

---

## Next Steps

1. ✅ Run migrations: `php artisan migrate`
2. ✅ Add route for payment submission
3. ✅ Add `submitPayment` method to controller
4. ⏳ Create EO payment QR upload field in event form
5. ⏳ Create EO payment approval dashboard
6. ⏳ Add approve/reject actions for EO
7. ⏳ Test complete workflow
8. ⏳ Add notifications (optional but recommended)
