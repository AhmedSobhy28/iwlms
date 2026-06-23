<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of available courses.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Course::class);

        $user = $request->user();

        return Inertia::render('student/courses/Index', [
            'courses' => Course::query()
                ->with('category:id,name')
                ->withCount([
                    'lessons',
                    'lessonCompletions' => fn ($query) => $query->where('user_id', $user->id),
                ])
                ->withExists([
                    'enrollments as is_enrolled' => fn ($query) => $query->where('user_id', $user->id),
                ])
                ->latest()
                ->get(['id', 'title', 'slug', 'description', 'thumbnail', 'category_id'])
                ->map(function (Course $course): array {
                    $lessonsCount = $course->lessons_count;
                    $completedCount = $course->lesson_completions_count;

                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'slug' => $course->slug,
                        'description' => $course->description,
                        'thumbnail' => $course->thumbnail,
                        'category' => $course->category,
                        'lessons_count' => $lessonsCount,
                        'completed_count' => $completedCount,
                        'progress' => $lessonsCount > 0 ? round(($completedCount / $lessonsCount) * 100) : 0,
                        'is_enrolled' => $course->is_enrolled,
                    ];
                }),
        ]);
    }

    /**
     * Display the specified course.
     */
    public function show(Request $request, Course $course): Response
    {
        $user = $request->user();
        $isEnrolled = $user->isEnrolledIn($course);

        $this->authorize('view', $course);

        $course->load([
            'category:id,name',
            'lessons' => fn ($query) => $query->orderBy('order')->select(['id', 'title', 'order', 'course_id']),
        ]);

        $completedLessonIds = $isEnrolled
            ? $user->lessonCompletions()
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->pluck('lesson_id')
            : collect();

        $lessonsCount = $course->lessons->count();
        $completedCount = $completedLessonIds->count();
        $progress = $lessonsCount > 0 ? round(($completedCount / $lessonsCount) * 100) : 0;

        return Inertia::render('student/courses/Show', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail,
                'category' => $course->category,
                'lessons' => $course->lessons->map(fn ($lesson) => [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'order' => $lesson->order,
                    'is_completed' => $completedLessonIds->contains($lesson->id),
                ]),
            ],
            'isEnrolled' => $isEnrolled,
            'progress' => $progress,
        ]);
    }
}
