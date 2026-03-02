<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Career\ApplyJobRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\JobResource;
use App\Services\ApplicationService;
use App\Services\JobService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CareerController extends Controller
{
    public function __construct(
        private readonly JobService $jobService,
        private readonly ApplicationService $applicationService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $jobs = $this->jobService->allPublished();

        return JobResource::collection($jobs);
    }

    public function show(string $uuid): JobResource
    {
        $job = $this->jobService->findByUuid($uuid);

        abort_unless($job->isPublished(), 404);

        return new JobResource($job);
    }

    public function apply(ApplyJobRequest $request, string $uuid): ApplicationResource
    {
        $job = $this->jobService->findByUuid($uuid);

        abort_unless($job->isPublished(), 404);

        $application = $this->applicationService->applyToJob(
            $job,
            $request->validated(),
            $request->file('resume')
        );

        return new ApplicationResource($application->load(['candidate', 'jobPosting']));
    }
}
