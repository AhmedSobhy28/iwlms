<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Auth\Admin\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\Student\AuthenticatedSessionController as StudentAuthenticatedSessionController;
use App\Http\Controllers\Auth\Student\RegisteredUserController as StudentRegisteredUserController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\LessonCompletionController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use Illuminate\Support\Facades\Route;

Route::prefix('student')->name('student.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [StudentAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [StudentAuthenticatedSessionController::class, 'store']);

        Route::get('register', [StudentRegisteredUserController::class, 'create'])
            ->name('register');

        Route::post('register', [StudentRegisteredUserController::class, 'store']);
    });

    Route::middleware(['auth', 'role:student'])->group(function () {
        Route::get('dashboard', StudentDashboardController::class)
            ->name('dashboard');

        Route::get('courses', [StudentCourseController::class, 'index'])
            ->name('courses.index');

        Route::get('courses/{course}', [StudentCourseController::class, 'show'])
            ->name('courses.show');

        Route::post('courses/{course}/enroll', [EnrollmentController::class, 'store'])
            ->name('courses.enroll');

        Route::get('courses/{course}/lessons/{lesson}', [StudentLessonController::class, 'show'])
            ->name('lessons.show');

        Route::post('lessons/{lesson}/complete', [LessonCompletionController::class, 'store'])
            ->name('lessons.complete');

        Route::post('logout', [StudentAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AdminAuthenticatedSessionController::class, 'store']);
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('dashboard', AdminDashboardController::class)
            ->name('dashboard');

        Route::resource('categories', CategoryController::class)
            ->except(['show']);

        Route::resource('courses', CourseController::class);

        Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
            Route::get('create', [AdminLessonController::class, 'create'])->name('create');
            Route::post('/', [AdminLessonController::class, 'store'])->name('store');
            Route::get('{lesson}/edit', [AdminLessonController::class, 'edit'])->name('edit');
            Route::put('{lesson}', [AdminLessonController::class, 'update'])->name('update');
            Route::delete('{lesson}', [AdminLessonController::class, 'destroy'])->name('destroy');
        });

        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });
});
