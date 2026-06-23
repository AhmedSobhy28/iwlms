<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonCompletionController extends Controller
{
    /**
     * Mark a lesson as completed.
     */
    public function store(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->authorize('complete', $lesson);

        $request->user()->lessonCompletions()->create([
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Lesson marked as complete.');
    }
}
