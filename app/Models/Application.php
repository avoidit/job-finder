<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    public const STATUSES = ['queued', 'applied', 'response', 'interview', 'offer', 'rejected'];

    protected $guarded = [];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function posting(): BelongsTo
    {
        return $this->belongsTo(Posting::class);
    }
}
