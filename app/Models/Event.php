<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'category_id',
        'title',
        'description',
        'short_description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue_name',
        'venue_address',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'max_participants',
        'current_participants',
        'registration_fee',
        'currency',
        'is_free',
        'registration_deadline',
        'status',
        'requires_approval',
        'is_public',
        'allow_waitlist',
        'requirements',
        'tags',
        'contact_email',
        'contact_phone',
        'website_url',
        'slug',
        'featured_image',
        'gallery_images',
        'delivery_mode',
        'f2f_start_date',
        'f2f_end_date',
        'f2f_start_time',
        'f2f_end_time',
        'online_start_date',
        'online_end_date',
        'online_start_time',
        'online_end_time',
        'online_platform_url',
        'innovation_categories',
        'conference_categories',
        'f2f_paper_deadline',
        'f2f_reviewer_registration_deadline',
        'f2f_jury_registration_deadline',
        'f2f_review_deadline',
        'f2f_acceptance_notification_date',
        'f2f_payment_deadline',
        'online_paper_deadline',
        'online_reviewer_registration_deadline',
        'online_jury_registration_deadline',
        'online_review_deadline',
        'online_acceptance_notification_date',
        'online_payment_deadline',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'registration_fee' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_free' => 'boolean',
        'requires_approval' => 'boolean',
        'is_public' => 'boolean',
        'allow_waitlist' => 'boolean',
        'requirements' => 'array',
        'tags' => 'array',
        'gallery_images' => 'array',
        'innovation_categories' => 'array',
        'innovation_theme' => 'array',
        'conference_categories' => 'array',
        'conference_theme' => 'array',
    ];
    
    protected $appends = ['poster_url'];

    // Accessors for PostgreSQL boolean compatibility
    public function getIsFreeAttribute($value)
    {
        if (is_string($value)) {
            return $value === 'true' || $value === 't' || $value === '1';
        }
        return (bool) $value;
    }

    public function getRequiresApprovalAttribute($value)
    {
        if (is_string($value)) {
            return $value === 'true' || $value === 't' || $value === '1';
        }
        return (bool) $value;
    }

    public function getIsPublicAttribute($value)
    {
        if (is_string($value)) {
            return $value === 'true' || $value === 't' || $value === '1';
        }
        return (bool) $value;
    }

    public function getAllowWaitlistAttribute($value)
    {
        if (is_string($value)) {
            return $value === 'true' || $value === 't' || $value === '1';
        }
        return (bool) $value;
    }

    // Boolean mutators for PostgreSQL compatibility
    public function setIsFreeAttribute($value)
    {
        $this->attributes['is_free'] = $value ? 'true' : 'false';
    }

    public function setRequiresApprovalAttribute($value)
    {
        $this->attributes['requires_approval'] = $value ? 'true' : 'false';
    }

    public function setIsPublicAttribute($value)
    {
        $this->attributes['is_public'] = $value ? 'true' : 'false';
    }

    public function setAllowWaitlistAttribute($value)
    {
        $this->attributes['allow_waitlist'] = $value ? 'true' : 'false';
    }
    
    // Accessor for poster URL
    public function getPosterUrlAttribute()
    {
        if (empty($this->attributes['featured_image'])) {
            return null;
        }
        
        $value = $this->attributes['featured_image'];
        
        // If it's already a full URL, return as is
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        
        // Check if there's an external storage URL configured
        $externalStorageUrl = env('EXTERNAL_STORAGE_URL');
        if ($externalStorageUrl) {
            return rtrim($externalStorageUrl, '/') . '/' . ltrim($value, '/');
        }
        
        // Otherwise, generate URL from local storage path
        return url('storage/' . $value);
    }

    // Relationships
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(EventOrganizer::class, 'organizer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function approvedRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'approved');
    }

    public function pendingRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', 'pending');
    }

    public function papers(): HasMany
    {
        return $this->hasMany(EventPaper::class);
    }

    public function paperCategories(): HasMany
    {
        return $this->hasMany(PaperCategory::class);
    }

    public function evaluationRubrics(): HasMany
    {
        return $this->hasMany(EvaluationRubric::class);
    }

    // Check if user is registered for this event
    public function isUserRegistered($userId, $role = null): bool
    {
        $query = $this->registrations()->where('user_id', $userId);
        
        if ($role) {
            $query->where('role', $role);
        }
        
        return $query->exists();
    }

    // Get user's registration for this event
    public function getUserRegistration($userId)
    {
        return $this->registrations()->where('user_id', $userId)->first();
    }

    // Get available roles based on event type
    public function getAvailableRolesAttribute(): array
    {
        try {
            $categoryName = strtolower($this->category->name ?? '');
            
            if (str_contains($categoryName, 'innovation')) {
                // Innovation events: jury and participant
                return ['jury', 'participant'];
            } elseif (str_contains($categoryName, 'conference')) {
                // Conference events: only reviewer and participant (no jury)
                return ['reviewer', 'participant'];
            }
            
            // Default for unknown types
            return ['participant'];
        } catch (\Exception $e) {
            return ['participant'];
        }
    }

    // Get event type name
    public function getEventTypeAttribute(): string
    {
        try {
            $categoryName = strtolower($this->category->name ?? '');
            
            if (str_contains($categoryName, 'innovation')) {
                return 'Innovation';
            } elseif (str_contains($categoryName, 'conference')) {
                return 'Conference';
            }
            
            return 'Event';
        } catch (\Exception $e) {
            return 'Event';
        }
    }

    // Accessors
    public function getIsFullAttribute(): bool
    {
        return $this->max_participants && $this->current_participants >= $this->max_participants;
    }

    public function getAvailableSlotsAttribute(): int
    {
        if (!$this->max_participants) {
            return PHP_INT_MAX;
        }
        return max(0, $this->max_participants - $this->current_participants);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function getCanRegisterAttribute(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        if ($this->is_full && !$this->allow_waitlist) {
            return false;
        }

        return true;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByOrganizer($query, $organizerId)
    {
        return $query->where('organizer_id', $organizerId);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title . '-' . Str::random(6));
            }
        });
    }
}
