<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Posting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'remote' => 'boolean',
        'posted_at' => 'datetime',
    ];

    public function application(): HasOne
    {
        return $this->hasOne(Application::class);
    }
}
