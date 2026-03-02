<?php

namespace App\Enums;

enum JobStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            JobStatus::Draft => 'Draft',
            JobStatus::Published => 'Published',
        };
    }
}
