<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Public Career Pages
Route::inertia('/careers', 'Careers/Index')->name('careers.index');
Route::get('/careers/{uuid}', fn (string $uuid) => inertia('Careers/Show', ['uuid' => $uuid]))->name('careers.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Admin / Recruiter ATS routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'Admin/Dashboard')->name('dashboard');
        Route::inertia('jobs', 'Admin/Jobs/Index')->name('jobs.index');
        Route::inertia('jobs/create', 'Admin/Jobs/Create')->name('jobs.create');
        Route::get('jobs/{uuid}/edit', fn (string $uuid) => inertia('Admin/Jobs/Edit', ['uuid' => $uuid]))->name('jobs.edit');
        Route::inertia('applications', 'Admin/Applications/Index')->name('applications.index');
        Route::get('applications/{uuid}', fn (string $uuid) => inertia('Admin/Applications/Show', ['uuid' => $uuid]))->name('applications.show');
        Route::inertia('candidates', 'Admin/Candidates/Index')->name('candidates.index');
        Route::get('candidates/{uuid}', fn (string $uuid) => inertia('Admin/Candidates/Show', ['uuid' => $uuid]))->name('candidates.show');
    });
});

require __DIR__.'/settings.php';
