# Cloudinary Upload Implementation Guide

## Overview
The CloudinaryService now properly handles both **images** and **PDFs/documents** with correct resource types and public access.

---

## How It Works

### Automatic Detection
The service automatically detects file types:
- **PDFs & Documents** → `resource_type = raw`, `access_mode = public`
- **Images (JPG, PNG)** → `resource_type = image` or `auto`

### URL Structure
- **Images**: `https://res.cloudinary.com/.../image/upload/.../filename.jpg`
- **PDFs**: `https://res.cloudinary.com/.../raw/upload/.../filename.pdf`

---

## Controller Examples

### Example 1: Upload Certificate (Supports PDF & Images)

```php
<?php

namespace App\Http\Controllers;

use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function uploadCertificate(Request $request)
    {
        $request->validate([
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ]);

        try {
            // Upload to Cloudinary (automatically handles PDF vs Image)
            $result = $this->cloudinary->uploadCertificate(
                $request->file('certificate'),
                Auth::id()
            );

            // Store in database
            $user = Auth::user();
            $user->certificate_path = $result['secure_url']; // This will be correct URL
            $user->certificate_public_id = $result['public_id'];
            $user->save();

            return back()->with('success', 'Certificate uploaded successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }
}
```

### Example 2: Upload PDF Document Only

```php
public function uploadDocument(Request $request)
{
    $request->validate([
        'document' => 'required|file|mimes:pdf|max:10240',
    ]);

    try {
        // Explicitly upload as PDF using uploadPdf method
        $result = $this->cloudinary->uploadPdf(
            $request->file('document'),
            'user-documents',
            [
                'public_id' => 'doc_' . Auth::id() . '_' . time(),
                'tags' => ['document', 'user_' . Auth::id()],
            ]
        );

        // Store the secure_url (will use /raw/upload/)
        $document = new Document();
        $document->user_id = Auth::id();
        $document->file_url = $result['secure_url'];
        $document->file_public_id = $result['public_id'];
        $document->save();

        return response()->json([
            'success' => true,
            'url' => $result['secure_url'], // Publicly accessible PDF URL
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

### Example 3: Upload Image Only

```php
public function uploadImage(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB
    ]);

    try {
        // Explicitly upload as image
        $result = $this->cloudinary->uploadImage(
            $request->file('image'),
            'event-images',
            [
                'public_id' => 'event_' . $request->event_id . '_' . time(),
                'transformation' => [
                    'width' => 1200,
                    'height' => 800,
                    'crop' => 'limit',
                    'quality' => 'auto',
                ],
            ]
        );

        // Store the URL (will use /image/upload/)
        $event = Event::find($request->event_id);
        $event->featured_image = $result['secure_url'];
        $event->save();

        return back()->with('success', 'Image uploaded successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Upload failed: ' . $e->getMessage());
    }
}
```

### Example 4: Upload Payment Receipt (Mixed Types)

```php
public function uploadPaymentReceipt(Request $request, Event $event, EventRegistration $registration)
{
    $request->validate([
        'payment_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    try {
        $cloudinary = new CloudinaryService();
        
        // Upload (automatically detects if PDF or image)
        $result = $cloudinary->uploadPaymentReceipt(
            $request->file('payment_receipt'),
            $registration->id,
            $event->id
        );

        // Delete old receipt if exists
        if ($registration->payment_receipt_path) {
            $cloudinary->deleteByUrl($registration->payment_receipt_path);
        }

        // Store the correct URL in database
        $registration->payment_receipt_path = $result['secure_url'];
        $registration->payment_status = 'pending';
        $registration->payment_submitted_at = now();
        $registration->save();

        return back()->with('success', 'Payment receipt uploaded successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to upload receipt: ' . $e->getMessage());
    }
}
```

---

## Available Methods

### CloudinaryService Methods

| Method | Purpose | Resource Type | Access |
|--------|---------|---------------|--------|
| `upload($file, $folder, $options)` | General upload (auto-detects) | Auto-detected | Auto-set |
| `uploadImage($file, $folder, $options)` | Image-only upload | `image` | Default |
| `uploadPdf($file, $folder, $options)` | PDF/document upload | `raw` | `public` |
| `uploadCertificate($file, $userId)` | Certificate (PDF or Image) | Auto-detected | Auto-set |
| `uploadResume($file, $userId)` | Resume/CV | `raw` | `public` |
| `uploadPoster($file, $userId, $eventId)` | Event poster | Auto-detected | Auto-set |
| `uploadPaymentReceipt($file, $regId, $eventId)` | Payment proof | Auto-detected | Auto-set |
| `deleteByUrl($url)` | Delete file by URL | Auto-detected | N/A |

---

## Key Features

### ✅ Automatic PDF Detection
- Checks file extension and MIME type
- Sets `resource_type = raw` for PDFs
- Sets `access_mode = public` for public accessibility

### ✅ Correct URL Generation
- PDFs: `/raw/upload/` path
- Images: `/image/upload/` path
- All URLs are `secure_url` (HTTPS)

### ✅ Public Access
- PDFs uploaded with `access_mode = public`
- No authentication required to view
- Direct browser viewing enabled

### ✅ Backward Compatible
- Existing image uploads still work
- No breaking changes to current code
- Optional explicit methods for clarity

---

## Testing

```php
// Test PDF upload
$pdf = UploadedFile::fake()->create('document.pdf', 1024);
$result = $cloudinary->uploadPdf($pdf, 'test-folder');

// Check URL structure
assert(str_contains($result['secure_url'], '/raw/upload/'));

// Test image upload
$image = UploadedFile::fake()->image('photo.jpg', 1200, 800);
$result = $cloudinary->uploadImage($image, 'test-folder');

// Check URL structure
assert(str_contains($result['secure_url'], '/image/upload/'));
```

---

## Troubleshooting

### PDFs return 403 Forbidden
**Solution**: Ensure `access_mode = public` is set during upload.

### URLs have `/image/upload/` for PDFs
**Solution**: Use `uploadPdf()` method or ensure automatic detection is working.

### Can't delete PDFs
**Solution**: The `deleteByUrl()` method now auto-detects resource type from URL.

---

## Summary

✅ **PDFs** → `raw/upload` with public access  
✅ **Images** → `image/upload` with standard access  
✅ **Automatic detection** in main `upload()` method  
✅ **Explicit methods** for specific use cases  
✅ **Correct URLs** stored in database  
✅ **Public accessibility** for all users
