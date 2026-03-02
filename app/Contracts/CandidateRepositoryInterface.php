<?php

namespace App\Contracts;

use App\Models\Candidate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CandidateRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;

    public function findByUuid(string $uuid): Candidate;

    public function findOrCreateByEmail(array $data): Candidate;
}
