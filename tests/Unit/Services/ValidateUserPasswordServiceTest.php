<?php

use App\Services\ValidateUserPasswordService;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\ValidationException;
use Mockery as m;
use Tests\TestCase;

uses(TestCase::class);

// Testes de caixa-branca (cobertura de decisões e fluxo de exceções.)

test('B01 Rejeita senha reutilizada', function () {
    $hash = m::mock(Hasher::class);

    $hash->shouldReceive('check')
        ->once()
        ->andReturn(true);

    $service = new ValidateUserPasswordService($hash);

    $service->execute('Senhavalida@321', 'hash-anterior');
})->throws(
    ValidationException::class, 'A senha informada foi usada recentemente.'
);

test('B02 Aceita senha nova valida', function () {
    $hash = m::mock(Hasher::class);

    $hash->shouldReceive('check')
        ->once()
        ->andReturn(false);

    $service = new ValidateUserPasswordService($hash);

    $resultado = $service->execute(
        'SenhaValida@123',
        'hash-anterior'
    );
    expect($resultado)->toBeNull();
});

test('B03 Rejeita senha com menos de 8 caracteres', function () {
    $hash = m::mock(Hasher::class);

    $hash->shouldReceive('check')
        ->once()
        ->andReturn(false);

    $service = new ValidateUserPasswordService($hash);

    try {
        $service->execute('Ab0@cde', 'hash-anterior');
    } catch (ValidationException $ex) {
        expect($ex->errors()['password'])->toBe([
            'A senha deve conter pelo menos 8 caracteres e uma combinação de letras maiúsculas e minúsculas, números e símbolos (!@#$%*).',
        ]);

        return;
    }

    $this->fail('Era esperada uma exceção do tipo ValidationException.');
});

test('B04 Prioriza senha reutilizada mesmo sendo curta', function () {
    $hash = m::mock(Hasher::class);

    $hash->shouldReceive('check')
        ->once()
        ->andReturn(true);

    $service = new ValidateUserPasswordService($hash);

    $service->execute(
        'Ab0@cde',
        'hash-anterior'
    );

})->throws(
    ValidationException::class, 'A senha informada foi usada recentemente.');

test('B05 Trata RuntimeException lançada pelo verificador de hash', function () {
    $hash = m::mock(Hasher::class);

    $hash->shouldReceive('check')
        ->once()
        ->andThrow(new RuntimeException('Erro simulado no hash.'));

    $service = new ValidateUserPasswordService($hash);

    try {
        $service->execute('SenhaValida@123', 'hash-anterior');
    } catch (ValidationException $ex) {
        expect($ex->errors()['password'])
            ->toBe(['Erro simulado no hash.']);

        return;
    }

    $this->fail('Era esperada uma exceção do tipo ValidationException.');
});

test('B06 Propaga TypeError lançado pelo verificador de hash', function () {
    $hash = m::mock(Hasher::class);

    $hash->shouldReceive('check')
        ->once()
        ->andThrow(new TypeError('Erro simulado de tipo no hash.'));

    $service = new ValidateUserPasswordService($hash);
    $service->execute('SenhaValida@123', 'hash-anterior');
})->throws(TypeError::class, 'Erro simulado de tipo no hash.');
