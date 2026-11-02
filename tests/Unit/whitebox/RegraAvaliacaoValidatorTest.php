<?php

test('CT02: Nota minima definida e maxima ausente', function () {
    $regra = new stdClass();
    $regra->notaMinimaGeral = 5.0;
    $regra->notaMaximaGeral = null;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeTrue();
    expect($validator->getMessages())->toBeEmpty();
});

test('CT03: Nota minima ausente e maxima definida', function () {
    $regra = new stdClass();
    $regra->notaMinimaGeral = null;
    $regra->notaMaximaGeral = 10.0;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeTrue();
    expect($validator->getMessages())->toBeEmpty();
});

test('CT04: Nota minima e maxima ausentes', function () {
    $regra = new stdClass();
    $regra->notaMinimaGeral = null;
    $regra->notaMaximaGeral = null;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeTrue();
    expect($validator->getMessages())->toBeEmpty();
});

test('CT06: Presenca abaixo do limite minimo', function () {
    $regra = new stdClass();
    $regra->porcentagemPresenca = -5;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeFalse();
    expect($validator->getMessages())->toContain('A porcentagem de presença deve estar entre 0 e 100.');
});

test('CT07: Presenca acima do limite maximo', function () {
    $regra = new stdClass();
    $regra->porcentagemPresenca = 110;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeFalse();
    expect($validator->getMessages())->toContain('A porcentagem de presença deve estar entre 0 e 100.');
});

test('CT08: Presenca dentro do intervalo valido', function () {
    $regra = new stdClass();
    $regra->porcentagemPresenca = 90;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeTrue();
    expect($validator->getMessages())->toBeEmpty();
});

test('CT10: Falta minima definida e maxima ausente', function () {
    $regra = new stdClass();
    $regra->faltaMinimaGeral = 10;
    $regra->faltaMaximaGeral = null;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeTrue();
    expect($validator->getMessages())->toBeEmpty();
});

test('CT11: Falta minima ausente e maxima definida', function () {
    $regra = new stdClass();
    $regra->faltaMinimaGeral = null;
    $regra->faltaMaximaGeral = 30;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeTrue();
    expect($validator->getMessages())->toBeEmpty();
});

test('CT12: Falta minima e maxima ausentes', function () {
    $regra = new stdClass();
    $regra->faltaMinimaGeral = null;
    $regra->faltaMaximaGeral = null;

    $validator = new RegraAvaliacao_Validators_RegraAvaliacaoValidator();
    $isValid = $validator->isValid($regra);

    expect($isValid)->toBeTrue();
    expect($validator->getMessages())->toBeEmpty();
});
