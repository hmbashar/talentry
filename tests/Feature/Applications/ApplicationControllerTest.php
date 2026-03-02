<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\User;

it('allows recruiter to list applications', function () {
    $recruiter = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $recruiter->id]);
    $candidate = Candidate::factory()->create();
    Application::factory()->count(3)->create([
        'job_posting_id' => $job->id,
        'candidate_id' => $candidate->id,
    ]);

    $this->actingAs($recruiter, 'sanctum')->getJson('/api/applications')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('allows recruiter to update application status', function () {
    $recruiter = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $recruiter->id]);
    $candidate = Candidate::factory()->create();
    $application = Application::factory()->create([
        'job_posting_id' => $job->id,
        'candidate_id' => $candidate->id,
    ]);

    $this->actingAs($recruiter, 'sanctum')
        ->patchJson("/api/applications/{$application->uuid}/status", [
            'status' => ApplicationStatus::Shortlisted->value,
        ])
        ->assertOk()
        ->assertJsonPath('data.status', 'shortlisted');
});

it('allows recruiter to add notes to an application', function () {
    $recruiter = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $recruiter->id]);
    $candidate = Candidate::factory()->create();
    $application = Application::factory()->create([
        'job_posting_id' => $job->id,
        'candidate_id' => $candidate->id,
    ]);

    $this->actingAs($recruiter, 'sanctum')
        ->postJson("/api/applications/{$application->uuid}/notes", [
            'note' => 'Great candidate, very promising.',
        ])
        ->assertStatus(201)
        ->assertJsonPath('data.note', 'Great candidate, very promising.');
});

it('prevents unauthenticated access to applications', function () {
    $this->getJson('/api/applications')->assertUnauthorized();
});
