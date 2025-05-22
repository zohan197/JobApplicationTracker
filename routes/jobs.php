<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Job\JobListingController;
use App\Http\Controllers\Job\ResumeController;
use App\Http\Controllers\Job\NoteController;
use App\Http\Controllers\Job\ReminderController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Job Listings
    Route::get('/job-listings', [JobListingController::class, 'index'])->name('job-listings.index');
    Route::put('/job-listings/{jobListing}', [JobListingController::class, 'update']);
    Route::post('/job-listings', [JobListingController::class, 'store']);
    Route::delete('/job-listings/{jobListing}', [JobListingController::class, 'destroy']);

    //Resumes
    Route::get('/resumes', [ResumeController::class, 'index'])->name('resumes.index');
    Route::post('/resumes', [ResumeController::class, 'store']);
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy']);
    Route::get('/resumes/{resume}/download', [ResumeController::class, 'download']);

    //Job Notes
    Route::prefix('job-listings/{jobListing}/notes')->group(function () {
        Route::get('/', [NoteController::class, 'index']);
        Route::post('/', [NoteController::class, 'store']);
    });

    Route::prefix('notes/{note}')->group(function () {
        Route::put('/', [NoteController::class, 'update']);
        Route::delete('/', [NoteController::class, 'destroy']);
    });

    //Reminders
    Route::prefix('job-listings/{jobListing}')->group(function () {
    Route::get('reminders', [ReminderController::class, 'index']);
    Route::post('reminders', [ReminderController::class, 'store']);
    Route::put('reminders/{reminder}', [ReminderController::class, 'update']);
    Route::delete('reminders/{reminder}', [ReminderController::class, 'destroy']);
    Route::patch('reminders/{reminder}/mark-as-notified', [ReminderController::class, 'markAsNotified']);
    });

});
