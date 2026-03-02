<?php

namespace App\Contracts;

use App\Models\JobPosting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;

    public function findByUuid(string $uuid): JobPosting;

    public function create(array $data): JobPosting;

    public function update(JobPosting $jobPosting, array $data): JobPosting;

    public function delete(JobPosting $jobPosting): void;

    public function publish(JobPosting $jobPosting): JobPosting;

    public function draft(JobPosting $jobPosting): JobPosting;

    public function allPublished(): Collection;
}
