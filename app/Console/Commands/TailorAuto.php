<?php

namespace App\Console\Commands;

use App\Models\Posting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TailorAuto extends Command
{
    protected $signature = 'tailor:auto {--min= : Minimum score (default: config jobfinder.auto_tailor_min)}';

    protected $description = 'Generate tailored documents for every unapplied posting at or above the score threshold';

    public function handle(): int
    {
        $min = (int) ($this->option('min') ?? config('jobfinder.auto_tailor_min'));

        $postings = Posting::where('score', '>=', $min)
            ->doesntHave('application')
            ->get()
            ->filter(fn ($p) => ! File::exists(
                storage_path('app/applications/'.$p->id.'-'.Str::slug($p->company ?? 'unknown').'/resume.md')
            ));

        foreach ($postings as $posting) {
            $this->call('tailor:generate', ['posting' => $posting->id]);
        }

        $this->info("tailor:auto: generated {$postings->count()} (threshold {$min}).");

        return self::SUCCESS;
    }
}
