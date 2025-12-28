<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventPaper extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'paper_category_id',
        'product_category',
        'product_theme',
        'title',
        'abstract',
        'poster_path',
        'video_url',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PaperCategory::class, 'paper_category_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PaperEvaluation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PaperReview::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'under_review', 'approved', 'rejected']);
    }

    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function averageScore(): float
    {
        return $this->evaluations()->avg('score') ?? 0;
    }
}
