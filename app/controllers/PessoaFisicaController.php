<?php
declare(strict_types=1);

use Phalcon\Http\ResponseInterface;

class PessoaFisicaController extends ControllerBase
{
    public function indexAction(): void
    {
        $this->view->pessoas = PessoaFisica::find([
            'order' => 'nome ASC'
        ]);
    }

    public function novoAction(): void
    {
        // Apenas renderiza o formulário
    }

    public function cadastrarAction(): ResponseInterface
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('pessoa-fisica');
        }

        $cpf = $this->request->getPost('cpf', 'string');

        if ($this->existeCpfCadastrado($cpf)) {
            $this->flash->error('Erro ao cadastrar: Já existe uma pessoa cadastrada com este CPF');
            $this->dispatcher->forward([
                'controller' => 'pessoa-fisica',
                'action' => 'novo'
            ]);

            return $this->response;
        }

        $pessoa = new PessoaFisica();
        $pessoa->nome = $this->request->getPost('nome', 'string');
        $pessoa->cpf = $cpf;
        $pessoa->data_nascimento = $this->request->getPost('data_nascimento', 'string') ?: null;
        $pessoa->email = $this->request->getPost('email', 'string') ?: null;
        $pessoa->telefone = $this->request->getPost('telefone', 'string') ?: null;

        if (!$pessoa->save()) {
            $mensagens = $pessoa->getMessages();
            $erros = [];
            foreach ($mensagens as $mensagem) {
                $erros[] = $mensagem->getMessage();
            }

            $this->flash->error('Erro ao cadastrar: ' . implode('<br>', $erros));
            $this->dispatcher->forward([
                'controller' => 'pessoa-fisica',
                'action' => 'novo'
            ]);

            return $this->response;
        }

        $this->flash->success('Pessoa física cadastrada com sucesso!');
        return $this->response->redirect('pessoa-fisica');
    }

    public function editarAction(int $id): void
    {
        $pessoa = PessoaFisica::findFirstById($id);

        if (!$pessoa) {
            $this->flash->error('Pessoa física não encontrada.');
            $this->response->redirect('pessoa-fisica')->send();
            return;
        }

        $this->view->pessoa = $pessoa;
    }

    public function atualizarAction(): ResponseInterface
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('pessoa-fisica');
        }

        $id = (int) $this->request->getPost('id', 'int');
        $pessoa = PessoaFisica::findFirstById($id);

        if (!$pessoa) {
            $this->flash->error('Pessoa física não encontrada.');
            return $this->response->redirect('pessoa-fisica');
        }

        $cpf = $this->request->getPost('cpf', 'string');

        if ($this->existeCpfCadastrado($cpf, $id)) {
            $this->flash->error('Erro ao atualizar: Já existe uma pessoa cadastrada com este CPF');
            $this->dispatcher->forward([
                'controller' => 'pessoa-fisica',
                'action' => 'editar',
                'params' => [$id]
            ]);

            return $this->response;
        }

        $pessoa->nome = $this->request->getPost('nome', 'string');
        $pessoa->cpf = $cpf;
        $pessoa->data_nascimento = $this->request->getPost('data_nascimento', 'string') ?: null;
        $pessoa->email = $this->request->getPost('email', 'string') ?: null;
        $pessoa->telefone = $this->request->getPost('telefone', 'string') ?: null;

        if (!$pessoa->save()) {
            $mensagens = $pessoa->getMessages();
            $erros = [];
            foreach ($mensagens as $mensagem) {
                $erros[] = $mensagem->getMessage();
            }

            $this->flash->error('Erro ao atualizar: ' . implode('<br>', $erros));
            $this->dispatcher->forward([
                'controller' => 'pessoa-fisica',
                'action' => 'editar',
                'params' => [$id]
            ]);

            return $this->response;
        }

        $this->flash->success('Pessoa física atualizada com sucesso!');
        return $this->response->redirect('pessoa-fisica');
    }

    public function excluirAction(int $id): ResponseInterface
    {
        $pessoa = PessoaFisica::findFirstById($id);

        if (!$pessoa) {
            $this->flash->error('Pessoa física não encontrada.');
            return $this->response->redirect('pessoa-fisica');
        }

        if (!$pessoa->delete()) {
            $this->flash->error('Erro ao excluir a pessoa física.');
            return $this->response->redirect('pessoa-fisica');
        }

        $this->flash->success('Pessoa física excluída com sucesso!');
        return $this->response->redirect('pessoa-fisica');
    }

    private function existeCpfCadastrado(string $cpf, ?int $ignorarId = null): bool
    {
        $condicoes = [
            'cpf = :cpf:',
            'bind' => ['cpf' => $cpf]
        ];

        if ($ignorarId !== null) {
            $condicoes[0] .= ' AND id != :id:';
            $condicoes['bind']['id'] = $ignorarId;
        }

        return PessoaFisica::findFirst($condicoes) !== null;
    }
}
