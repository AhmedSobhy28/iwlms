<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('student')
            && $user->isEnrolledIn($lesson->course);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can mark the lesson as complete.
     */
    public function complete(User $user, Lesson $lesson): bool
    {
        return $user->hasRole('student')
            && $user->isEnrolledIn($lesson->course)
            && ! $user->hasCompletedLesson($lesson);
    }
}
