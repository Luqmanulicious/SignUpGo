<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaperCategory extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'description',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function papers(): HasMany
    {
        return $this->hasMany(EventPaper::class);
    }
}
