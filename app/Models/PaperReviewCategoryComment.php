<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperReviewCategoryComment extends Model
{
    protected $fillable = [
        'paper_review_id',
        'rubric_category_id',
        'comment',
    ];

    public function paperReview(): BelongsTo
    {
        return $this->belongsTo(PaperReview::class, 'paper_review_id');
    }

    // Note: rubric_categories table doesn't have a model yet, using DB queries
}
