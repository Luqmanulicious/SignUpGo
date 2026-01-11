<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $cloudUrl = config('services.cloudinary.cloud_url');
        
        if (!$cloudUrl) {
            throw new \Exception('Cloudinary URL is not configured. Please set CLOUDINARY_URL in your .env file.');
        }

        $this->cloudinary = new Cloudinary($cloudUrl);
    }

    /**
     * Upload a file to Cloudinary
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array ['url' => string, 'public_id' => string, 'secure_url' => string]
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', array $options = []): array
    {
        try {
            // Default options (will be overridden by $options)
            $defaultOptions = [
                'folder' => $folder,
            ];

            // Merge with custom options (custom options override defaults)
            $uploadOptions = array_merge($defaultOptions, $options);

            // Upload to Cloudinary
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                $uploadOptions
            );

            Log::info('File uploaded to Cloudinary', [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'folder' => $folder,
                'format' => $result['format'] ?? 'unknown',
                'resource_type' => $result['resource_type'] ?? 'unknown',
            ]);

            return [
                'url' => $result['url'],
                'secure_url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format' => $result['format'] ?? null,
                'resource_type' => $result['resource_type'] ?? null,
                'bytes' => $result['bytes'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'folder' => $folder,
            ]);
            throw new \Exception('Failed to upload file to Cloudinary: ' . $e->getMessage());
        }
    }

    /**
     * Upload a certificate (PDF, JPG, PNG)
     * 
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function uploadCertificate(UploadedFile $file, int $userId): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
        
        return $this->upload($file, 'certificates', [
            'public_id' => 'certificate_' . $userId . '_' . time(),
            'tags' => ['certificate', 'user_' . $userId],
            'resource_type' => $isImage ? 'image' : 'raw',
            'use_filename' => false,
            'unique_filename' => false,
        ]);
    }

    /**
     * Upload an image file (JPG, PNG, etc.)
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadImage(UploadedFile $file, string $folder = 'images', array $options = []): array
    {
        $defaultOptions = [
            'resource_type' => 'image',
        ];
        
        return $this->upload($file, $folder, array_merge($defaultOptions, $options));
    }

    /**
     * Upload a PDF or document file
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadPdf(UploadedFile $file, string $folder = 'documents', array $options = []): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $defaultOptions = [
            'resource_type' => 'raw',
            'use_filename' => false,
            'unique_filename' => false,
        ];
        
        return $this->upload($file, $folder, array_merge($defaultOptions, $options));
    }

    /**
     * Upload a resume (PDF, DOC, DOCX)
     * 
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function uploadResume(UploadedFile $file, int $userId): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        return $this->upload($file, 'resumes', [
            'public_id' => 'resume_' . $userId . '_' . time(),
            'tags' => ['resume', 'user_' . $userId],
            'resource_type' => 'raw',
            'use_filename' => false,
            'unique_filename' => false,
        ]);
    }

    /**
     * Upload a profile picture
     * 
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function uploadProfilePicture(UploadedFile $file, int $userId): array
    {
        return $this->upload($file, 'profile_pictures', [
            'public_id' => 'profile_' . $userId . '_' . time(),
            'tags' => ['profile_picture', 'user_' . $userId],
            'transformation' => [
                'width' => 500,
                'height' => 500,
                'crop' => 'fill',
                'gravity' => 'face',
                'quality' => 'auto',
            ],
        ]);
    }

    /**
     * Upload a paper poster (for innovation) or paper document (for conference)
     * Handles images (JPG, PNG) and documents (DOC, DOCX, PDF)
     * 
     * @param UploadedFile $file
     * @param int $userId
     * @param int $eventId
     * @return array
     */
    public function uploadPoster(UploadedFile $file, int $userId, int $eventId): array
    {
        // Get the original file extension to preserve it
        $originalExtension = strtolower($file->getClientOriginalExtension());
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Sanitize filename (remove special characters)
        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
        
        // Create public_id WITHOUT extension (Cloudinary adds it automatically)
        $publicId = 'poster_' . $eventId . '_' . $userId . '_' . time() . '_' . $sanitizedName;
        
        // Determine resource type based on file extension
        $resourceType = 'raw'; // Default to raw for documents
        $isImage = in_array($originalExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
        
        if ($isImage) {
            $resourceType = 'image';
        }
        
        return $this->upload($file, 'posters', [
            'public_id' => $publicId,
            'tags' => ['poster', 'event_' . $eventId, 'user_' . $userId],
            'resource_type' => $resourceType,
            'use_filename' => false,
            'unique_filename' => false,
        ]);
    }

    /**
     * Upload a payment receipt
     * 
     * @param UploadedFile $file
     * @param int $registrationId
     * @param int $eventId
     * @return array
     */
    public function uploadPaymentReceipt(UploadedFile $file, int $registrationId, int $eventId): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
        
        return $this->upload($file, 'payment-receipts', [
            'public_id' => 'payment_receipt_' . $eventId . '_' . $registrationId . '_' . time(),
            'tags' => ['payment_receipt', 'event_' . $eventId, 'registration_' . $registrationId],
            'resource_type' => $isImage ? 'image' : 'raw',
            'use_filename' => false,
            'unique_filename' => false,
        ]);
    }

    /**
     * Upload a QR code image
     * 
     * @param string $imageData Base64 or file path
     * @param int $eventId
     * @param int $userId
     * @return array
     */
    public function uploadQrCode(string $imageData, int $eventId, int $userId): array
    {
        try {
            // Default options for QR codes
            $uploadOptions = [
                'folder' => 'qrcodes',
                'public_id' => 'qr_' . $eventId . '_' . $userId . '_' . time(),
                'resource_type' => 'image',
                'tags' => ['qrcode', 'event_' . $eventId, 'user_' . $userId],
            ];

            // Upload to Cloudinary
            $result = $this->cloudinary->uploadApi()->upload(
                $imageData,
                $uploadOptions
            );

            Log::info('QR code uploaded to Cloudinary', [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'event_id' => $eventId,
                'user_id' => $userId,
            ]);

            return [
                'url' => $result['url'],
                'secure_url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format' => $result['format'] ?? null,
                'resource_type' => $result['resource_type'] ?? null,
                'bytes' => $result['bytes'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary QR code upload failed', [
                'error' => $e->getMessage(),
                'event_id' => $eventId,
                'user_id' => $userId,
            ]);
            throw new \Exception('Failed to upload QR code to Cloudinary: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file from Cloudinary by public_id
     * 
     * @param string $publicId
     * @param string $resourceType
     * @return bool
     */
    public function delete(string $publicId, string $resourceType = 'image'): bool
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => $resourceType,
            ]);

            Log::info('File deleted from Cloudinary', [
                'public_id' => $publicId,
                'result' => $result['result'] ?? 'unknown',
            ]);

            return ($result['result'] ?? '') === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed', [
                'error' => $e->getMessage(),
                'public_id' => $publicId,
            ]);
            return false;
        }
    }

    /**
     * Extract public_id from Cloudinary URL
     * 
     * @param string $url
     * @return string|null
     */
    public function getPublicIdFromUrl(string $url): ?string
    {
        // Example URL: https://res.cloudinary.com/cloud-name/image/upload/v1234567890/folder/filename.jpg
        // Public ID: folder/filename
        
        if (empty($url)) {
            return null;
        }

        // Match pattern: /upload/v{version}/{public_id}.{format}
        if (preg_match('#/upload/(?:v\d+/)?(.+)\.[^.]+$#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Delete file by URL
     * 
     * @param string $url
     * @return bool
     */
    public function deleteByUrl(string $url): bool
    {
        $publicId = $this->getPublicIdFromUrl($url);
        
        if (!$publicId) {
            Log::warning('Could not extract public_id from URL', ['url' => $url]);
            return false;
        }

        // Determine resource type from URL
        $resourceType = 'image';
        if (str_contains($url, '/raw/') || str_contains($url, '.pdf') || str_contains($url, '.doc')) {
            $resourceType = 'raw';
        } elseif (str_contains($url, '/video/')) {
            $resourceType = 'video';
        }

        return $this->delete($publicId, $resourceType);
    }
}
