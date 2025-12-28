# 📝 Additional Cloudinary Integration Notes

## QR Code Image Storage

### Current Implementation
The system has QR code images stored in the `qr_image_path` field of the `event_registrations` table. Currently, these are NOT being uploaded to Cloudinary automatically.

### QR Code Types in Your System

1. **Registration QR Codes** (`event_registrations.qr_image_path`)
   - Used for event registration confirmation
   - Displayed when viewing registration details
   - Currently referenced in views but generation logic not found in controllers

2. **Presentation QR Codes** (`presentation_qr_codes.qr_image_url`)
   - Used for event check-in at venue
   - Generated for participants during events
   - Currently stored in database

### Recommendations

Since QR codes are typically generated programmatically (not uploaded by users), you have two options:

#### Option 1: Store QR Codes in Cloudinary (Recommended)
**Pros:**
- Consistent storage location with other files
- CDN delivery for fast loading
- No local storage needed

**Implementation:**
When generating QR codes, use the `CloudinaryService::uploadQrCode()` method:

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\CloudinaryService;

// Generate QR code
$qrCodeImage = QrCode::format('png')
    ->size(300)
    ->generate($registration->qr_code);

// Upload to Cloudinary
$cloudinary = new CloudinaryService();
$result = $cloudinary->uploadQrCode(
    'data:image/png;base64,' . base64_encode($qrCodeImage),
    $event->id,
    $user->id
);

// Save URL to database
$registration->qr_image_path = $result['secure_url'];
$registration->save();
```

#### Option 2: Generate QR Codes On-the-Fly (Alternative)
**Pros:**
- No storage needed at all
- Always up-to-date
- Simpler implementation

**Implementation:**
Remove `qr_image_path` storage and generate QR codes in views:

```blade
{{-- In your view --}}
@if($registration->qr_code)
    <img src="data:image/png;base64,{{ base64_encode(
        QrCode::format('png')->size(300)->generate($registration->qr_code)
    ) }}" alt="QR Code">
@endif
```

### Where QR Codes Are Referenced

1. **Views that display QR codes:**
   - `resources/views/dashboard/registrations/show.blade.php`
   - `resources/views/event-dashboard/participant.blade.php`

2. **Database fields:**
   - `event_registrations.qr_image_path` - Registration QR codes
   - `presentation_qr_codes.qr_image_url` - Presentation QR codes

### Action Required

**Choose one approach:**

1. **If you want to store QR codes in Cloudinary:**
   - Find where QR codes are generated (likely in an admin controller or observer)
   - Update generation logic to use `CloudinaryService::uploadQrCode()`
   - Migrate existing QR code images to Cloudinary

2. **If you want to generate QR codes on-the-fly:**
   - Update views to generate QR codes dynamically
   - Remove `qr_image_path` database field (optional)
   - No migration needed

## Other File Types to Check

### Event Organizer Documents
If your system allows event organizers to upload documents, check:
- `EventOrganizerDocument` model has `file_path` field
- Need to find controller that handles organizer document uploads
- Update to use Cloudinary

### Profile Pictures (If Implemented)
If users can upload profile pictures:
- Check `users` table for avatar/photo fields
- Update upload logic to use Cloudinary

## Summary of Changes Made

### ✅ Already Using Cloudinary:
1. User certificates
2. User resumes
3. Paper posters

### ⚠️ Needs Review:
1. QR code images (choose generation strategy)
2. Event organizer documents (if implemented)
3. Profile pictures (if implemented)

### 📋 View Files Updated:
1. ✅ `resources/views/registrations/create.blade.php` - Certificate/resume links
2. ✅ `resources/views/registrations/edit.blade.php` - Poster display
3. ✅ `resources/views/event-dashboard/reviewer.blade.php` - Poster viewing
4. ✅ `resources/views/dashboard/registrations/show.blade.php` - QR code display ready
5. ⚠️ `resources/views/event-dashboard/participant.blade.php` - Uses `qr_image_url` from different table

## Next Steps

1. **Determine QR code strategy** (store in Cloudinary vs generate on-the-fly)
2. **Find QR code generation logic** and update accordingly
3. **Check for event organizer document uploads** and update if needed
4. **Test all file upload/display functionality** thoroughly

---

**Note:** The `CloudinaryService` class now includes the `uploadQrCode()` method ready to use when you implement your chosen QR code strategy.
