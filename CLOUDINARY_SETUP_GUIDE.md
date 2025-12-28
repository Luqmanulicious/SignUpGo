# Cloudinary Integration Setup Guide

## Overview
This guide will help you set up Cloudinary to handle file uploads (certificates, resumes, and poster images) in your Laravel application instead of storing them in the database or local storage.

## What Was Changed

### 1. Files Modified
- `config/services.php` - Added Cloudinary configuration
- `.env.example` - Added Cloudinary environment variables
- `composer.json` - Added cloudinary-labs/cloudinary-laravel package
- `app/Services/CloudinaryService.php` - **NEW**: Created service to handle Cloudinary uploads
- `app/Http/Controllers/AccountController.php` - Updated to use Cloudinary
- `app/Http/Controllers/Auth/RegisterController.php` - Updated to use Cloudinary
- `app/Http/Controllers/RegistrationController.php` - Updated to use Cloudinary
- `app/Http/Controllers/EventDashboardController.php` - Updated to use Cloudinary

### 2. What's Different
- Files are now uploaded to Cloudinary cloud storage instead of local `storage/app/public`
- File URLs are stored in the database as full Cloudinary URLs (e.g., `https://res.cloudinary.com/...`)
- Old local files won't be accessible - you'll need to migrate existing files if needed

## Installation Steps

### Step 1: Install Cloudinary Package
```bash
composer install
```
This will install the `cloudinary-labs/cloudinary-laravel` package that's already in composer.json.

### Step 2: Add Cloudinary URL to .env
Open your `.env` file (or create one from `.env.example`) and add:

```env
CLOUDINARY_URL=cloudinary://818775563552672:XhHnD2Zs8uqbaY9oDIl_s9kxFqY@dv2f48oq5
```

**Important**: This URL contains your Cloudinary credentials:
- Cloud Name: `dv2f48oq5`
- API Key: `818775563552672`
- API Secret: `XhHnD2Zs8uqbaY9oDIl_s9kxFqY`

### Step 3: Clear Configuration Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test the Integration
Try uploading a file through your application:
1. Go to Account Settings
2. Upload a certificate or resume
3. Check your Cloudinary dashboard to verify the file was uploaded

## How It Works

### CloudinaryService
The new `CloudinaryService` class provides these methods:

1. **upload($file, $folder, $options)** - Generic upload method
2. **uploadCertificate($file, $userId)** - Upload user certificates
3. **uploadResume($file, $userId)** - Upload user resumes
4. **uploadPoster($file, $userId, $eventId)** - Upload paper posters
5. **delete($publicId, $resourceType)** - Delete files by public_id
6. **deleteByUrl($url)** - Delete files by Cloudinary URL
7. **getPublicIdFromUrl($url)** - Extract public_id from URL

### File Organization in Cloudinary

Files are organized in folders:
- `/certificates/` - User certificates
- `/resumes/` - User resumes
- `/posters/` - Paper posters

Each file is named with a pattern like:
- Certificate: `certificate_{userId}_{timestamp}`
- Resume: `resume_{userId}_{timestamp}`
- Poster: `poster_{eventId}_{userId}_{timestamp}`

### Database Storage

Your database now stores full Cloudinary URLs instead of local paths:
- **Before**: `certificates/abc123.pdf`
- **After**: `https://res.cloudinary.com/dv2f48oq5/image/upload/v1234567890/certificates/certificate_1_1234567890.pdf`

## Migration of Existing Files (Optional)

If you have existing files in local storage that you want to migrate to Cloudinary:

### Option 1: Manual Migration Script
Create a migration script that:
1. Reads all existing files from `storage/app/public`
2. Uploads each to Cloudinary
3. Updates the database with new URLs

### Option 2: Let Users Re-upload
Simply inform users that they need to re-upload their files. Old files will remain in local storage but won't be accessible through the UI.

## Troubleshooting

### Error: "Cloudinary URL is not configured"
- Make sure `CLOUDINARY_URL` is set in your `.env` file
- Run `php artisan config:clear`

### Error: "Failed to upload file to Cloudinary"
- Check your internet connection
- Verify your Cloudinary credentials are correct
- Check Cloudinary dashboard for any account issues
- Review Laravel logs: `storage/logs/laravel.log`

### Files Not Appearing in Cloudinary Dashboard
- Go to Cloudinary Dashboard > Media Library
- Check the appropriate folder (certificates, resumes, or posters)
- Files might take a few seconds to appear

### Large File Upload Issues
Current file size limits:
- Certificates: 5MB (PDF, JPG, JPEG, PNG)
- Resumes: 5MB (PDF, DOC, DOCX)
- Posters: 10MB (JPG, JPEG, PNG, PDF)

To increase limits, update the validation rules in controllers and check Cloudinary account limits.

## Viewing Uploaded Files

Since files are now on Cloudinary, you can access them directly via the URL stored in the database. In your Blade views, you can display them like:

```blade
@if($user->certificate_path)
    <a href="{{ $user->certificate_path }}" target="_blank">View Certificate</a>
@endif

@if($user->resume_path)
    <a href="{{ $user->resume_path }}" target="_blank">View Resume</a>
@endif

@if($paper->poster_path)
    <img src="{{ $paper->poster_path }}" alt="Paper Poster">
@endif
```

## Security Considerations

1. **Environment Variables**: Never commit your `.env` file to Git. The Cloudinary URL contains sensitive credentials.

2. **Public Access**: By default, uploaded files are publicly accessible via their URLs. If you need private files, configure Cloudinary upload options:
   ```php
   $result = $cloudinary->upload($file, 'private-folder', [
       'type' => 'private',
       'access_mode' => 'authenticated'
   ]);
   ```

3. **File Validation**: All file uploads are validated before being sent to Cloudinary (file type, size, etc.).

## Cloudinary Dashboard

Access your Cloudinary dashboard at: https://cloudinary.com/console

From there you can:
- View all uploaded files
- Monitor storage usage
- Configure upload presets
- Set up transformations
- View analytics

## Benefits of Using Cloudinary

1. **No Database Bloat**: Files are stored in the cloud, not in your database
2. **CDN Delivery**: Files are delivered via Cloudinary's global CDN for faster loading
3. **Image Optimization**: Automatic image optimization and format conversion
4. **Scalability**: No server storage limitations
5. **Transformations**: Easy image resizing, cropping, and manipulation
6. **Backups**: Cloudinary handles file backups and redundancy

## Support

For Cloudinary-specific issues:
- Documentation: https://cloudinary.com/documentation/php_integration
- Support: https://support.cloudinary.com

For application issues:
- Check `storage/logs/laravel.log` for error details
- Review the CloudinaryService class for implementation details
