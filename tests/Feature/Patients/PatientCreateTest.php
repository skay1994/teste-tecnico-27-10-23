<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('Create a patient without address', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    Queue::fake();

    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $this->postJson(route('api.patients.store'), [
        'document' => '903.232.740-21',
        'cns' => '151 1000 4870 0002',
        'name' => fake()->name(),
        'mother_name' => fake()->firstNameFemale,
        'birthdate' => fake()->date(max: '-30 years'),
        'phone' => fake()->numerify('(##) #####-####'),
        'photo' => $file,
    ])->assertCreated();

    $this->assertDatabaseCount('patients', 1)
        ->assertDatabaseHas('patients', [
            'document' => '903.232.740-21'
        ]);

    Storage::disk()->assertExists($file->hashName());
});

it('Create a patient with address', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    Queue::fake();

    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $zipcode = fake()->numerify('#####-###');
    $this->postJson(route('api.patients.store'), [
        'document' => '903.232.740-21',
        'cns' => '151 1000 4870 0002',
        'name' => fake()->name(),
        'mother_name' => fake()->firstNameFemale,
        'birthdate' => fake()->date(max: '-30 years'),
        'phone' => fake()->numerify('(##) #####-####'),
        'photo' => $file,
        'address' => [
            [
                'zipcode' => $zipcode,
                'city' => fake()->city,
                'state' => fake()->word,
                'neighborhood' => fake()->streetName,
                'street' => fake()->streetName,
                'number' => fake()->buildingNumber,
                'complement' => fake()->word,
            ]
        ]
    ])->assertCreated();

    $this->assertDatabaseCount('patients', 1)
        ->assertDatabaseHas('patients', [
            'document' => '903.232.740-21'
        ]);

    $this->assertDatabaseCount('patient_addresses', 1)
        ->assertDatabaseHas('patient_addresses', [
            'zipcode' => $zipcode
        ]);

    Storage::disk()->assertExists($file->hashName());
});

it('Create a patient fail by invalid fields', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    Queue::fake();

    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson(route('api.patients.store'), [
        'document' => '300.587.430-41',
        'cns' => '115 5328 0832 0001',
        'name' => null,
        'mother_name' => null,
        'birthdate' => fake()->word,
        'phone' => fake()->word,
        'photo' => $file,
    ])->assertUnprocessable();

    $response->assertJsonValidationErrorFor('document')
        ->assertJsonValidationErrorFor('cns')
        ->assertJsonValidationErrorFor('name')
        ->assertJsonValidationErrorFor('mother_name')
        ->assertJsonValidationErrorFor('birthdate')
        ->assertJsonValidationErrorFor('phone');
});

it('Create a patient with address fail by invalid fields', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    Queue::fake();

    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson(route('api.patients.store'), [
        'document' => '903.232.740-21',
        'cns' => '151 1000 4870 0002',
        'name' => fake()->name(),
        'mother_name' => fake()->firstNameFemale,
        'birthdate' => fake()->date(max: '-30 years'),
        'phone' => fake()->numerify('(##) #####-####'),
        'photo' => $file,
        'address' => [
            [
                'zipcode' => null,
                'city' => null,
                'state' => null,
                'neighborhood' => null,
                'street' => null,
                'number' => null,
                'complement' => null,
            ]
        ]
    ])->assertUnprocessable();

    $response->assertJsonValidationErrorFor('address.0.zipcode')
        ->assertJsonValidationErrorFor('address.0.city')
        ->assertJsonValidationErrorFor('address.0.state')
        ->assertJsonValidationErrorFor('address.0.neighborhood')
        ->assertJsonValidationErrorFor('address.0.street')
        ->assertJsonValidationErrorFor('address.0.number');
});

it('Create a patient fail by document in use', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    Queue::fake();

    Patient::factory()->create([
        'document' => '721.409.540-84'
    ]);

    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson(route('api.patients.store'), [
        'document' => '721.409.540-84',
        'cns' => '151 1000 4870 0002',
        'name' => fake()->name(),
        'mother_name' => fake()->firstNameFemale,
        'birthdate' => fake()->date(max: '-30 years'),
        'phone' => fake()->numerify('(##) #####-####'),
        'photo' => $file,
    ])->assertUnprocessable();

    $response->assertJsonValidationErrorFor('document');
});

it('Create a patient fail by cns in use', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    Queue::fake();

    Patient::factory()->create([
        'cns' => '233 6606 7320 0004'
    ]);

    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->postJson(route('api.patients.store'), [
        'document' => '721.409.540-84',
        'cns' => '233 6606 7320 0004',
        'name' => fake()->name(),
        'mother_name' => fake()->firstNameFemale,
        'birthdate' => fake()->date(max: '-30 years'),
        'phone' => fake()->numerify('(##) #####-####'),
        'photo' => $file,
    ])->assertUnprocessable();

    $response->assertJsonValidationErrorFor('cns');
});
