<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Public Career Pages
Route::inertia('/careers', 'Careers/Index')->name('careers.index');
Route::inertia('/careers/{uuid}', 'Careers/Show')->name('careers.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Admin / Recruiter ATS routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'Admin/Dashboard')->name('dashboard');
        Route::inertia('jobs', 'Admin/Jobs/Index')->name('jobs.index');
        Route::inertia('jobs/create', 'Admin/Jobs/Create')->name('jobs.create');
        Route::inertia('jobs/{uuid}/edit', 'Admin/Jobs/Edit')->name('jobs.edit');
        Route::inertia('applications', 'Admin/Applications/Index')->name('applications.index');
        Route::inertia('applications/{uuid}', 'Admin/Applications/Show')->name('applications.show');
        Route::inertia('candidates', 'Admin/Candidates/Index')->name('candidates.index');
        Route::inertia('candidates/{uuid}', 'Admin/Candidates/Show')->name('candidates.show');
    });
});

require __DIR__.'/settings.php';
