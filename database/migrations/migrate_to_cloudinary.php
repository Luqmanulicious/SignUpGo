<?php

/**
 * Migration Script: Local Storage to Cloudinary
 * 
 * This script migrates existing files from local storage to Cloudinary.
 * Run this ONCE after setting up Cloudinary to migrate existing files.
 * 
 * Usage:
 * php artisan tinker
 * require 'database/migrations/migrate_to_cloudinary.php';
 * migrateFilesToCloudinary();
 */

use App\Models\User;
use App\Models\EventPaper;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

function migrateFilesToCloudinary()
{
    $cloudinary = new CloudinaryService();
    $stats = [
        'certificates' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
        'resumes' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
        'posters' => ['success' => 0, 'failed' => 0, 'skipped' => 0],
    ];

    echo "\n=== Starting Migration to Cloudinary ===\n\n";

    // Migrate User Certificates
    echo "Migrating user certificates...\n";
    $users = User::whereNotNull('certificate_path')->get();
    
    foreach ($users as $user) {
        $localPath = $user->certificate_path;
        
        // Skip if already a Cloudinary URL
        if (str_starts_with($localPath, 'http')) {
            echo "  [SKIP] User #{$user->id} certificate already on Cloudinary\n";
            $stats['certificates']['skipped']++;
            continue;
        }

        try {
            $fullPath = storage_path('app/public/' . $localPath);
            
            if (!file_exists($fullPath)) {
                echo "  [FAIL] User #{$user->id} certificate file not found: {$localPath}\n";
                $stats['certificates']['failed']++;
                continue;
            }

            // Create a temporary UploadedFile instance
            $file = new \Illuminate\Http\UploadedFile(
                $fullPath,
                basename($localPath),
                mime_content_type($fullPath),
                null,
                true
            );

            // Upload to Cloudinary
            $result = $cloudinary->uploadCertificate($file, $user->id);
            
            // Update database
            $user->certificate_path = $result['secure_url'];
            $user->save();

            echo "  [OK] User #{$user->id} certificate migrated\n";
            $stats['certificates']['success']++;

            // Optional: Delete local file
            // Storage::disk('public')->delete($localPath);

        } catch (\Exception $e) {
            echo "  [FAIL] User #{$user->id} certificate: " . $e->getMessage() . "\n";
            $stats['certificates']['failed']++;
            Log::error('Certificate migration failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Migrate User Resumes
    echo "\nMigrating user resumes...\n";
    $users = User::whereNotNull('resume_path')->get();
    
    foreach ($users as $user) {
        $localPath = $user->resume_path;
        
        // Skip if already a Cloudinary URL
        if (str_starts_with($localPath, 'http')) {
            echo "  [SKIP] User #{$user->id} resume already on Cloudinary\n";
            $stats['resumes']['skipped']++;
            continue;
        }

        try {
            $fullPath = storage_path('app/public/' . $localPath);
            
            if (!file_exists($fullPath)) {
                echo "  [FAIL] User #{$user->id} resume file not found: {$localPath}\n";
                $stats['resumes']['failed']++;
                continue;
            }

            $file = new \Illuminate\Http\UploadedFile(
                $fullPath,
                basename($localPath),
                mime_content_type($fullPath),
                null,
                true
            );

            $result = $cloudinary->uploadResume($file, $user->id);
            
            $user->resume_path = $result['secure_url'];
            $user->save();

            echo "  [OK] User #{$user->id} resume migrated\n";
            $stats['resumes']['success']++;

        } catch (\Exception $e) {
            echo "  [FAIL] User #{$user->id} resume: " . $e->getMessage() . "\n";
            $stats['resumes']['failed']++;
            Log::error('Resume migration failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Migrate Paper Posters
    echo "\nMigrating paper posters...\n";
    $papers = EventPaper::whereNotNull('poster_path')->get();
    
    foreach ($papers as $paper) {
        $localPath = $paper->poster_path;
        
        // Skip if already a Cloudinary URL
        if (str_starts_with($localPath, 'http')) {
            echo "  [SKIP] Paper #{$paper->id} poster already on Cloudinary\n";
            $stats['posters']['skipped']++;
            continue;
        }

        try {
            $fullPath = storage_path('app/public/' . $localPath);
            
            if (!file_exists($fullPath)) {
                echo "  [FAIL] Paper #{$paper->id} poster file not found: {$localPath}\n";
                $stats['posters']['failed']++;
                continue;
            }

            $file = new \Illuminate\Http\UploadedFile(
                $fullPath,
                basename($localPath),
                mime_content_type($fullPath),
                null,
                true
            );

            $result = $cloudinary->uploadPoster($file, $paper->user_id, $paper->event_id);
            
            $paper->poster_path = $result['secure_url'];
            $paper->save();

            echo "  [OK] Paper #{$paper->id} poster migrated\n";
            $stats['posters']['success']++;

        } catch (\Exception $e) {
            echo "  [FAIL] Paper #{$paper->id} poster: " . $e->getMessage() . "\n";
            $stats['posters']['failed']++;
            Log::error('Poster migration failed', [
                'paper_id' => $paper->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Print summary
    echo "\n=== Migration Summary ===\n";
    echo "Certificates:\n";
    echo "  ✓ Success: {$stats['certificates']['success']}\n";
    echo "  ✗ Failed:  {$stats['certificates']['failed']}\n";
    echo "  ⊘ Skipped: {$stats['certificates']['skipped']}\n";
    
    echo "\nResumes:\n";
    echo "  ✓ Success: {$stats['resumes']['success']}\n";
    echo "  ✗ Failed:  {$stats['resumes']['failed']}\n";
    echo "  ⊘ Skipped: {$stats['resumes']['skipped']}\n";
    
    echo "\nPosters:\n";
    echo "  ✓ Success: {$stats['posters']['success']}\n";
    echo "  ✗ Failed:  {$stats['posters']['failed']}\n";
    echo "  ⊘ Skipped: {$stats['posters']['skipped']}\n";
    
    echo "\n=== Migration Complete! ===\n";
    echo "Check storage/logs/laravel.log for any errors.\n\n";

    return $stats;
}

// If you want to clean up local files after migration (BE CAREFUL!)
function cleanupLocalFiles()
{
    echo "\n=== Cleaning up local files ===\n";
    echo "WARNING: This will delete files from local storage.\n";
    echo "Make sure migration was successful before proceeding.\n\n";
    
    $confirm = readline("Type 'YES' to confirm deletion of local files: ");
    
    if ($confirm !== 'YES') {
        echo "Cleanup cancelled.\n";
        return;
    }

    $deleted = 0;
    
    // Delete certificates
    Storage::disk('public')->deleteDirectory('certificates');
    echo "Deleted certificates directory\n";
    $deleted++;
    
    // Delete resumes
    Storage::disk('public')->deleteDirectory('resumes');
    echo "Deleted resumes directory\n";
    $deleted++;
    
    // Delete posters
    Storage::disk('public')->deleteDirectory('posters');
    echo "Deleted posters directory\n";
    $deleted++;

    echo "\n$deleted directories cleaned up.\n";
}
