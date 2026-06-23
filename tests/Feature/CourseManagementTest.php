<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRoles();
    Storage::fake('public');
});

test('admins can manage courses and lessons', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.courses.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('admin.courses.store'), [
            'title' => 'Laravel Basics',
            'slug' => 'laravel-basics',
            'description' => 'Learn Laravel from scratch.',
            'category_id' => $category->id,
            'thumbnail' => UploadedFile::fake()->create('course.jpg', 100, 'image/jpeg'),
        ])
        ->assertRedirect();

    $course = Course::query()->where('slug', 'laravel-basics')->first();
    expect($course)->not->toBeNull();
    expect($course->thumbnail)->not->toBeNull();
    Storage::disk('public')->assertExists($course->thumbnail);

    $this->actingAs($admin)
        ->post(route('admin.courses.lessons.store', $course), [
            'title' => 'Introduction',
            'content' => 'Welcome to the course.',
            'order' => 1,
        ])
        ->assertRedirect(route('admin.courses.show', $course));

    $lesson = Lesson::query()->where('course_id', $course->id)->first();
    expect($lesson)->not->toBeNull();

    $this->actingAs($admin)
        ->put(route('admin.courses.lessons.update', [$course, $lesson]), [
            'title' => 'Getting Started',
            'content' => 'Updated content.',
            'order' => 1,
        ])
        ->assertRedirect(route('admin.courses.show', $course));

    expect($lesson->fresh()->title)->toBe('Getting Started');

    $this->actingAs($admin)
        ->put(route('admin.courses.update', $course), [
            'title' => 'Laravel Fundamentals',
            'slug' => 'laravel-fundamentals',
            'description' => 'Updated description.',
            'category_id' => $category->id,
        ])
        ->assertRedirect(route('admin.courses.show', $course->fresh()));

    expect($course->fresh()->title)->toBe('Laravel Fundamentals');

    $this->actingAs($admin)
        ->delete(route('admin.courses.lessons.destroy', [$course->fresh(), $lesson]))
        ->assertRedirect(route('admin.courses.show', $course->fresh()));

    expect(Lesson::query()->count())->toBe(0);

    $this->actingAs($admin)
        ->delete(route('admin.courses.destroy', $course->fresh()))
        ->assertRedirect(route('admin.courses.index'));

    expect(Course::query()->count())->toBe(0);
});

test('students cannot manage courses', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();

    $this->actingAs($student)
        ->get(route('admin.courses.index'))
        ->assertForbidden();

    $this->actingAs($student)
        ->post(route('admin.courses.store'), [
            'title' => 'Blocked',
            'slug' => 'blocked',
            'description' => 'Should not work.',
            'category_id' => $course->category_id,
        ])
        ->assertForbidden();
});

test('students can enroll complete lessons and track progress', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();
    $lessons = Lesson::factory()->count(2)->sequence(
        ['order' => 1],
        ['order' => 2],
    )->create(['course_id' => $course->id]);

    $this->actingAs($student)
        ->get(route('student.courses.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/courses/Index')
            ->has('courses', 1)
            ->where('courses.0.is_enrolled', false)
            ->where('courses.0.progress', 0)
        );

    $this->actingAs($student)
        ->get(route('student.courses.show', $course))
        ->assertOk();

    $this->actingAs($student)
        ->get(route('student.lessons.show', [$course, $lessons[0]]))
        ->assertForbidden();

    $this->actingAs($student)
        ->post(route('student.courses.enroll', $course))
        ->assertRedirect(route('student.courses.show', $course));

    expect(Enrollment::query()->where('user_id', $student->id)->where('course_id', $course->id)->exists())->toBeTrue();

    $this->actingAs($student)
        ->get(route('student.courses.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/courses/Index')
            ->where('courses.0.is_enrolled', true)
            ->where('courses.0.progress', 0)
        );

    $this->actingAs($student)
        ->get(route('student.lessons.show', [$course, $lessons[0]]))
        ->assertOk();

    $this->actingAs($student)
        ->post(route('student.lessons.complete', $lessons[0]))
        ->assertRedirect();

    expect(LessonCompletion::query()->where('user_id', $student->id)->where('lesson_id', $lessons[0]->id)->exists())->toBeTrue();

    $this->actingAs($student)
        ->post(route('student.lessons.complete', $lessons[0]))
        ->assertForbidden();

    $this->actingAs($student)
        ->get(route('student.courses.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/courses/Index')
            ->where('courses.0.is_enrolled', true)
            ->where('courses.0.progress', 50)
            ->where('courses.0.completed_count', 1)
        );

    $this->actingAs($student)
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('student/Dashboard')
            ->has('enrolledCourses', 1)
            ->where('enrolledCourses.0.progress', 50)
        );
});

test('students cannot enroll twice in the same course', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->create();

    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
    ]);

    $this->actingAs($student)
        ->post(route('student.courses.enroll', $course))
        ->assertForbidden();
});
