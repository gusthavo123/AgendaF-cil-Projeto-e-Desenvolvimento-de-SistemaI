<?php
// UC02: Gerenciar clientes (CRUD). Controller: recebe a requisição, valida e chama o Model.
require __DIR__ . '/app/bootstrap.php';
exigir_login();

$model = new Cliente();
$acao  = $_GET['acao'] ?? 'listar';

/** Valida os dados do formulário; devolve [campo => mensagem de erro]. */
function validar_cliente(array $d): array
{
    $erros = [];

    if ($d['nome'] === '' || mb_strlen($d['nome']) > 100) {
        $erros['nome'] = 'Informe o nome (até 100 caracteres).';
    }

    $digitos = strlen(preg_replace('/\D/', '', $d['telefone']));
    if ($d['telefone'] === '' || $digitos < 10 || $digitos > 13) {
        $erros['telefone'] = 'Informe um telefone válido com DDD.';
    }

    if ($d['email'] !== '' && (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 120)) {
        $erros['email'] = 'E-mail inválido.';
    }

    return $erros;
}

switch ($acao) {

    case 'novo':
        renderizar('clientes/form', ['titulo' => 'Novo cliente', 'cliente' => [], 'erros' => []]);
        break;

    case 'editar':
        $cliente = $model->buscarPorId((int) ($_GET['id'] ?? 0));
        if ($cliente === null) {
            flash('danger', 'Cliente não encontrado.');
            redirecionar('clientes.php');
        }
        renderizar('clientes/form', ['titulo' => 'Editar cliente', 'cliente' => $cliente, 'erros' => []]);
        break;

    case 'salvar':
        if (!eh_post()) {
            redirecionar('clientes.php');
        }
        csrf_validar();

        $id    = (int) ($_POST['id'] ?? 0);
        $dados = [
            'nome'     => trim($_POST['nome'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
        ];

        if ($id > 0 && $model->buscarPorId($id) === null) {
            flash('danger', 'Cliente não encontrado.');
            redirecionar('clientes.php');
        }

        $erros = validar_cliente($dados);
        if ($erros) {
            renderizar('clientes/form', [
                'titulo'  => $id ? 'Editar cliente' : 'Novo cliente',
                'cliente' => $dados + ['id' => $id],
                'erros'   => $erros,
            ]);
            break;
        }

        $dados['email'] = $dados['email'] === '' ? null : $dados['email'];

        if ($id > 0) {
            $model->atualizar($id, $dados);
            flash('success', 'Cliente atualizado com sucesso.');
        } else {
            $model->inserir($dados);
            flash('success', 'Cliente cadastrado com sucesso.');
        }
        redirecionar('clientes.php');
        break;

    case 'excluir':
        if (!eh_post()) {
            redirecionar('clientes.php');
        }
        csrf_validar();
        $id = (int) ($_POST['id'] ?? 0);

        // RF10: não exclui cliente que tem agendamentos
        if ($model->temAgendamentos($id)) {
            flash('danger', 'Não é possível excluir: o cliente possui agendamentos.');
        } else {
            $model->excluir($id);
            flash('success', 'Cliente excluído.');
        }
        redirecionar('clientes.php');
        break;

    default: // listar
        renderizar('clientes/lista', ['titulo' => 'Clientes', 'clientes' => $model->listarTodos()]);
}
