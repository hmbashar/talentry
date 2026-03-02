<?php

use App\Models\HomepageSetting;
use App\Models\JobPosting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the homepage with settings, jobs and stats', function () {
    JobPosting::factory()->published()->count(3)->create();

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('settings')
            ->has('jobs')
            ->has('stats')
            ->has('stats.total_jobs')
            ->has('stats.total_applications')
            ->has('stats.total_candidates')
        );
});

it('seeds default settings via firstOrCreate on first visit', function () {
    expect(HomepageSetting::count())->toBe(0);

    $this->get('/')->assertOk();

    expect(HomepageSetting::count())->toBe(1);
    expect(HomepageSetting::first()->content['hero_title'])->toBe('Find Your Dream Job Today');
});

it('only returns published jobs on homepage', function () {
    JobPosting::factory()->published()->count(2)->create();
    JobPosting::factory()->draft()->count(3)->create();

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('stats.total_jobs', 2)
        );
});

it('caps homepage jobs at 6', function () {
    JobPosting::factory()->published()->count(10)->create();

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('jobs', 6)
        );
});

it('admin can retrieve homepage settings via api', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->getJson('/api/homepage-settings')
        ->assertOk()
        ->assertJsonStructure(['data' => ['hero_title', 'footer_tagline']]);
});

it('admin can update homepage settings via api', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patchJson('/api/homepage-settings', ['hero_title' => 'New Headline'])
        ->assertOk()
        ->assertJsonPath('data.hero_title', 'New Headline');

    expect(HomepageSetting::first()->content['hero_title'])->toBe('New Headline');
});

it('recruiter cannot update homepage settings', function () {
    $recruiter = User::factory()->recruiter()->create();

    $this->actingAs($recruiter)
        ->patchJson('/api/homepage-settings', ['hero_title' => 'Hacked'])
        ->assertForbidden();
});

it('guest cannot update homepage settings', function () {
    $this->patchJson('/api/homepage-settings', ['hero_title' => 'Hacked'])
        ->assertUnauthorized();
});

it('validation rejects overly long hero_title', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patchJson('/api/homepage-settings', ['hero_title' => str_repeat('a', 201)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['hero_title']);
});

