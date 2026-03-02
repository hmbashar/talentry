<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Recruiter]);
    }

    public function view(User $user, Application $application): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Recruiter]);
    }

    public function updateStatus(User $user, Application $application): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Recruiter]);
    }

    public function addNote(User $user, Application $application): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Recruiter]);
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->isAdmin();
    }
}
