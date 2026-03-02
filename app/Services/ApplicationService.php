<?php

namespace App\Services;

use App\Contracts\ApplicationRepositoryInterface;
use App\Contracts\CandidateRepositoryInterface;
use App\Models\Application;
use App\Models\ApplicationNote;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ApplicationService
{
    public function __construct(
        private readonly ApplicationRepositoryInterface $applicationRepository,
        private readonly CandidateRepositoryInterface $candidateRepository,
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->applicationRepository->all($filters);
    }

    public function findByUuid(string $uuid): Application
    {
        return $this->applicationRepository->findByUuid($uuid);
    }

    public function applyToJob(JobPosting $jobPosting, array $data, UploadedFile $resume): Application
    {
        $candidate = $this->candidateRepository->findOrCreateByEmail([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $resumePath = $resume->store('resumes', 'public');

        return $this->applicationRepository->create([
            'job_posting_id' => $jobPosting->id,
            'candidate_id' => $candidate->id,
            'status' => 'applied',
            'resume_path' => $resumePath,
            'cover_letter' => $data['cover_letter'] ?? null,
        ]);
    }

    public function updateStatus(Application $application, string $status): Application
    {
        return $this->applicationRepository->updateStatus($application, $status);
    }

    public function addNote(Application $application, User $user, string $note): ApplicationNote
    {
        return $application->notes()->create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'note' => $note,
        ]);
    }

    public function delete(Application $application): void
    {
        $this->applicationRepository->delete($application);
    }
}
