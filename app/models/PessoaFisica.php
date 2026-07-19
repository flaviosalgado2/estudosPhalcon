<?php
declare(strict_types=1);

use Phalcon\Mvc\Model;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\StringLength;
use Phalcon\Filter\Validation\Validator\Email as EmailValidator;

class PessoaFisica extends Model
{
    public ?int $id = null;
    public string $nome = '';
    public string $cpf = '';
    public ?string $data_nascimento = null;
    public ?string $email = null;
    public ?string $telefone = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function initialize(): void
    {
        $this->setSource('pessoa_fisica');
        $this->setSchema('public');
    }

    public function beforeValidation(): bool
    {
        $validator = new Validation();

        $validator->add(
            'nome',
            new PresenceOf([
                'message' => 'O nome é obrigatório'
            ])
        );

        $validator->add(
            'nome',
            new StringLength([
                'max' => 150,
                'messageMaximum' => 'O nome deve ter no máximo 150 caracteres',
                'includedMaximum' => true
            ])
        );

        $validator->add(
            'cpf',
            new PresenceOf([
                'message' => 'O CPF é obrigatório'
            ])
        );

        $validator->add(
            'cpf',
            new StringLength([
                'min' => 11,
                'max' => 20,
                'messageMinimum' => 'O CPF deve ter pelo menos 11 caracteres',
                'messageMaximum' => 'O CPF deve ter no máximo 20 caracteres',
                'includedMinimum' => true,
                'includedMaximum' => true,
                'allowEmpty' => true
            ])
        );

        if (!empty($this->email)) {
            $validator->add(
                'email',
                new EmailValidator([
                    'message' => 'O e-mail informado é inválido'
                ])
            );
        }

        $resultado = $validator->validate($this->toArray());

        if ($resultado->count() > 0) {
            foreach ($resultado as $mensagem) {
                $this->appendMessage($mensagem);
            }
            return false;
        }

        return true;
    }

    public function beforeSave(): void
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function beforeCreate(): void
    {
        $this->created_at = date('Y-m-d H:i:s');
    }
}
