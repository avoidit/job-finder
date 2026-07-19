<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class IngestLarajobs extends IngestCommand
{
    protected $signature = 'ingest:larajobs';

    protected $description = 'Ingest postings from the Larajobs RSS feed';

    public function handle(): int
    {
        $xml = simplexml_load_string(Http::timeout(20)->get('https://larajobs.com/feed')->body());

        $new = 0;
        $seen = 0;

        foreach ($xml->channel->item as $item) {
            $job = $item->children('https://larajobs.com');
            $seen++;

            $new += (int) $this->store([
                'source' => 'larajobs',
                'external_id' => (string) $item->link,
                'company' => (string) $job->company ?: null,
                'title' => (string) $item->title,
                'url' => (string) $item->link,
                'location' => (string) $job->location ?: null,
                'remote' => str_contains(strtolower((string) $job->location), 'remote'),
                'salary' => (string) $job->salary ?: null,
                'description' => trim(strip_tags((string) $item->children('http://purl.org/rss/1.0/modules/content/')->encoded ?: (string) $item->description)),
                'posted_at' => Carbon::parse((string) $item->pubDate),
            ]);
        }

        $this->report('larajobs', $new, $seen);

        return self::SUCCESS;
    }
}
