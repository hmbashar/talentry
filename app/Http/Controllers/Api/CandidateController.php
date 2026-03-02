<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateResource;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CandidateController extends Controller
{
    public function __construct(private readonly CandidateService $candidateService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $candidates = $this->candidateService->list($request->only(['search']));

        return CandidateResource::collection($candidates);
    }

    public function show(string $uuid): CandidateResource
    {
        $candidate = $this->candidateService->findByUuid($uuid);

        return new CandidateResource($candidate);
    }
}
