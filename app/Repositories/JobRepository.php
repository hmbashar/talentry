<?php

namespace App\Repositories;

use App\Contracts\JobRepositoryInterface;
use App\Enums\JobStatus;
use App\Models\JobPosting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class JobRepository implements JobRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return JobPosting::query()
            ->with(['user', 'applications'])
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['search']), fn ($q) => $q->where('title', 'like', '%'.$filters['search'].'%'))
            ->latest()
            ->paginate(15);
    }

    public function findByUuid(string $uuid): JobPosting
    {
        return JobPosting::query()
            ->with(['user', 'applications.candidate'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data): JobPosting
    {
        $data['uuid'] = Str::uuid();

        return JobPosting::query()->create($data)->fresh();
    }

    public function update(JobPosting $jobPosting, array $data): JobPosting
    {
        $jobPosting->update($data);

        return $jobPosting->fresh();
    }

    public function delete(JobPosting $jobPosting): void
    {
        $jobPosting->delete();
    }

    public function publish(JobPosting $jobPosting): JobPosting
    {
        $jobPosting->update(['status' => JobStatus::Published->value]);

        return $jobPosting->fresh();
    }

    public function draft(JobPosting $jobPosting): JobPosting
    {
        $jobPosting->update(['status' => JobStatus::Draft->value]);

        return $jobPosting->fresh();
    }

    public function allPublished(): Collection
    {
        return JobPosting::query()
            ->published()
            ->withCount('applications')
            ->latest()
            ->get();
    }
}
