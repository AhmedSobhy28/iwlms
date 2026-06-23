<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Enroll the student in a course.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        $request->user()->enrollments()->create([
            'course_id' => $course->id,
        ]);

        return to_route('student.courses.show', $course)
            ->with('success', 'You have enrolled in this course.');
    }
}
