<?php

namespace App\Services;

use App\Models\Posting;

/**
 * Deterministic tailoring (ADR 0002): rank profile bullets and achievements
 * by tag overlap with the keywords found in a posting. No LLM calls.
 */
class Tailor
{
    /** Keywords from config/jobfinder.php present in the posting text. */
    public function postingKeywords(Posting $posting): array
    {
        $haystack = strtolower(implode(' ', [
            $posting->title,
            $posting->description,
            implode(' ', $posting->tags ?? []),
        ]));

        $matched = array_filter(
            array_keys(config('jobfinder.keywords')),
            fn ($keyword) => str_contains($haystack, $keyword),
        );

        // Normalize spelling variants to the canonical tags used in profile.php.
        $aliases = config('jobfinder.aliases');

        return array_values(array_unique(array_map(
            fn ($keyword) => $aliases[$keyword] ?? $keyword,
            $matched,
        )));
    }

    /** Stable-sort tagged items by overlap with keywords, descending. */
    public function rank(array $items, array $keywords): array
    {
        $scored = array_map(
            fn ($item, $i) => $item + ['overlap' => count(array_intersect($item['tags'], $keywords)), 'position' => $i],
            $items,
            array_keys($items),
        );

        usort($scored, fn ($a, $b) => [$b['overlap'], $a['position']] <=> [$a['overlap'], $b['position']]);

        return $scored;
    }

    /** Profile with each job's bullets reordered for this posting. */
    public function tailoredProfile(Posting $posting): array
    {
        $profile = config('profile');
        $keywords = $this->postingKeywords($posting);

        foreach ($profile['jobs'] as $i => $job) {
            $profile['jobs'][$i]['bullets'] = $this->rank($job['bullets'], $keywords);
        }

        $profile['achievements'] = array_slice($this->rank($profile['achievements'], $keywords), 0, 3);

        return $profile;
    }
}
