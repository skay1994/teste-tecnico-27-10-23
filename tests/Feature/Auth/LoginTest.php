<?php

use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

it('Login with valid credentials successfully', function () {
    /** @var TestCase $this $user */

    $password = 'password';
    $user = User::factory()->create([
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson(route('api.auth.login'), [
        'email' => $user->email,
        'password' => $password,
    ])->assertOk();

    $data = $response->json();

    expect($data)
        ->toHaveKeys(['token', 'success'])
        ->and($data['success'])->toBeTrue();
});

it('Login with valid user using invalid credentials fails', function () {
    /** @var TestCase $this $user */

    $user = User::factory()->create([
        'password' => Hash::make(Str::password()),
    ]);

    /** @var TestCase $this $user */
    $response = $this->postJson(route('api.auth.login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertUnprocessable();

    $response->assertJsonValidationErrorFor('email');
});

it('Login with invalid credentials fails', function () {
    /** @var TestCase $this $user */

    $response = $this->postJson(route('api.auth.login'), [
        'email' => fake()->email,
        'password' => 'password',
    ])->assertUnprocessable();

    $response->assertJsonValidationErrorFor('email');
});
