<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class IngestHn extends IngestCommand
{
    protected $signature = 'ingest:hn';

    protected $description = 'Ingest stack-matching comments from the latest HN Who is Hiring thread';

    private const API = 'https://hn.algolia.com/api/v1/search_by_date';

    public function handle(): int
    {
        $thread = Http::timeout(20)->get(self::API, [
            'tags' => 'story,author_whoishiring',
            'hitsPerPage' => 5,
        ])->json('hits');

        $story = collect($thread)->first(fn ($hit) => str_contains($hit['title'], 'Who is hiring?'));

        if (! $story) {
            $this->error('hn: no Who is hiring thread found.');

            return self::FAILURE;
        }

        $new = 0;
        $seen = 0;
        $page = 0;

        do {
            $response = Http::timeout(20)->get(self::API, [
                'tags' => "comment,story_{$story['objectID']}",
                'hitsPerPage' => 1000,
                'page' => $page,
            ])->json();

            foreach ($response['hits'] as $comment) {
                $text = trim(html_entity_decode(
                    strip_tags(str_replace(['<p>', '</p>'], "\n", $comment['comment_text'] ?? '')),
                    ENT_QUOTES | ENT_HTML5,
                ));

                // Algolia fuzzy-match is unreliable (spike finding: "largely"
                // matched "laravel") — filter keywords ourselves.
                if ($text === '' || ! Str::contains(strtolower($text), config('jobfinder.hn_filter'))) {
                    continue;
                }
                $seen++;

                // Convention: first line is "Company | REMOTE | type | location"
                $firstLine = Str::of($text)->before("\n");
                $company = trim($firstLine->before('|'));

                $new += (int) $this->store([
                    'source' => 'hn',
                    'external_id' => $comment['objectID'],
                    'company' => $company !== '' ? Str::limit($company, 120) : null,
                    'title' => Str::limit((string) $firstLine, 200),
                    'url' => "https://news.ycombinator.com/item?id={$comment['objectID']}",
                    'remote' => str_contains(strtolower($text), 'remote'),
                    'description' => $text,
                    'posted_at' => Carbon::parse($comment['created_at']),
                ]);
            }

            $page++;
        } while ($page < ($response['nbPages'] ?? 1));

        $this->report('hn', $new, $seen);

        return self::SUCCESS;
    }
}
