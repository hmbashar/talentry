<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;

class DashboardService
{
    public function stats(): array
    {
        $totalJobs = JobPosting::query()->count();
        $publishedJobs = JobPosting::query()->published()->count();
        $totalApplications = Application::query()->count();
        $totalCandidates = Candidate::query()->count();

        $applicationsByStatus = Application::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $recentApplications = Application::query()
            ->with([
                'candidate:id,uuid,name,email',
                'jobPosting:id,uuid,title',
            ])
            ->latest()
            ->limit(5)
            ->get(['id', 'uuid', 'status', 'candidate_id', 'job_posting_id', 'created_at'])
            ->map(fn ($app) => [
                'uuid' => $app->uuid,
                'status' => $app->status->value,
                'status_label' => $app->status->label(),
                'created_at' => $app->created_at->diffForHumans(),
                'candidate' => [
                    'name' => $app->candidate?->name,
                    'email' => $app->candidate?->email,
                ],
                'job' => $app->jobPosting?->title,
                'job_uuid' => $app->jobPosting?->uuid,
            ]);

        return [
            'total_jobs' => $totalJobs,
            'published_jobs' => $publishedJobs,
            'draft_jobs' => $totalJobs - $publishedJobs,
            'total_applications' => $totalApplications,
            'total_candidates' => $totalCandidates,
            'interview_count' => (int) ($applicationsByStatus['interview'] ?? 0),
            'applications_by_status' => $applicationsByStatus,
            'recent_applications' => $recentApplications,
        ];
    }
}
