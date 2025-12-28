<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'event_registration_id',
        'overall_rating',
        'content_rating',
        'organization_rating',
        'platform_rating',
        'venue_rating',
        'comments',
        'suggestions',
        'system_feedback',
        'would_recommend',
        'submitted_at',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
        'submitted_at' => 'datetime',
        'overall_rating' => 'integer',
        'content_rating' => 'integer',
        'organization_rating' => 'integer',
        'platform_rating' => 'integer',
        'venue_rating' => 'integer',
    ];

    // Relationships
    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    // Accessors
    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->content_rating,
            $this->organization_rating,
            $this->platform_rating,
            $this->venue_rating,
        ]);

        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : 0;
    }
}
