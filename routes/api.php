<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\Public\CareerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// Public Career Routes (no auth required)
// ─────────────────────────────────────────────
Route::prefix('public')->group(function () {
    Route::get('jobs', [CareerController::class, 'index']);
    Route::get('jobs/{uuid}', [CareerController::class, 'show']);
    Route::post('jobs/{uuid}/apply', [CareerController::class, 'apply']);
});

// ─────────────────────────────────────────────
// Authenticated Admin/Recruiter Routes
// ─────────────────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('user', fn (Request $request) => $request->user());

    // Dashboard
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);

    // Jobs
    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobController::class, 'index']);
        Route::post('/', [JobController::class, 'store']);
        Route::get('{uuid}', [JobController::class, 'show']);
        Route::put('{uuid}', [JobController::class, 'update']);
        Route::delete('{uuid}', [JobController::class, 'destroy']);
        Route::patch('{uuid}/publish', [JobController::class, 'publish']);
        Route::patch('{uuid}/draft', [JobController::class, 'draft']);
    });

    // Applications
    Route::prefix('applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'index']);
        Route::get('{uuid}', [ApplicationController::class, 'show']);
        Route::patch('{uuid}/status', [ApplicationController::class, 'updateStatus']);
        Route::post('{uuid}/notes', [ApplicationController::class, 'storeNote']);
        Route::delete('{uuid}', [ApplicationController::class, 'destroy']);
    });

    // Candidates
    Route::prefix('candidates')->group(function () {
        Route::get('/', [CandidateController::class, 'index']);
        Route::get('{uuid}', [CandidateController::class, 'show']);
    });
});
