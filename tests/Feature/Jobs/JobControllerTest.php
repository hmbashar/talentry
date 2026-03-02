<?php

use App\Models\JobPosting;
use App\Models\User;

it('allows admin to create a job', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/jobs', [
        'title' => 'Senior Laravel Developer',
        'description' => 'We are looking for an experienced Laravel developer.',
        'location' => 'Remote',
        'employment_type' => 'full_time',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'Senior Laravel Developer')
        ->assertJsonPath('data.status', 'draft');
});

it('allows recruiter to create a job', function () {
    $recruiter = User::factory()->recruiter()->create();

    $response = $this->actingAs($recruiter, 'sanctum')->postJson('/api/jobs', [
        'title' => 'Frontend Engineer',
        'description' => 'Build beautiful UIs.',
        'location' => 'New York',
        'employment_type' => 'full_time',
    ]);

    $response->assertStatus(201);
});

it('returns a list of jobs for authenticated users', function () {
    $user = User::factory()->recruiter()->create();
    JobPosting::factory()->count(3)->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')->getJson('/api/jobs')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('allows admins to publish a job', function () {
    $admin = User::factory()->admin()->create();
    $job = JobPosting::factory()->draft()->create(['user_id' => $admin->id]);

    $this->actingAs($admin, 'sanctum')->patchJson("/api/jobs/{$job->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.status', 'published');
});

it('allows admin to delete a job', function () {
    $admin = User::factory()->admin()->create();
    $job = JobPosting::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin, 'sanctum')->deleteJson("/api/jobs/{$job->uuid}")
        ->assertOk();

    $this->assertSoftDeleted('job_postings', ['id' => $job->id]);
});

it('prevents unauthenticated access to jobs api', function () {
    $this->getJson('/api/jobs')->assertUnauthorized();
});
