<?php

use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    seedRoles();
});

test('admin login screen can be rendered', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('admins can authenticate using the admin login screen', function () {
    $user = User::factory()->admin()->create();

    $response = $this->post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('students cannot authenticate through the admin login screen', function () {
    $user = User::factory()->student()->create();

    $this->post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('admins can manage categories', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.categories.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('admin.categories.store'), ['name' => 'Science'])
        ->assertRedirect(route('admin.categories.index'));

    expect(Category::query()->where('name', 'Science')->exists())->toBeTrue();

    $category = Category::query()->where('name', 'Science')->first();

    $this->actingAs($admin)
        ->put(route('admin.categories.update', $category), ['name' => 'STEM'])
        ->assertRedirect(route('admin.categories.index'));

    expect($category->fresh()->name)->toBe('STEM');

    $this->actingAs($admin)
        ->delete(route('admin.categories.destroy', $category))
        ->assertRedirect(route('admin.categories.index'));

    expect(Category::query()->count())->toBe(0);
});

test('students cannot manage categories', function () {
    $student = User::factory()->student()->create();
    $category = Category::factory()->create();

    $this->actingAs($student)
        ->get(route('admin.categories.index'))
        ->assertForbidden();

    $this->actingAs($student)
        ->post(route('admin.categories.store'), ['name' => 'Blocked'])
        ->assertForbidden();

    $this->actingAs($student)
        ->put(route('admin.categories.update', $category), ['name' => 'Blocked'])
        ->assertForbidden();

    $this->actingAs($student)
        ->delete(route('admin.categories.destroy', $category))
        ->assertForbidden();
});
