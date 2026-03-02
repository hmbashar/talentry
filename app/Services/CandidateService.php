<?php

namespace App\Services;

use App\Contracts\CandidateRepositoryInterface;
use App\Models\Candidate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CandidateService
{
    public function __construct(
        private readonly CandidateRepositoryInterface $candidateRepository,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->candidateRepository->all($filters);
    }

    public function findByUuid(string $uuid): Candidate
    {
        return $this->candidateRepository->findByUuid($uuid);
    }
}
