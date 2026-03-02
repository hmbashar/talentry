<?php

namespace App\Services;

use App\Models\Application;
use App\Models\JobPosting;

class DashboardService
{
    public function stats(): array
    {
        $totalJobs = JobPosting::query()->count();
        $publishedJobs = JobPosting::query()->published()->count();
        $totalApplications = Application::query()->count();

        $applicationsByStatus = Application::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $topJobs = JobPosting::query()
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->limit(5)
            ->get(['id', 'uuid', 'title', 'status']);

        return [
            'total_jobs' => $totalJobs,
            'published_jobs' => $publishedJobs,
            'draft_jobs' => $totalJobs - $publishedJobs,
            'total_applications' => $totalApplications,
            'applications_by_status' => $applicationsByStatus,
            'top_jobs' => $topJobs,
        ];
    }
}
