<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperReview extends Model
{
    protected $fillable = [
        'event_paper_id',
        'reviewer_id',
        'status',
        'overall_score',
        'overall_comment',
    ];

    protected $casts = [
        'overall_score' => 'integer',
    ];

    public function paper(): BelongsTo
    {
        return $this->belongsTo(EventPaper::class, 'event_paper_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function isSelected(): bool
    {
        return $this->status === 'selected';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
