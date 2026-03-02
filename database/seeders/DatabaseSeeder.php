<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationNote;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Admin User',
            'email' => 'admin@talentry.test',
            'password' => Hash::make('password'),
        ]);

        // Create recruiter user
        $recruiter = User::factory()->recruiter()->create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Sarah Recruiter',
            'email' => 'recruiter@talentry.test',
            'password' => Hash::make('password'),
        ]);

        // Create published job postings
        $jobs = JobPosting::factory()->published()->count(5)->create([
            'user_id' => $recruiter->id,
        ]);

        // Create draft job postings
        JobPosting::factory()->draft()->count(2)->create([
            'user_id' => $recruiter->id,
        ]);

        // Create candidates and applications
        $candidates = Candidate::factory()->count(20)->create();

        $candidates->each(function (Candidate $candidate) use ($jobs) {
            $job = $jobs->random();
            $application = Application::factory()->create([
                'job_posting_id' => $job->id,
                'candidate_id' => $candidate->id,
            ]);

            ApplicationNote::factory()->count(rand(0, 2))->create([
                'application_id' => $application->id,
            ]);
        });
    }
}
