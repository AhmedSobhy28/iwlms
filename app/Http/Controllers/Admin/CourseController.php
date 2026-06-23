<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Course::class);

        return Inertia::render('admin/courses/Index', [
            'courses' => Course::query()
                ->with('category:id,name')
                ->withCount('lessons', 'enrollments')
                ->latest()
                ->get(['id', 'title', 'slug', 'thumbnail', 'category_id', 'created_at']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Course::class);

        return Inertia::render('admin/courses/Create', [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = Course::query()->create($data);

        return to_route('admin.courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): Response
    {
        $this->authorize('view', $course);

        $course->load([
            'category:id,name',
            'lessons' => fn ($query) => $query->orderBy('order')->select(['id', 'title', 'order', 'course_id']),
        ]);

        return Inertia::render('admin/courses/Show', [
            'course' => $course,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): Response
    {
        $this->authorize('update', $course);

        return Inertia::render('admin/courses/Edit', [
            'course' => $course->only(['id', 'title', 'slug', 'description', 'thumbnail', 'category_id']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $data = $request->safe()->except('thumbnail');

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update($data);

        return to_route('admin.courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return to_route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
