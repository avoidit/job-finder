<?php

namespace App\Console\Commands;

use App\Models\Posting;
use Illuminate\Console\Command;

abstract class IngestCommand extends Command
{
    /** Upsert one posting; returns true if newly created. */
    protected function store(array $attrs): bool
    {
        $posting = Posting::updateOrCreate(
            ['source' => $attrs['source'], 'external_id' => $attrs['external_id']],
            $attrs,
        );

        return $posting->wasRecentlyCreated;
    }

    protected function report(string $source, int $new, int $seen): void
    {
        $this->info("{$source}: {$seen} postings fetched, {$new} new.");
    }
}
