<?php

use App\Models\Patient;
use App\Models\User;
use Elasticsearch\Client;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('Get a list with 10 first patients', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Queue::fake();

    $mock = $this->mock(Client::class);
    $mock->shouldReceive('index')
        ->andReturn(true);

    Patient::factory()->count(10)->create();

    $response = $this->getJson(route('api.patients.index'))
        ->assertOk();

    $response->assertJsonStructure([
        'data' => [
            [
                'id', 'document', 'cns'
            ]
        ],
        'links',
        'meta' => [
            'current_page',
            'from',
            'last_page'
        ]
    ]);
});

it('Get a list with search', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Queue::fake();

    $mock = $this->mock(Client::class);
    $mock->shouldReceive('index')
        ->andReturn(true);
    $mock->shouldReceive('search')
        ->andReturn([
            'hits' => [
                'total' => [ 'value'=> 3 ],
                'hits' => [
                    [ '_id' => 1 ],
                    [ '_id' => 10 ],
                    [ '_id' => 5 ],
                    [ '_id' => 2 ]
                ]
            ]
        ]);

    Schema::disableForeignKeyConstraints();
    Patient::truncate();
    Schema::enableForeignKeyConstraints();

    Patient::factory()->count(10)->create();


    $response = $this->getJson(route('api.patients.index', [
        's' => 'John Doe',
    ]))->assertOk();
    $body = $response->json();

    $this->assertEquals($body['data'][0]['id'], 1);
    $this->assertEquals($body['data'][1]['id'], 10);
    $this->assertEquals($body['data'][2]['id'], 5);
    $this->assertEquals($body['data'][3]['id'], 2);

    $response->assertJsonStructure([
        'data' => [
            [
                'id', 'document', 'cns'
            ]
        ],
        'links',
        'meta' => [
            'current_page',
            'from',
            'last_page'
        ]
    ]);

    Schema::disableForeignKeyConstraints();
    Patient::truncate();
    Schema::enableForeignKeyConstraints();
});
