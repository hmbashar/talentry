<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Applied = 'applied';
    case Shortlisted = 'shortlisted';
    case Interview = 'interview';
    case Rejected = 'rejected';
    case Hired = 'hired';

    public function label(): string
    {
        return match ($this) {
            ApplicationStatus::Applied => 'Applied',
            ApplicationStatus::Shortlisted => 'Shortlisted',
            ApplicationStatus::Interview => 'Interview',
            ApplicationStatus::Rejected => 'Rejected',
            ApplicationStatus::Hired => 'Hired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            ApplicationStatus::Applied => 'blue',
            ApplicationStatus::Shortlisted => 'yellow',
            ApplicationStatus::Interview => 'purple',
            ApplicationStatus::Rejected => 'red',
            ApplicationStatus::Hired => 'green',
        };
    }
}
