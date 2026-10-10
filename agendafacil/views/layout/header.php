<?php
// Layout: topo da página (menu + mensagens). Recebe $titulo do controller.
$logado = usuario_logado();
$pagina = basename($_SERVER['SCRIPT_NAME']);

function item_menu(string $arquivo, string $texto, string $pagina): string
{
    $ativo = $pagina === $arquivo ? ' active' : '';
    return '<li class="nav-item"><a class="nav-link' . $ativo . '" href="' . $arquivo . '">' . $texto . '</a></li>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'AgendaFácil') ?> · AgendaFácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgendaFácil</a>

        <?php if ($logado): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                    aria-controls="menu" aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto">
                    <?= item_menu('index.php', 'Agenda do dia', $pagina) ?>
                    <?= item_menu('agendamentos.php', 'Agendamentos', $pagina) ?>
                    <?= item_menu('clientes.php', 'Clientes', $pagina) ?>
                    <?= item_menu('servicos.php', 'Serviços', $pagina) ?>
                    <?= item_menu('profissionais.php', 'Profissionais', $pagina) ?>
                </ul>

                <form action="logout.php" method="post" class="d-flex align-items-center gap-2 m-0">
                    <span class="navbar-text"><?= e($logado['nome']) ?></span>
                    <?= csrf_campo() ?>
                    <button class="btn btn-outline-light btn-sm" type="submit">Sair</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</nav>

<main class="container py-4">
    <?php foreach (flash_obter() as $f): ?>
        <div class="alert alert-<?= e($f['tipo']) ?> alert-dismissible fade show" role="alert">
            <?= e($f['mensagem']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endforeach; ?>
