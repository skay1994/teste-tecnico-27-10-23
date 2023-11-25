<?php

use Tests\TestCase;

it('Create a new user', function () {
    /** @var TestCase $this $user */

    $response = $this->postJson(route('api.auth.register'), [
        'name' => fake()->name,
        'email' => fake()->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertCreated();

    $data = $response->json();

    expect($data)->toHaveKeys(['success', 'token'])
        ->and($data['success'])->toBeTrue();
});

it('Create a user and check returned token is valid', function () {
    /** @var TestCase $this $user */

    $response = $this->postJson(route('api.auth.register'), [
        'name' => fake()->name,
        'email' => fake()->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertCreated();

    $data = $response->json();

    expect($data)->toHaveKeys(['success', 'token'])
        ->and($data['success'])->toBeTrue();

    $token = $data['token'];

    $this->withToken($token)
        ->getJson(route('api.user.profile'))
        ->assertOk();
});

it('Create a new user with invalid fields', function () {
    /** @var TestCase $this $user */

    $response = $this->postJson(route('api.auth.register'), [
        'name' => null,
        'email' => 'email',
        'password' => 'password',
        'password_confirmation' => 'password_confirmation',
    ])->assertUnprocessable();

    $response->assertJsonValidationErrorFor('name')
        ->assertJsonValidationErrorFor('email')
        ->assertJsonValidationErrorFor('password');
});
