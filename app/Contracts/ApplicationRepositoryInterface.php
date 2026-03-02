<?php

namespace App\Contracts;

use App\Models\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ApplicationRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;

    public function findByUuid(string $uuid): Application;

    public function create(array $data): Application;

    public function updateStatus(Application $application, string $status): Application;

    public function delete(Application $application): void;
}
