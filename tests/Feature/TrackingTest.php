<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Posting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    private function posting(array $attrs = []): Posting
    {
        return Posting::create($attrs + [
            'source' => 'test',
            'external_id' => uniqid(),
            'company' => 'Acme',
            'title' => 'Laravel Developer',
            'url' => 'https://example.com/job',
            'description' => 'Laravel job.',
            'score' => 50,
        ]);
    }

    public function test_dashboard_shows_unapplied_postings_in_queue(): void
    {
        $this->posting(['title' => 'Visible Laravel Job']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible Laravel Job')
            ->assertSee('Applied this week');
    }

    public function test_marking_applied_creates_application_and_sets_applied_at(): void
    {
        $posting = $this->posting();

        $this->post("/postings/{$posting->id}/status", ['status' => 'applied'])
            ->assertRedirect('/');

        $application = Application::where('posting_id', $posting->id)->firstOrFail();
        $this->assertSame('applied', $application->status);
        $this->assertNotNull($application->applied_at);

        // Applied posting leaves the queue; weekly count reflects it.
        $this->get('/')->assertSee('Applied this week: <strong>1</strong>', false);
    }

    public function test_invalid_status_rejected_and_applied_at_not_overwritten(): void
    {
        $posting = $this->posting();

        $this->post("/postings/{$posting->id}/status", ['status' => 'ghosted'])
            ->assertSessionHasErrors('status');

        $this->post("/postings/{$posting->id}/status", ['status' => 'applied']);
        $original = Application::firstOrFail()->applied_at;

        $this->travel(1)->days();
        $this->post("/postings/{$posting->id}/status", ['status' => 'interview']);
        $this->post("/postings/{$posting->id}/status", ['status' => 'applied']);

        $this->assertTrue(Application::firstOrFail()->applied_at->equalTo($original));
    }
}
