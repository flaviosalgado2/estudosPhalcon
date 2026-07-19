<?php
declare(strict_types=1);

use Phalcon\Di\Di;

beforeEach(function () {
    // Limpar a tabela antes de cada teste
    $db = Di::getDefault()->get('db');
    $db->execute('TRUNCATE TABLE pessoa_fisica RESTART IDENTITY');
});

function httpRequest(string $method, string $path, array $data = []): string
{
    static $cookieFile = null;

    if ($cookieFile === null) {
        $cookieFile = sys_get_temp_dir() . '/pest_phalcon_cookies_' . getmypid() . '.txt';
        touch($cookieFile);
    }

    $url = 'http://localhost:8000' . $path;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    curl_close($ch);

    return $response === false ? '' : $response;
}

it('exibe a listagem vazia de pessoas físicas', function () {
    $response = httpRequest('GET', '/pessoa-fisica');

    expect($response)->toContain('Pessoas Físicas');
    expect($response)->toContain('Nenhuma pessoa física cadastrada');
});

it('cadastra uma pessoa física via controller', function () {
    $response = httpRequest('POST', '/pessoa-fisica/cadastrar', [
        'nome' => 'Maria Oliveira',
        'cpf' => '529.982.247-25',
        'data_nascimento' => '1985-03-20',
        'email' => 'maria@email.com',
        'telefone' => '(21) 91234-5678',
    ]);

    expect($response)->toContain('Pessoas Físicas');
    expect($response)->toContain('Maria Oliveira');

    $pessoa = PessoaFisica::findFirstByCpf('529.982.247-25');
    expect($pessoa)->not->toBeNull();
    expect($pessoa->nome)->toBe('Maria Oliveira');
});

it('rejeita cadastro com cpf duplicado', function () {
    httpRequest('POST', '/pessoa-fisica/cadastrar', [
        'nome' => 'Primeira Pessoa',
        'cpf' => '529.982.247-25',
    ]);

    $response = httpRequest('POST', '/pessoa-fisica/cadastrar', [
        'nome' => 'Segunda Pessoa',
        'cpf' => '529.982.247-25',
    ]);

    expect($response)->toContain('Já existe uma pessoa cadastrada com este CPF');
    expect(PessoaFisica::find())->toHaveCount(1);
});

it('rejeita cadastro com e-mail inválido', function () {
    $response = httpRequest('POST', '/pessoa-fisica/cadastrar', [
        'nome' => 'Teste Email',
        'cpf' => '111.222.333-44',
        'email' => 'email-invalido',
    ]);

    expect($response)->toContain('O e-mail informado é inválido');
    expect(PessoaFisica::find())->toHaveCount(0);
});

it('edita uma pessoa física existente', function () {
    httpRequest('POST', '/pessoa-fisica/cadastrar', [
        'nome' => 'Carlos Souza',
        'cpf' => '529.982.247-25',
    ]);

    $pessoa = PessoaFisica::findFirstByCpf('529.982.247-25');

    $response = httpRequest('POST', '/pessoa-fisica/atualizar', [
        'id' => $pessoa->id,
        'nome' => 'Carlos Souza Editado',
        'cpf' => '529.982.247-25',
    ]);

    expect($response)->toContain('Carlos Souza Editado');

    $pessoaAtualizada = PessoaFisica::findFirstById($pessoa->id);
    expect($pessoaAtualizada->nome)->toBe('Carlos Souza Editado');
});

it('exclui uma pessoa física', function () {
    httpRequest('POST', '/pessoa-fisica/cadastrar', [
        'nome' => 'Ana Paula',
        'cpf' => '529.982.247-25',
    ]);

    $pessoa = PessoaFisica::findFirstByCpf('529.982.247-25');

    $response = httpRequest('GET', '/pessoa-fisica/excluir/' . $pessoa->id);

    expect($response)->toContain('Nenhuma pessoa física cadastrada');
    expect(PessoaFisica::findFirstById($pessoa->id))->toBeNull();
});
