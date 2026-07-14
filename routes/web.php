<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\InternController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dynamic dashboard redirect based on role
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'mentor') {
        return redirect()->route('mentor.dashboard');
    } else {
        return redirect()->route('intern.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// Profile management (Breeze standard)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/interns', [AdminController::class, 'storeIntern'])->name('interns.store');
    Route::match(['PUT', 'POST'], '/interns/{user}', [AdminController::class, 'updateIntern'])->name('interns.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
});

// Mentor-only Routes
Route::middleware(['auth', 'role:mentor'])->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/dashboard', [MentorController::class, 'index'])->name('dashboard');
    Route::post('/tasks', [MentorController::class, 'storeTask'])->name('tasks.store');
    Route::match(['PUT', 'POST'], '/tasks/{task}', [MentorController::class, 'updateTask'])->name('tasks.update');
    Route::delete('/tasks/{task}', [MentorController::class, 'destroyTask'])->name('tasks.destroy');
    Route::post('/submissions/{submission}/grade', [MentorController::class, 'gradeSubmission'])->name('submissions.grade');
    Route::post('/interns/{user}/certificate', [MentorController::class, 'uploadCertificate'])->name('interns.certificate');
});

// Intern-only Routes
Route::middleware(['auth', 'role:intern'])->prefix('intern')->name('intern.')->group(function () {
    Route::get('/dashboard', [InternController::class, 'index'])->name('dashboard');
    Route::post('/tasks/{task}/submit', [InternController::class, 'submitTask'])->name('tasks.submit');
    Route::post('/upload-letter', [InternController::class, 'uploadLetter'])->name('upload_letter');
});

// Shared Admin & Mentor Reports Routes
Route::middleware(['auth', 'role:admin,mentor'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('pdf');
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('excel');
});

require __DIR__.'/auth.php';
