<?php

use App\Rules\CheckMandatoryCensoFields;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use iEducar\Modules\Educacenso\Model\EtapaAgregada;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->rule = new CheckMandatoryCensoFields();

    $this->createDefaultParams = function () {
        $params = new \stdClass;
        $params->ref_cod_instituicao = 1;
        $params->etapa_agregada = 304;
        $params->organizacao_curricular = null;
        $params->etapa_educacenso = null;
        $params->local_funcionamento_diferenciado = null;
        $params->tipo_atendimento = '{0}';
        $params->tipo_mediacao_didatico_pedagogico = App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;
        return $params;
    };

    $this->invokePrivateMethod = function ($object, string $methodName, array $parameters = []) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    };
});

test('etapa do educacenso vazia com formacao geral basica bloqueia', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->etapa_educacenso = null;

    $result = ($this->invokePrivateMethod)($this->rule, 'validaEtapaEducacenso', [$params]);

    expect($result)->toBeFalse();
    expect($this->rule->message())->toContain('Etapa de ensino');
    expect($this->rule->message())->toContain('Formação geral básica');
});

test('etapa do educacenso preenchida com formacao geral basica passa', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->etapa_educacenso = 25;

    $result = ($this->invokePrivateMethod)($this->rule, 'validaEtapaEducacenso', [$params]);

    expect($result)->toBeTrue();
});

test('local de funcionamento diferenciado com etapa proibida bloqueia', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->etapa_agregada = null; // Evita conflito com a validação de combinação de etapas
    $params->local_funcionamento_diferenciado = 2; // Unidade Prisional
    $params->etapa_educacenso = 1; // Etapa proibida para prisional/socioeducativo

    $result = ($this->invokePrivateMethod)($this->rule, 'validaEtapaEducacenso', [$params]);

    expect($result)->toBeFalse();
    expect($this->rule->message())->toContain('Local de funcionamento diferenciado');
});

test('local de funcionamento diferenciado com etapa permitida passa', function () {
    $params = ($this->createDefaultParams)();
    $params->organizacao_curricular = '{' . OrganizacaoCurricular::FORMACAO_GERAL_BASICA . '}';
    $params->local_funcionamento_diferenciado = 2; // Unidade Prisional
    $params->etapa_educacenso = 25; // Etapa permitida

    $result = ($this->invokePrivateMethod)($this->rule, 'validaEtapaEducacenso', [$params]);

    expect($result)->toBeTrue();
});
