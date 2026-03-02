<?php

namespace App\Repositories;

use App\Contracts\CandidateRepositoryInterface;
use App\Models\Candidate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CandidateRepository implements CandidateRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return Candidate::query()
            ->withCount('applications')
            ->when(isset($filters['search']), fn ($q) => $q->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('email', 'like', '%'.$filters['search'].'%');
            }))
            ->latest()
            ->paginate(20);
    }

    public function findByUuid(string $uuid): Candidate
    {
        return Candidate::query()
            ->with(['applications.jobPosting'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function findOrCreateByEmail(array $data): Candidate
    {
        return Candidate::query()->firstOrCreate(
            ['email' => $data['email']],
            array_merge($data, ['uuid' => Str::uuid()])
        );
    }
}
