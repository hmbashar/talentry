<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, JobPosting $jobPosting): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Recruiter]);
    }

    public function update(User $user, JobPosting $jobPosting): bool
    {
        return $user->isAdmin() || $jobPosting->user_id === $user->id;
    }

    public function delete(User $user, JobPosting $jobPosting): bool
    {
        return $user->isAdmin() || $jobPosting->user_id === $user->id;
    }

    public function restore(User $user, JobPosting $jobPosting): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, JobPosting $jobPosting): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, JobPosting $jobPosting): bool
    {
        return $user->isAdmin() || $jobPosting->user_id === $user->id;
    }
}
