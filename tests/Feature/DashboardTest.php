<?php

use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

it('returns correct stats including candidates and interview count', function () {
    $recruiter = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $recruiter->id]);
    $candidate = Candidate::factory()->create();
    Application::factory()->create(['job_posting_id' => $job->id, 'candidate_id' => $candidate->id, 'status' => 'interview']);
    Application::factory()->create(['job_posting_id' => $job->id, 'candidate_id' => $candidate->id, 'status' => 'applied']);

    $this->actingAs($recruiter, 'sanctum')
        ->getJson('/api/dashboard/stats')
        ->assertOk()
        ->assertJsonPath('total_applications', 2)
        ->assertJsonPath('total_candidates', 1)
        ->assertJsonPath('published_jobs', 1)
        ->assertJsonPath('interview_count', 1)
        ->assertJsonStructure(['recent_applications' => [['uuid', 'status', 'status_label', 'candidate', 'job']]]);
});
