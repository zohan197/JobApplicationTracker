<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/dashboard-stats', [DashboardController::class, 'dashboardStats']);
Route::get('/upcoming-reminders', [DashboardController::class, 'upcomingReminders']);
Route::get('/dashboard/reminders', [DashboardController::class, 'upcomingReminders']);



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/jobs.php';

