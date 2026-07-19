<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IngestWwr extends IngestCommand
{
    protected $signature = 'ingest:wwr';

    protected $description = 'Ingest postings from WeWorkRemotely RSS feeds';

    private const FEEDS = [
        'https://weworkremotely.com/categories/remote-back-end-programming-jobs.rss',
        'https://weworkremotely.com/categories/remote-full-stack-programming-jobs.rss',
    ];

    public function handle(): int
    {
        $new = 0;
        $seen = 0;

        foreach (self::FEEDS as $feed) {
            $xml = simplexml_load_string(
                Http::timeout(20)->withUserAgent('Mozilla/5.0 (X11; Linux x86_64)')->get($feed)->body()
            );

            foreach ($xml->channel->item as $item) {
                $seen++;

                // Title convention: "Company: Job Title"
                $title = (string) $item->title;

                $new += (int) $this->store([
                    'source' => 'wwr',
                    'external_id' => (string) ($item->guid ?: $item->link),
                    'company' => Str::contains($title, ':') ? trim(Str::before($title, ':')) : null,
                    'title' => Str::contains($title, ':') ? trim(Str::after($title, ':')) : $title,
                    'url' => (string) $item->link,
                    'location' => (string) $item->region ?: null,
                    'remote' => true, // remote-only board
                    'description' => trim(strip_tags((string) $item->description)),
                    'tags' => array_filter(array_map('trim', explode(',', (string) $item->skills))) ?: null,
                    'posted_at' => Carbon::parse((string) $item->pubDate),
                ]);
            }
        }

        $this->report('wwr', $new, $seen);

        return self::SUCCESS;
    }
}
