<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    /**
     * Display the specified lesson.
     */
    public function show(Request $request, Course $course, Lesson $lesson): Response
    {
        abort_unless($lesson->course_id === $course->id, 404);

        $this->authorize('view', $lesson);

        $user = $request->user();
        $isCompleted = $user->hasCompletedLesson($lesson);

        $courseLessons = $course->lessons()
            ->orderBy('order')
            ->get(['id', 'title', 'order']);

        $currentIndex = $courseLessons->search(fn (Lesson $item) => $item->id === $lesson->id);
        $previousLesson = $currentIndex > 0 ? $courseLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $courseLessons->count() - 1 ? $courseLessons[$currentIndex + 1] : null;

        return Inertia::render('student/lessons/Show', [
            'course' => $course->only(['id', 'title', 'slug']),
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'content' => $lesson->content,
                'order' => $lesson->order,
                'is_video' => $lesson->isVideoUrl(),
                'is_completed' => $isCompleted,
            ],
            'previousLesson' => $previousLesson?->only(['id', 'title']),
            'nextLesson' => $nextLesson?->only(['id', 'title']),
        ]);
    }
}
