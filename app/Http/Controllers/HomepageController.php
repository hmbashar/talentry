<?php

namespace App\Http\Controllers;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\HomepageSetting;
use App\Models\JobPosting;
use Inertia\Inertia;
use Inertia\Response;

class HomepageController extends Controller
{
    public function __invoke(): Response
    {
        $settings = HomepageSetting::current();

        $jobs = JobPosting::query()
            ->where('status', JobStatus::Published)
            ->with('company:id,name')
            ->withCount('applications')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (JobPosting $job) => [
                'uuid' => $job->uuid,
                'title' => $job->title,
                'location' => $job->location,
                'employment_type' => $job->employment_type->value,
                'employment_type_label' => $job->employment_type->label(),
                'deadline' => $job->deadline?->format('M d, Y'),
                'applications_count' => $job->applications_count,
                'company' => $job->company?->name,
            ]);

        $stats = [
            'total_jobs' => JobPosting::query()->where('status', JobStatus::Published)->count(),
            'total_applications' => Application::query()->count(),
            'total_candidates' => Candidate::query()->count(),
            'employment_types' => collect(EmploymentType::cases())->count(),
        ];

        return Inertia::render('Welcome', [
            'canRegister' => \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::registration()),
            'settings' => $settings->content,
            'jobs' => $jobs,
            'stats' => $stats,
        ]);
    }
}
