<?php

use App\Models\User;

test('guests are redirected to the student login page', function () {
    $response = $this->get('/student/dashboard');
    $response->assertRedirect('/student/login');
});

test('students can visit the student dashboard', function () {
    seedRoles();

    $user = User::factory()->student()->create();
    $this->actingAs($user);

    $response = $this->get('/student/dashboard');
    $response->assertStatus(200);
});

test('admins cannot visit the student dashboard', function () {
    seedRoles();

    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get('/student/dashboard');
    $response->assertForbidden();
});
