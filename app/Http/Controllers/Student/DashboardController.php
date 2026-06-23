<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $enrolledCourses = $user->enrolledCourses()
            ->with('category:id,name')
            ->withCount(['lessons', 'lessonCompletions' => fn ($query) => $query->where('user_id', $user->id)])
            ->get();

        $enrolledCourses = $enrolledCourses->map(function ($course) {
            $completedCount = $course->lesson_completions_count;
            $lessonsCount = $course->lessons_count;
            $progress = $lessonsCount > 0 ? round(($completedCount / $lessonsCount) * 100) : 0;

            return [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'thumbnail' => $course->thumbnail,
                'category' => $course->category,
                'lessons_count' => $lessonsCount,
                'completed_count' => $completedCount,
                'progress' => $progress,
            ];
        });

        return Inertia::render('student/Dashboard', [
            'enrolledCourses' => $enrolledCourses,
        ]);
    }
}
