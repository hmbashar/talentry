<?php

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('returns only published jobs on the public career page', function () {
    $user = User::factory()->recruiter()->create();
    JobPosting::factory()->published()->count(3)->create(['user_id' => $user->id]);
    JobPosting::factory()->draft()->count(2)->create(['user_id' => $user->id]);

    $this->getJson('/api/public/jobs')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('returns a published job detail', function () {
    $user = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $user->id]);

    $this->getJson("/api/public/jobs/{$job->uuid}")
        ->assertOk()
        ->assertJsonPath('data.uuid', $job->uuid);
});

it('returns 404 for draft job on public endpoint', function () {
    $user = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->draft()->create(['user_id' => $user->id]);

    $this->getJson("/api/public/jobs/{$job->uuid}")->assertNotFound();
});

it('allows a candidate to apply to a published job', function () {
    Storage::fake('public');

    $user = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $user->id]);

    $response = $this->postJson("/api/public/jobs/{$job->uuid}/apply", [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'cover_letter' => 'I am very excited about this opportunity.',
        'resume' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.status', 'applied');

    $this->assertDatabaseHas('candidates', ['email' => 'jane@example.com']);
    $this->assertDatabaseHas('applications', ['job_posting_id' => $job->id]);
});

it('rejects apply with missing resume', function () {
    $user = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $user->id]);

    $this->postJson("/api/public/jobs/{$job->uuid}/apply", [
        'name' => 'John',
        'email' => 'john@example.com',
    ])->assertUnprocessable();
});
