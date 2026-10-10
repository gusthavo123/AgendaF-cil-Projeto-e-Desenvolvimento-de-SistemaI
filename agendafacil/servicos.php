<?php
// UC03: Gerenciar serviços (CRUD)
require __DIR__ . '/app/bootstrap.php';
exigir_login();

$model = new Servico();
$acao  = $_GET['acao'] ?? 'listar';

function validar_servico(array $d): array
{
    $erros = [];

    if ($d['nome'] === '' || mb_strlen($d['nome']) > 100) {
        $erros['nome'] = 'Informe o nome (até 100 caracteres).';
    }

    if (mb_strlen($d['descricao']) > 255) {
        $erros['descricao'] = 'Use até 255 caracteres.';
    }

    $duracao = filter_var($d['duracao_min'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 5, 'max_range' => 480]]);
    if ($duracao === false) {
        $erros['duracao_min'] = 'Informe a duração em minutos (de 5 a 480).';
    }

    $preco = para_decimal((string) $d['preco']);
    if ($preco === null || $preco < 0 || $preco > 99999999.99) {
        $erros['preco'] = 'Informe um preço válido (ex.: 40,00).';
    }

    return $erros;
}

switch ($acao) {

    case 'novo':
        renderizar('servicos/form', [
            'titulo' => 'Novo serviço', 'servico' => ['ativo' => 1], 'erros' => [],
        ]);
        break;

    case 'editar':
        $servico = $model->buscarPorId((int) ($_GET['id'] ?? 0));
        if ($servico === null) {
            flash('danger', 'Serviço não encontrado.');
            redirecionar('servicos.php');
        }
        renderizar('servicos/form', ['titulo' => 'Editar serviço', 'servico' => $servico, 'erros' => []]);
        break;

    case 'salvar':
        if (!eh_post()) {
            redirecionar('servicos.php');
        }
        csrf_validar();

        $id    = (int) ($_POST['id'] ?? 0);
        $dados = [
            'nome'        => trim($_POST['nome'] ?? ''),
            'descricao'   => trim($_POST['descricao'] ?? ''),
            'duracao_min' => trim($_POST['duracao_min'] ?? ''),
            'preco'       => trim($_POST['preco'] ?? ''),
            'ativo'       => isset($_POST['ativo']) ? 1 : 0,
        ];

        if ($id > 0 && $model->buscarPorId($id) === null) {
            flash('danger', 'Serviço não encontrado.');
            redirecionar('servicos.php');
        }

        $erros = validar_servico($dados);
        if ($erros) {
            renderizar('servicos/form', [
                'titulo'  => $id ? 'Editar serviço' : 'Novo serviço',
                'servico' => $dados + ['id' => $id],
                'erros'   => $erros,
            ]);
            break;
        }

        // normaliza os tipos antes de gravar
        $dados['duracao_min'] = (int) $dados['duracao_min'];
        $dados['preco']       = para_decimal($dados['preco']);
        $dados['descricao']   = $dados['descricao'] === '' ? null : $dados['descricao'];

        if ($id > 0) {
            $model->atualizar($id, $dados);
            flash('success', 'Serviço atualizado com sucesso.');
        } else {
            $model->inserir($dados);
            flash('success', 'Serviço cadastrado com sucesso.');
        }
        redirecionar('servicos.php');
        break;

    case 'excluir':
        if (!eh_post()) {
            redirecionar('servicos.php');
        }
        csrf_validar();
        $id = (int) ($_POST['id'] ?? 0);

        // RF10
        if ($model->temAgendamentos($id)) {
            flash('danger', 'Não é possível excluir: o serviço possui agendamentos. Desmarque "ativo" para tirá-lo de uso.');
        } else {
            $model->excluir($id);
            flash('success', 'Serviço excluído.');
        }
        redirecionar('servicos.php');
        break;

    default:
        renderizar('servicos/lista', ['titulo' => 'Serviços', 'servicos' => $model->listarTodos()]);
}
