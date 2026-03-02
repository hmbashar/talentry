<?php

namespace App\Repositories;

use App\Contracts\ApplicationRepositoryInterface;
use App\Models\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ApplicationRepository implements ApplicationRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return Application::query()
            ->with(['jobPosting', 'candidate', 'notes.user'])
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['job_posting_id']), fn ($q) => $q->where('job_posting_id', $filters['job_posting_id']))
            ->latest()
            ->paginate(20);
    }

    public function findByUuid(string $uuid): Application
    {
        return Application::query()
            ->with(['jobPosting', 'candidate', 'notes.user'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data): Application
    {
        $data['uuid'] = Str::uuid();

        return Application::query()->create($data);
    }

    public function updateStatus(Application $application, string $status): Application
    {
        $application->update(['status' => $status]);

        return $application->fresh(['jobPosting', 'candidate', 'notes.user']);
    }

    public function delete(Application $application): void
    {
        $application->delete();
    }
}
