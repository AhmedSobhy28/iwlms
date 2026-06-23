<?php

test('registration screen can be rendered', function () {
    seedRoles();

    $response = $this->get('/student/register');

    $response->assertStatus(200);
});

test('new users can register as students', function () {
    seedRoles();

    $response = $this->post('/student/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('student.dashboard', absolute: false));

    expect(auth()->user()?->hasRole('student'))->toBeTrue();
});
