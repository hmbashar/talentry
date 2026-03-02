<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\StoreApplicationNoteRequest;
use App\Http\Requests\Application\UpdateApplicationStatusRequest;
use App\Http\Resources\ApplicationNoteResource;
use App\Http\Resources\ApplicationResource;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applicationService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', \App\Models\Application::class);
        $applications = $this->applicationService->list($request->only(['status', 'job_posting_id']));

        return ApplicationResource::collection($applications);
    }

    public function show(string $uuid): ApplicationResource
    {
        $application = $this->applicationService->findByUuid($uuid);
        $this->authorize('view', $application);

        return new ApplicationResource($application);
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, string $uuid): ApplicationResource
    {
        $application = $this->applicationService->findByUuid($uuid);
        $this->authorize('updateStatus', $application);
        $application = $this->applicationService->updateStatus($application, $request->validated('status'));

        return new ApplicationResource($application);
    }

    public function storeNote(StoreApplicationNoteRequest $request, string $uuid): \Illuminate\Http\JsonResponse
    {
        $application = $this->applicationService->findByUuid($uuid);
        $this->authorize('addNote', $application);
        $note = $this->applicationService->addNote($application, $request->user(), $request->validated('note'));

        return (new ApplicationNoteResource($note->load('user')))->response()->setStatusCode(201);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $application = $this->applicationService->findByUuid($uuid);
        $this->authorize('delete', $application);
        $this->applicationService->delete($application);

        return response()->json(['message' => 'Application deleted successfully.']);
    }
}
