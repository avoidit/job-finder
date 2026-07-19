<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class IngestRemoteok extends IngestCommand
{
    protected $signature = 'ingest:remoteok';

    protected $description = 'Ingest postings from the RemoteOK API';

    public function handle(): int
    {
        $jobs = Http::timeout(20)
            ->withUserAgent('Mozilla/5.0 (X11; Linux x86_64)')
            ->get('https://remoteok.com/api')
            ->json();

        $new = 0;
        $seen = 0;

        foreach ($jobs as $job) {
            // First array element is a legal/TOS notice, not a posting.
            // Attribution: postings sourced from Remote OK (remoteok.com).
            if (! isset($job['id'], $job['position'])) {
                continue;
            }
            $seen++;

            $new += (int) $this->store([
                'source' => 'remoteok',
                'external_id' => (string) $job['id'],
                'company' => $job['company'] ?: null,
                'title' => $job['position'],
                'url' => $job['url'] ?? "https://remoteok.com/l/{$job['id']}",
                'location' => $job['location'] ?: null,
                'remote' => true, // remote-only board
                'salary' => trim(($job['salary_min'] ?? '') !== '' && ($job['salary_min'] ?? 0) > 0
                    ? '$'.$job['salary_min'].'–$'.$job['salary_max']
                    : '') ?: null,
                'description' => trim(strip_tags($job['description'] ?? '')),
                'tags' => $job['tags'] ?: null,
                'posted_at' => isset($job['date']) ? Carbon::parse($job['date']) : null,
            ]);
        }

        $this->report('remoteok', $new, $seen);

        return self::SUCCESS;
    }
}
