<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Recruiter = 'recruiter';

    public function label(): string
    {
        return match ($this) {
            UserRole::Admin => 'Admin',
            UserRole::Recruiter => 'Recruiter',
        };
    }
}
