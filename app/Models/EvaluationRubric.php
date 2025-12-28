<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationRubric extends Model
{
    protected $fillable = [
        'event_id',
        'criteria_name',
        'description',
        'max_score',
        'order',
    ];

    protected $casts = [
        'max_score' => 'integer',
        'order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PaperEvaluation::class);
    }
}
