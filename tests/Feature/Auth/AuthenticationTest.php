<?php

use App\Models\User;

test('login screen can be rendered', function () {
    seedRoles();

    $response = $this->get('/student/login');

    $response->assertStatus(200);
});

test('students can authenticate using the login screen', function () {
    seedRoles();

    $user = User::factory()->student()->create();

    $response = $this->post('/student/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('student.dashboard', absolute: false));
});

test('admins cannot authenticate through the student login screen', function () {
    seedRoles();

    $user = User::factory()->admin()->create();

    $this->post('/student/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('users can not authenticate with invalid password', function () {
    seedRoles();

    $user = User::factory()->student()->create();

    $this->post('/student/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('students can logout', function () {
    seedRoles();

    $user = User::factory()->student()->create();

    $response = $this->actingAs($user)->post('/student/logout');

    $this->assertGuest();
    $response->assertRedirect(route('student.login', absolute: false));
});
