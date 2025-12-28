<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperEvaluation extends Model
{
    protected $fillable = [
        'event_paper_id',
        'evaluator_id',
        'evaluation_rubric_id',
        'score',
        'comment',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function paper(): BelongsTo
    {
        return $this->belongsTo(EventPaper::class, 'event_paper_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(EvaluationRubric::class, 'evaluation_rubric_id');
    }
}
