<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperReviewScore extends Model
{
    protected $fillable = [
        'paper_review_id',
        'rubric_item_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function paperReview(): BelongsTo
    {
        return $this->belongsTo(PaperReview::class, 'paper_review_id');
    }

    public function rubricItem(): BelongsTo
    {
        return $this->belongsTo(EvaluationRubric::class, 'rubric_item_id');
    }
}
