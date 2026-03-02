<?php

use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\User;

it('returns the candidate with their applications in the show endpoint', function () {
    $recruiter = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $recruiter->id]);
    $candidate = Candidate::factory()->create();
    Application::factory()->create([
        'job_posting_id' => $job->id,
        'candidate_id' => $candidate->id,
    ]);

    $this->actingAs($recruiter, 'sanctum')
        ->getJson("/api/candidates/{$candidate->uuid}")
        ->assertOk()
        ->assertJsonPath('data.uuid', $candidate->uuid)
        ->assertJsonCount(1, 'data.applications')
        ->assertJsonPath('data.applications.0.job_posting.uuid', $job->uuid);
});

it('returns candidates list without full application data', function () {
    $recruiter = User::factory()->recruiter()->create();
    $candidate = Candidate::factory()->create();

    $this->actingAs($recruiter, 'sanctum')
        ->getJson('/api/candidates')
        ->assertOk()
        ->assertJsonMissingPath('data.0.applications');
});
