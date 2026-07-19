<?php

namespace App\Console\Commands;

use App\Models\Posting;
use Illuminate\Console\Command;

class ScorePostings extends Command
{
    protected $signature = 'score:run {--top=15 : Show the top N after scoring}';

    protected $description = 'Score all postings against the keyword config and show the ranked top';

    public function handle(): int
    {
        foreach (Posting::all() as $posting) {
            $posting->update(['score' => $this->score($posting)]);
        }

        $top = Posting::orderByDesc('score')->limit((int) $this->option('top'))->get();

        $this->table(
            ['Score', 'Source', 'Company', 'Title', 'Location'],
            $top->map(fn ($p) => [
                $p->score,
                $p->source,
                mb_strimwidth($p->company ?? '—', 0, 24, '…'),
                mb_strimwidth($p->title, 0, 44, '…'),
                mb_strimwidth($p->location ?? ($p->remote ? 'remote' : '—'), 0, 22, '…'),
            ]),
        );

        return self::SUCCESS;
    }

    private function score(Posting $posting): int
    {
        $title = strtolower($posting->title);
        $body = strtolower($posting->description.' '.implode(' ', $posting->tags ?? []));

        $score = 0;

        // Title match counts double — descriptions are often keyword-stuffed
        // with every stack a board supports; the title says what the job is.
        foreach (config('jobfinder.keywords') as $keyword => $weight) {
            if (str_contains($title, $keyword)) {
                $score += $weight * 2;
            } elseif (str_contains($body, $keyword)) {
                $score += $weight;
            }
        }

        if (! preg_match('/\b(php|laravel)\b/', $title)) {
            $offStack = implode('|', config('jobfinder.off_stack_title'));

            if (preg_match("/(^|\\W)({$offStack})(\\W|\$)/", $title)) {
                $score += config('jobfinder.off_stack_penalty');
            }
        }

        if ($posting->remote) {
            $score += config('jobfinder.remote_bonus');
        }

        $location = strtolower($posting->location ?? '');

        foreach (config('jobfinder.location_bonus') as $keyword => $bonus) {
            if (str_contains($location, $keyword)) {
                $score += $bonus;
            }
        }

        // "hybrid Manchester" or "Europe only" is not applyable from Madison —
        // unless Madison/Wisconsin is the hybrid location itself.
        if (! str_contains($location, 'madison') && ! str_contains($location, 'wisconsin')) {
            foreach (config('jobfinder.region_penalty') as $keyword => $penalty) {
                if (str_contains($location, $keyword) || str_contains($title, $keyword)) {
                    $score += $penalty;
                }
            }
        }

        return $score;
    }
}
