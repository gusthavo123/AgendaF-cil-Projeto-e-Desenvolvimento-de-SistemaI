<?php
// RF11: página inicial com a agenda do dia
require __DIR__ . '/app/bootstrap.php';
exigir_login();

$data = $_GET['data'] ?? date('Y-m-d');
if (!data_valida($data)) {
    $data = date('Y-m-d');
}

$agendamentos = (new Agendamento())->agendaDoDia($data);

renderizar('home', [
    'titulo'       => 'Agenda do dia',
    'data'         => $data,
    'agendamentos' => $agendamentos,
    'voltar'       => 'index.php?data=' . $data,
    'mostrarData'  => false,
]);
