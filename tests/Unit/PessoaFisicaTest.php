<?php
declare(strict_types=1);

use Phalcon\Di\Di;

beforeEach(function () {
    // Limpar a tabela antes de cada teste
    $db = Di::getDefault()->get('db');
    $db->execute('TRUNCATE TABLE pessoa_fisica RESTART IDENTITY');
});

it('cadastra uma pessoa física válida', function () {
    $pessoa = new PessoaFisica();
    $pessoa->nome = 'João Silva';
    $pessoa->cpf = '123.456.789-09';
    $pessoa->data_nascimento = '1990-05-15';
    $pessoa->email = 'joao@email.com';
    $pessoa->telefone = '(11) 98765-4321';

    expect($pessoa->save())->toBeTrue();
    expect($pessoa->id)->toBeInt();
    expect($pessoa->id)->toBeGreaterThan(0);
});

it('rejeita pessoa física sem nome', function () {
    $pessoa = new PessoaFisica();
    $pessoa->cpf = '123.456.789-09';

    expect($pessoa->save())->toBeFalse();
    expect($pessoa->getMessages())->toHaveCount(1);
    expect($pessoa->getMessages()[0]->getMessage())->toBe('O nome é obrigatório');
});

it('rejeita pessoa física sem cpf', function () {
    $pessoa = new PessoaFisica();
    $pessoa->nome = 'João Silva';

    expect($pessoa->save())->toBeFalse();
    expect($pessoa->getMessages())->toHaveCount(1);
    expect($pessoa->getMessages()[0]->getMessage())->toBe('O CPF é obrigatório');
});

it('rejeita e-mail inválido', function () {
    $pessoa = new PessoaFisica();
    $pessoa->nome = 'João Silva';
    $pessoa->cpf = '123.456.789-09';
    $pessoa->email = 'email-invalido';

    expect($pessoa->save())->toBeFalse();
    expect($pessoa->getMessages())->toHaveCount(1);
    expect($pessoa->getMessages()[0]->getMessage())->toBe('O e-mail informado é inválido');
});

it('atualiza uma pessoa física existente', function () {
    $pessoa = new PessoaFisica();
    $pessoa->nome = 'João Silva';
    $pessoa->cpf = '123.456.789-09';
    $pessoa->save();

    $pessoa->nome = 'João Silva Atualizado';

    expect($pessoa->save())->toBeTrue();
    expect($pessoa->nome)->toBe('João Silva Atualizado');
});

it('exclui uma pessoa física', function () {
    $pessoa = new PessoaFisica();
    $pessoa->nome = 'João Silva';
    $pessoa->cpf = '123.456.789-09';
    $pessoa->save();

    expect($pessoa->delete())->toBeTrue();
    expect(PessoaFisica::findFirstById($pessoa->id))->toBeNull();
});
