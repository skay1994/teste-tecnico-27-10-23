<?php

use App\Models\User;
use Tests\TestCase;

it('User can logout', function () {
    /** @var TestCase $this $user */
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->getJson(route('api.auth.logout'))
        ->assertOk();

    $this->assertDatabaseEmpty('personal_access_tokens');
});
