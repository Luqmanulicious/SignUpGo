<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $table = 'event_registrations';
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    protected $fillable = [
        'user_id',
        'event_id',
        'registration_code',
        'role',
        'attendance_mode',
        'status',
        'phone',
        'organization',
        'emergency_contact_name',
        'emergency_contact_phone',
        'certificate_path',
        'certificate_filename',
        'application_notes',
        'admin_notes',
        'registered_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_reason',
        'qr_code',
        'qr_image_path',
        'checked_in_at',
        'jury_categories',
        'jury_themes',
        'reviewer_themes',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'jury_categories' => 'array',
        'jury_themes' => 'array',
        'reviewer_themes' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function paper()
    {
        return $this->hasOne(EventPaper::class, 'user_id', 'user_id')
                    ->where('event_papers.event_id', $this->event_id);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'event_registration_id');
    }

    // Accessors
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute(): bool
    {
        return in_array($this->status, ['approved', 'confirmed']);
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsJuryAttribute(): bool
    {
        return $this->role === 'jury';
    }

    public function getIsParticipantAttribute(): bool
    {
        return $this->role === 'participant';
    }

    public function getIsReviewerAttribute(): bool
    {
        return $this->role === 'reviewer';
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->role) {
            'jury' => 'purple',
            'reviewer' => 'blue',
            'participant' => 'green',
            default => 'gray',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
