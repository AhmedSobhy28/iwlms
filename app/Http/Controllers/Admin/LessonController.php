<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course): Response
    {
        $this->authorize('create', Lesson::class);

        return Inertia::render('admin/lessons/Create', [
            'course' => $course->only(['id', 'title', 'slug']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonRequest $request, Course $course): RedirectResponse
    {
        $course->lessons()->create($request->validated());

        return to_route('admin.courses.show', $course)
            ->with('success', 'Lesson created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course, Lesson $lesson): Response
    {
        $this->authorize('update', $lesson);

        abort_unless($lesson->course_id === $course->id, 404);

        return Inertia::render('admin/lessons/Edit', [
            'course' => $course->only(['id', 'title', 'slug']),
            'lesson' => $lesson->only(['id', 'title', 'content', 'order', 'course_id']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        abort_unless($lesson->course_id === $course->id, 404);

        $lesson->update($request->validated());

        return to_route('admin.courses.show', $course)
            ->with('success', 'Lesson updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorize('delete', $lesson);

        abort_unless($lesson->course_id === $course->id, 404);

        $lesson->delete();

        return to_route('admin.courses.show', $course)
            ->with('success', 'Lesson deleted successfully.');
    }
}
