<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('Get cep informations', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Http::fake([
        'https://viacep.com.br/ws/*' => Http::response([
            'cep' => '01310-930',
            'logradouro' => 'Av. Paulista',
            'complemento' => 'Casa',
            'bairro' => 'Bela Vista',
            'localidade' => 'São Paulo',
            'uf' => 'SP',
        ])
    ]);

    $this->getJson(route('api.via-cep', [
        'cep' => '01310-930',
    ]))
        ->assertOk()
        ->assertJsonStructure(['cep', 'logradouro', 'complemento', 'bairro', 'localidade', 'uf']);

    expect(Cache::get('cep-01310930'))->toBeArray();
});

it('Get cep informations failed by invalid value', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Http::fake([
        'https://viacep.com.br/ws/*' => Http::response([
            'erro' => true,
        ])
    ]);

    $this->getJson(route('api.via-cep', [
        'cep' => '11111-111',
    ]))
        ->assertBadRequest()
        ->assertJson(['message' => 'Cep inválido']);
});

it('Get cep informations failed by empty value', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson(route('api.via-cep', [
        'cep' => null,
    ]))
        ->assertBadRequest()
        ->assertJson(['message' => 'Cep inválido']);
});
