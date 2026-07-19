<?php

namespace App\Console\Commands;

use App\Models\Posting;
use App\Services\Tailor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TailorGenerate extends Command
{
    protected $signature = 'tailor:generate {posting : Posting ID}';

    protected $description = 'Generate a tailored resume.md and cover.md for a posting';

    public function handle(Tailor $tailor): int
    {
        $posting = Posting::findOrFail($this->argument('posting'));
        $profile = $tailor->tailoredProfile($posting);

        $dir = storage_path('app/applications/'.$posting->id.'-'.Str::slug($posting->company ?? 'unknown'));
        File::ensureDirectoryExists($dir);

        File::put("{$dir}/resume.md", view('tailor.resume', ['profile' => $profile])->render());
        File::put("{$dir}/cover.md", view('tailor.cover', ['profile' => $profile, 'posting' => $posting])->render());

        $this->info("Generated in {$dir}");
        $this->line('Keywords matched: '.implode(', ', $tailor->postingKeywords($posting)));
        $this->warn('Hand-edit cover.md before sending.');

        return self::SUCCESS;
    }
}
