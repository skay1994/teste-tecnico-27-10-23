<?php

use App\Models\Patient;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('Delete a  patient', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Queue::fake();

    $patient = Patient::factory()->create();

    $this->deleteJson(route('api.patients.destroy', [
        'patient' => $patient->getKey()
    ]))->assertNoContent();

    $this->assertDatabaseCount('patients', 0);
});

it('Delete a  patient fail by not found', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->deleteJson(route('api.patients.destroy', [
        'patient' => 1111
    ]))->assertNotFound();
});
