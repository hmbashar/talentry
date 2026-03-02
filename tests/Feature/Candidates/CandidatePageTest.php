<?php

use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the candidates show page with the uuid prop', function () {
    $recruiter = User::factory()->recruiter()->create();
    $candidate = Candidate::factory()->create();

    $this->actingAs($recruiter)
        ->get("/admin/candidates/{$candidate->uuid}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Candidates/Show')
            ->where('uuid', $candidate->uuid)
        );
});

it('renders the applications show page with the uuid prop', function () {
    $recruiter = User::factory()->recruiter()->create();
    $job = JobPosting::factory()->published()->create(['user_id' => $recruiter->id]);
    $candidate = Candidate::factory()->create();
    $application = Application::factory()->create([
        'job_posting_id' => $job->id,
        'candidate_id' => $candidate->id,
    ]);

    $this->actingAs($recruiter)
        ->get("/admin/applications/{$application->uuid}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Applications/Show')
            ->where('uuid', $application->uuid)
        );
});
