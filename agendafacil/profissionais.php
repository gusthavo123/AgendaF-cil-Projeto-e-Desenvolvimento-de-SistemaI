<?php
// UC04: Gerenciar profissionais (CRUD)
require __DIR__ . '/app/bootstrap.php';
exigir_login();

$model = new Profissional();
$acao  = $_GET['acao'] ?? 'listar';

function validar_profissional(array $d): array
{
    $erros = [];

    if ($d['nome'] === '' || mb_strlen($d['nome']) > 100) {
        $erros['nome'] = 'Informe o nome (até 100 caracteres).';
    }

    if ($d['telefone'] !== '') {
        $digitos = strlen(preg_replace('/\D/', '', $d['telefone']));
        if ($digitos < 10 || $digitos > 13) {
            $erros['telefone'] = 'Telefone inválido (inclua o DDD).';
        }
    }

    if (mb_strlen($d['especialidade']) > 80) {
        $erros['especialidade'] = 'Use até 80 caracteres.';
    }

    return $erros;
}

switch ($acao) {

    case 'novo':
        renderizar('profissionais/form', [
            'titulo' => 'Novo profissional', 'profissional' => ['ativo' => 1], 'erros' => [],
        ]);
        break;

    case 'editar':
        $profissional = $model->buscarPorId((int) ($_GET['id'] ?? 0));
        if ($profissional === null) {
            flash('danger', 'Profissional não encontrado.');
            redirecionar('profissionais.php');
        }
        renderizar('profissionais/form', [
            'titulo' => 'Editar profissional', 'profissional' => $profissional, 'erros' => [],
        ]);
        break;

    case 'salvar':
        if (!eh_post()) {
            redirecionar('profissionais.php');
        }
        csrf_validar();

        $id    = (int) ($_POST['id'] ?? 0);
        $dados = [
            'nome'          => trim($_POST['nome'] ?? ''),
            'telefone'      => trim($_POST['telefone'] ?? ''),
            'especialidade' => trim($_POST['especialidade'] ?? ''),
            'ativo'         => isset($_POST['ativo']) ? 1 : 0,
        ];

        if ($id > 0 && $model->buscarPorId($id) === null) {
            flash('danger', 'Profissional não encontrado.');
            redirecionar('profissionais.php');
        }

        $erros = validar_profissional($dados);
        if ($erros) {
            renderizar('profissionais/form', [
                'titulo'       => $id ? 'Editar profissional' : 'Novo profissional',
                'profissional' => $dados + ['id' => $id],
                'erros'        => $erros,
            ]);
            break;
        }

        // campos opcionais vazios viram NULL no banco
        $dados['telefone']      = $dados['telefone'] === '' ? null : $dados['telefone'];
        $dados['especialidade'] = $dados['especialidade'] === '' ? null : $dados['especialidade'];

        if ($id > 0) {
            $model->atualizar($id, $dados);
            flash('success', 'Profissional atualizado com sucesso.');
        } else {
            $model->inserir($dados);
            flash('success', 'Profissional cadastrado com sucesso.');
        }
        redirecionar('profissionais.php');
        break;

    case 'excluir':
        if (!eh_post()) {
            redirecionar('profissionais.php');
        }
        csrf_validar();
        $id = (int) ($_POST['id'] ?? 0);

        // RF10
        if ($model->temAgendamentos($id)) {
            flash('danger', 'Não é possível excluir: o profissional possui agendamentos. Desmarque "ativo" para tirá-lo de uso.');
        } else {
            $model->excluir($id);
            flash('success', 'Profissional excluído.');
        }
        redirecionar('profissionais.php');
        break;

    default:
        renderizar('profissionais/lista', [
            'titulo' => 'Profissionais', 'profissionais' => $model->listarTodos(),
        ]);
}
