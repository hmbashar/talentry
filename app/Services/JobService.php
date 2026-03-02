<?php

namespace App\Services;

use App\Contracts\JobRepositoryInterface;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobService
{
    public function __construct(
        private readonly JobRepositoryInterface $jobRepository,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->jobRepository->all($filters);
    }

    public function findByUuid(string $uuid): JobPosting
    {
        return $this->jobRepository->findByUuid($uuid);
    }

    public function create(User $user, array $data): JobPosting
    {
        $data['user_id'] = $user->id;

        return $this->jobRepository->create($data);
    }

    public function update(JobPosting $jobPosting, array $data): JobPosting
    {
        return $this->jobRepository->update($jobPosting, $data);
    }

    public function delete(JobPosting $jobPosting): void
    {
        $this->jobRepository->delete($jobPosting);
    }

    public function publish(JobPosting $jobPosting): JobPosting
    {
        return $this->jobRepository->publish($jobPosting);
    }

    public function draft(JobPosting $jobPosting): JobPosting
    {
        return $this->jobRepository->draft($jobPosting);
    }

    public function allPublished(): Collection
    {
        return $this->jobRepository->allPublished();
    }
}
