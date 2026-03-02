<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobController extends Controller
{
    public function __construct(private readonly JobService $jobService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $jobs = $this->jobService->list($request->only(['status', 'search']));

        return JobResource::collection($jobs);
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        $job = $this->jobService->create($request->user(), $request->validated());

        return (new JobResource($job->load('user')))->response()->setStatusCode(201);
    }

    public function show(string $uuid): JobResource
    {
        $job = $this->jobService->findByUuid($uuid);

        return new JobResource($job);
    }

    public function update(UpdateJobRequest $request, string $uuid): JobResource
    {
        $job = $this->jobService->findByUuid($uuid);
        $this->authorize('update', $job);
        $job = $this->jobService->update($job, $request->validated());

        return new JobResource($job);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $job = $this->jobService->findByUuid($uuid);
        $this->authorize('delete', $job);
        $this->jobService->delete($job);

        return response()->json(['message' => 'Job deleted successfully.']);
    }

    public function publish(string $uuid): JobResource
    {
        $job = $this->jobService->findByUuid($uuid);
        $this->authorize('publish', $job);
        $job = $this->jobService->publish($job);

        return new JobResource($job);
    }

    public function draft(string $uuid): JobResource
    {
        $job = $this->jobService->findByUuid($uuid);
        $this->authorize('publish', $job);
        $job = $this->jobService->draft($job);

        return new JobResource($job);
    }
}
