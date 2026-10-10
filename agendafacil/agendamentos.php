<?php
// UC05: Gerenciar agendamentos (criar, listar, remarcar, concluir, cancelar, excluir)
require __DIR__ . '/app/bootstrap.php';
exigir_login();

$model         = new Agendamento();
$clienteModel  = new Cliente();
$profModel     = new Profissional();
$servicoModel  = new Servico();
$acao          = $_GET['acao'] ?? 'listar';

/** Dados que o formulário precisa para montar os selects. */
function dados_do_form(Cliente $c, Profissional $p, Servico $s, array $ag = []): array
{
    return [
        'clientes'      => $c->listarTodos(),
        'profissionais' => $p->listarAtivos($ag['profissional_id'] ?? null),
        'servicos'      => $s->listarAtivos($ag['servico_id'] ?? null),
    ];
}

/** Só aceita voltar para páginas conhecidas (evita redirecionamento aberto). */
function destino_seguro(string $url): string
{
    if ($url === 'agendamentos.php' || preg_match('/^index\.php(\?data=\d{4}-\d{2}-\d{2})?$/', $url)) {
        return $url;
    }
    return 'agendamentos.php';
}

switch ($acao) {

    case 'novo':
        renderizar('agendamentos/form', [
            'titulo'      => 'Novo agendamento',
            'agendamento' => ['data_hora_inicio' => date('Y-m-d\T09:00')],
            'erros'       => [],
        ] + dados_do_form($clienteModel, $profModel, $servicoModel));
        break;

    case 'editar':
        $ag = $model->buscarPorId((int) ($_GET['id'] ?? 0));
        if ($ag === null) {
            flash('danger', 'Agendamento não encontrado.');
            redirecionar('agendamentos.php');
        }
        if ($ag['status'] !== 'agendado') {
            flash('warning', 'Só é possível remarcar agendamentos com status "Agendado".');
            redirecionar('agendamentos.php');
        }
        $ag['data_hora_inicio'] = date('Y-m-d\TH:i', strtotime($ag['data_hora_inicio']));

        renderizar('agendamentos/form', [
            'titulo'      => 'Remarcar agendamento',
            'agendamento' => $ag,
            'erros'       => [],
        ] + dados_do_form($clienteModel, $profModel, $servicoModel, $ag));
        break;

    case 'salvar':
        if (!eh_post()) {
            redirecionar('agendamentos.php');
        }
        csrf_validar();

        $id    = (int) ($_POST['id'] ?? 0);
        $dados = [
            'cliente_id'       => (int) ($_POST['cliente_id'] ?? 0),
            'profissional_id'  => (int) ($_POST['profissional_id'] ?? 0),
            'servico_id'       => (int) ($_POST['servico_id'] ?? 0),
            'data_hora_inicio' => trim($_POST['data_hora_inicio'] ?? ''),
            'observacoes'      => trim($_POST['observacoes'] ?? ''),
        ];

        if ($id > 0) {
            $existente = $model->buscarPorId($id);
            if ($existente === null || $existente['status'] !== 'agendado') {
                flash('danger', 'Agendamento não encontrado ou não pode mais ser alterado.');
                redirecionar('agendamentos.php');
            }
        }

        // ---- validação (RF05, RF09) ----
        $erros = [];

        if ($clienteModel->buscarPorId($dados['cliente_id']) === null) {
            $erros['cliente_id'] = 'Escolha um cliente.';
        }
        if ($profModel->buscarPorId($dados['profissional_id']) === null) {
            $erros['profissional_id'] = 'Escolha um profissional.';
        }
        $servico = $servicoModel->buscarPorId($dados['servico_id']);
        if ($servico === null) {
            $erros['servico_id'] = 'Escolha um serviço.';
        }
        if (mb_strlen($dados['observacoes']) > 255) {
            $erros['observacoes'] = 'Use até 255 caracteres.';
        }

        $dt = DateTime::createFromFormat('Y-m-d\TH:i', $dados['data_hora_inicio']);
        if ($dt === false || $dt->format('Y-m-d\TH:i') !== $dados['data_hora_inicio']) {
            $erros['data_hora_inicio'] = 'Informe uma data e hora válidas.';
        }

        $inicio = $dt !== false ? $dt->format('Y-m-d H:i:s') : '';

        if (!$erros && $model->haConflito($dados['profissional_id'], $inicio, (int) $servico['duracao_min'], $id ?: null)) {
            $erros['data_hora_inicio'] = 'Esse profissional já tem um atendimento nesse horário. Escolha outro horário.';
        }

        if ($erros) {
            renderizar('agendamentos/form', [
                'titulo'      => $id ? 'Remarcar agendamento' : 'Novo agendamento',
                'agendamento' => $dados + ['id' => $id],
                'erros'       => $erros,
            ] + dados_do_form($clienteModel, $profModel, $servicoModel, $dados));
            break;
        }

        $dados['data_hora_inicio'] = $inicio;
        $dados['observacoes']      = $dados['observacoes'] === '' ? null : $dados['observacoes'];

        if ($id > 0) {
            $model->atualizar($id, $dados);
            flash('success', 'Agendamento remarcado com sucesso.');
        } else {
            $dados['usuario_id'] = usuario_logado()['id'];
            $model->inserir($dados);
            flash('success', 'Agendamento criado com sucesso.');
        }
        redirecionar('agendamentos.php');
        break;

    case 'status': // RF08: concluir ou cancelar
        if (!eh_post()) {
            redirecionar('agendamentos.php');
        }
        csrf_validar();

        $id     = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $volta  = destino_seguro($_POST['voltar'] ?? '');

        if (!in_array($status, ['concluido', 'cancelado'], true) || $model->buscarPorId($id) === null) {
            flash('danger', 'Operação inválida.');
        } else {
            $model->atualizarStatus($id, $status);
            flash('success', 'Status atualizado para "' . rotulo_status($status) . '".');
        }
        redirecionar($volta);
        break;

    case 'excluir':
        if (!eh_post()) {
            redirecionar('agendamentos.php');
        }
        csrf_validar();

        $id    = (int) ($_POST['id'] ?? 0);
        $volta = destino_seguro($_POST['voltar'] ?? '');

        if ($model->buscarPorId($id) === null) {
            flash('danger', 'Agendamento não encontrado.');
        } else {
            $model->excluir($id);
            flash('success', 'Agendamento excluído.');
        }
        redirecionar($volta);
        break;

    default: // listar com filtros (RF06)
        $filtros = [
            'data'            => data_valida($_GET['data'] ?? '') ? $_GET['data'] : '',
            'cliente_id'      => (int) ($_GET['cliente_id'] ?? 0),
            'profissional_id' => (int) ($_GET['profissional_id'] ?? 0),
            'status'          => $_GET['status'] ?? '',
        ];

        renderizar('agendamentos/lista', [
            'titulo'        => 'Agendamentos',
            'agendamentos'  => $model->listar($filtros),
            'filtros'       => $filtros,
            'clientes'      => $clienteModel->listarTodos(),
            'profissionais' => $profModel->listarTodos(),
            'voltar'        => 'agendamentos.php',
            'mostrarData'   => true,
        ]);
}
