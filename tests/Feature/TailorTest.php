<?php

namespace Tests\Feature;

use App\Models\Posting;
use App\Services\Tailor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TailorTest extends TestCase
{
    use RefreshDatabase;

    private function laravelPosting(): Posting
    {
        return Posting::create([
            'source' => 'test',
            'external_id' => 'fixture-1',
            'company' => 'Acme Rail',
            'title' => 'Senior Laravel Developer',
            'url' => 'https://example.com/job/1',
            'remote' => true,
            'description' => 'We need a senior Laravel engineer. PHP, MySQL, REST APIs. Vue a plus.',
        ]);
    }

    public function test_extracts_posting_keywords_deterministically(): void
    {
        $keywords = app(Tailor::class)->postingKeywords($this->laravelPosting());

        foreach (['laravel', 'php', 'mysql', 'rest', 'vue'] as $expected) {
            $this->assertContains($expected, $keywords);
        }

        $this->assertNotContains('sql server', $keywords);
        $this->assertNotContains('full stack', $keywords);
    }

    public function test_ranks_laravel_bullets_first_for_a_laravel_posting(): void
    {
        $profile = app(Tailor::class)->tailoredProfile($this->laravelPosting());

        // RailRCS job: the sole-developer Laravel bullet must rank first.
        $this->assertStringContainsString(
            'Sole developer on a production Laravel platform',
            $profile['jobs'][0]['bullets'][0]['text'],
        );

        // Top cover-letter achievement is the Laravel platform one; top 3 kept.
        $this->assertCount(3, $profile['achievements']);
        $this->assertStringContainsString('production Laravel platform', $profile['achievements'][0]['text']);
    }

    public function test_renders_identical_output_for_identical_input(): void
    {
        $posting = $this->laravelPosting();
        $tailor = app(Tailor::class);

        $render = fn () => view('tailor.resume', ['profile' => $tailor->tailoredProfile($posting)])->render();

        $first = $render();

        $this->assertSame($first, $render());
        $this->assertStringContainsString('# Heath Landers — Full Stack Developer', $first);
        $this->assertStringContainsString('- Sole developer on a production Laravel platform', $first);
    }
}
