# Cloudinary Quick Reference

## ✅ Setup Complete!

Your Laravel application is now configured to use Cloudinary for file storage.

## 🔧 What Changed

### All File Uploads Now Use Cloudinary
- ✅ User certificates → Cloudinary `/certificates/` folder
- ✅ User resumes → Cloudinary `/resumes/` folder  
- ✅ Paper posters → Cloudinary `/posters/` folder

### Controllers Updated
- ✅ `AccountController` - Profile file uploads
- ✅ `RegisterController` - Registration file uploads
- ✅ `RegistrationController` - Paper poster uploads
- ✅ `EventDashboardController` - Event paper uploads

## 🚀 Testing Your Integration

### 1. Test Certificate Upload
```
1. Login to your account
2. Go to Account Settings
3. Upload a certificate (PDF, JPG, or PNG)
4. Check Cloudinary dashboard: https://cloudinary.com/console/media_library
5. Verify file appears in /certificates/ folder
```

### 2. Test Resume Upload
```
1. Go to Account Settings
2. Upload a resume (PDF, DOC, or DOCX)
3. Check Cloudinary dashboard /resumes/ folder
```

### 3. Test Paper Poster Upload
```
1. Register for an event as a participant
2. Upload a paper poster
3. Check Cloudinary dashboard /posters/ folder
```

## 📝 Environment Configuration

Your `.env` file now has:
```env
CLOUDINARY_URL=cloudinary://818775563552672:XhHnD2Zs8uqbaY9oDIl_s9kxFqY@dv2f48oq5
```

This URL contains:
- **Cloud Name**: dv2f48oq5
- **API Key**: 818775563552672
- **API Secret**: XhHnD2Zs8uqbaY9oDIl_s9kxFqY

## 💡 Usage Examples

### In Controllers
```php
use App\Services\CloudinaryService;

// Upload a certificate
$cloudinary = new CloudinaryService();
$result = $cloudinary->uploadCertificate($file, $userId);
$url = $result['secure_url']; // Store this in database

// Upload a resume
$result = $cloudinary->uploadResume($file, $userId);

// Upload a poster
$result = $cloudinary->uploadPoster($file, $userId, $eventId);

// Delete by URL
$cloudinary->deleteByUrl($oldFileUrl);

// Delete by public_id
$cloudinary->delete($publicId, 'image');
```

### In Blade Views
```blade
{{-- Display certificate link --}}
@if($user->certificate_path)
    <a href="{{ $user->certificate_path }}" target="_blank">
        View Certificate
    </a>
@endif

{{-- Display poster image --}}
@if($paper->poster_path)
    <img src="{{ $paper->poster_path }}" alt="Poster" class="img-fluid">
@endif

{{-- Display resume link --}}
@if($user->resume_path)
    <a href="{{ $user->resume_path }}" target="_blank" class="btn btn-primary">
        <i class="fas fa-download"></i> Download Resume
    </a>
@endif
```

## 🎯 File Size Limits

Current validation rules:
- **Certificates**: 5MB max (PDF, JPG, JPEG, PNG)
- **Resumes**: 5MB max (PDF, DOC, DOCX)
- **Posters**: 10MB max (JPG, JPEG, PNG, PDF)

To change limits, update the validation rules in the controllers.

## 🔍 Cloudinary Dashboard Access

Access your files at: https://cloudinary.com/console

Features available:
- 📁 Browse all uploaded files
- 📊 View storage usage and bandwidth
- 🖼️ Preview images and documents
- 🔧 Configure upload settings
- 📈 View analytics

## ⚠️ Important Notes

1. **Old Files**: Existing files in `storage/app/public` won't be automatically migrated. They will remain in local storage but new uploads go to Cloudinary.

2. **Database Values**: The database now stores full Cloudinary URLs (e.g., `https://res.cloudinary.com/dv2f48oq5/...`) instead of relative paths.

3. **Public Access**: All uploaded files are publicly accessible via their URLs. For private files, configure upload options in `CloudinaryService`.

4. **Internet Required**: File uploads require internet connection to reach Cloudinary servers.

## 🐛 Common Issues

### "Cloudinary URL is not configured"
```bash
# Solution:
php artisan config:clear
php artisan cache:clear
```

### Files not uploading
1. Check internet connection
2. Verify CLOUDINARY_URL in .env is correct
3. Check logs: `storage/logs/laravel.log`
4. Verify Cloudinary account is active

### Can't see files in dashboard
1. Wait a few seconds (files may take time to appear)
2. Check correct folder (certificates, resumes, or posters)
3. Verify upload was successful in Laravel logs

## 📚 Additional Resources

- Cloudinary PHP Documentation: https://cloudinary.com/documentation/php_integration
- Laravel File Upload: https://laravel.com/docs/filesystem
- CloudinaryService: `app/Services/CloudinaryService.php`
- Full Setup Guide: `CLOUDINARY_SETUP_GUIDE.md`

## 🎉 Next Steps

1. **Test the integration** by uploading files through your application
2. **Check Cloudinary dashboard** to confirm files are being uploaded
3. **Monitor logs** for any errors: `tail -f storage/logs/laravel.log`
4. **Update views** if needed to properly display Cloudinary URLs
5. **(Optional) Migrate existing files** from local storage to Cloudinary

---

**Everything is ready! Start uploading files and they'll automatically go to Cloudinary! 🚀**
