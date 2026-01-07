<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Note: Reviews now use rubric_item_scores table through jury_mappings
    // instead of paper_review_scores and paper_review_category_comments

    public function isSelected(): bool
    {
        return $this->status === 'selected';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
