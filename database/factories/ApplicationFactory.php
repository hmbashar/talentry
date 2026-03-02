<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Candidate;
use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'job_posting_id' => JobPosting::factory(),
            'candidate_id' => Candidate::factory(),
            'status' => ApplicationStatus::Applied->value,
            'resume_path' => 'resumes/sample-resume.pdf',
            'cover_letter' => fake()->optional()->paragraph(),
        ];
    }
}
