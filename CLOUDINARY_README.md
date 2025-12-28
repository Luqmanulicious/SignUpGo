# 📸 Cloudinary Integration - README

## Overview

This SignUpGO application now uses **Cloudinary** for all file storage instead of local storage or database storage. This provides better performance, scalability, and reliability for handling user uploads.

## What Files Are Stored in Cloudinary?

- ✅ **User Certificates** (PDF, JPG, PNG) - `/certificates/` folder
- ✅ **User Resumes** (PDF, DOC, DOCX) - `/resumes/` folder
- ✅ **Paper Posters** (JPG, PNG, PDF) - `/posters/` folder

## Quick Start

### 1. Configuration
Your Cloudinary URL is already configured in `.env`:
```env
CLOUDINARY_URL=cloudinary://818775563552672:XhHnD2Zs8uqbaY9oDIl_s9kxFqY@dv2f48oq5
```

### 2. Testing
Test the integration by uploading files:
1. Login to your account
2. Go to Account Settings
3. Upload a certificate or resume
4. Check [Cloudinary Dashboard](https://cloudinary.com/console/media_library)

### 3. Viewing Files
Access your Cloudinary dashboard:
- URL: https://cloudinary.com/console
- Cloud Name: `dv2f48oq5`

## Documentation

📖 **Detailed Guides:**
- [`CLOUDINARY_QUICK_START.md`](CLOUDINARY_QUICK_START.md) - Quick reference and examples
- [`CLOUDINARY_SETUP_GUIDE.md`](CLOUDINARY_SETUP_GUIDE.md) - Complete setup instructions
- [`CLOUDINARY_INTEGRATION_SUMMARY.md`](CLOUDINARY_INTEGRATION_SUMMARY.md) - Full implementation summary

## How It Works

### File Upload Flow
```
User uploads file
    ↓
Laravel validates file
    ↓
CloudinaryService uploads to cloud
    ↓
Cloudinary returns secure URL
    ↓
URL saved to database
    ↓
File accessible via Cloudinary CDN
```

### Code Structure
```
app/
├── Services/
│   └── CloudinaryService.php          # Main Cloudinary service
└── Http/Controllers/
    ├── AccountController.php          # Updated for Cloudinary
    ├── Auth/RegisterController.php    # Updated for Cloudinary
    ├── RegistrationController.php     # Updated for Cloudinary
    └── EventDashboardController.php   # Updated for Cloudinary
```

## Usage Examples

### In Controllers
```php
use App\Services\CloudinaryService;

// Upload certificate
$cloudinary = new CloudinaryService();
$result = $cloudinary->uploadCertificate($file, $userId);
$url = $result['secure_url'];

// Delete file
$cloudinary->deleteByUrl($oldUrl);
```

### In Blade Views
```blade
@if($user->certificate_path)
    <a href="{{ $user->certificate_path }}" target="_blank">
        View Certificate
    </a>
@endif
```

## Migrating Existing Files

If you have files in local storage (`storage/app/public`), migrate them using:

```bash
php artisan tinker
```

```php
require 'database/migrations/migrate_to_cloudinary.php';
migrateFilesToCloudinary();
```

## File Size Limits

| Type | Max Size | Formats |
|------|----------|---------|
| Certificates | 5MB | PDF, JPG, JPEG, PNG |
| Resumes | 5MB | PDF, DOC, DOCX |
| Posters | 10MB | JPG, JPEG, PNG, PDF |

## Troubleshooting

### Configuration Issues
```bash
php artisan config:clear
php artisan cache:clear
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Common Errors

**"Cloudinary URL is not configured"**
- Clear config cache: `php artisan config:clear`
- Verify `.env` has `CLOUDINARY_URL` set

**Upload fails**
- Check internet connection
- Verify file size is within limits
- Check Cloudinary account status

**Files not visible**
- Wait a few seconds for sync
- Check correct folder in Media Library
- Refresh Cloudinary dashboard

## Security

- ✅ Cloudinary credentials stored securely in `.env`
- ✅ `.env` file is git-ignored
- ✅ All uploads validated before sending to cloud
- ⚠️ Files are publicly accessible by default

For private files, modify `CloudinaryService.php`:
```php
'type' => 'private',
'access_mode' => 'authenticated'
```

## Benefits

1. **No Server Storage Needed** - Files stored in cloud
2. **CDN Delivery** - Fast global access
3. **Automatic Optimization** - Images optimized automatically
4. **Scalability** - No storage limits
5. **Reliability** - Built-in backups and redundancy
6. **Easy Management** - Centralized file dashboard

## Support

- **Cloudinary Docs**: https://cloudinary.com/documentation/php_integration
- **Cloudinary Support**: https://support.cloudinary.com
- **Dashboard**: https://cloudinary.com/console
- **Laravel Logs**: `storage/logs/laravel.log`

## Status

✅ **Integration Complete and Tested**

All controllers updated, views configured, and ready to use!

---

**Last Updated**: December 18, 2025  
**Version**: 1.0  
**Status**: Production Ready
