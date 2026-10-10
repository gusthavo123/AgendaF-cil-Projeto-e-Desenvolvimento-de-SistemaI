<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 m-0">Agenda do dia</h1>
    <a href="agendamentos.php?acao=novo" class="btn btn-primary">Novo agendamento</a>
</div>

<form method="get" action="index.php" class="row g-2 align-items-end mb-3">
    <div class="col-auto">
        <label for="data" class="form-label mb-1">Data</label>
        <input type="date" class="form-control" id="data" name="data" value="<?= e($data) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Ver</button>
        <a href="index.php" class="btn btn-link">Hoje</a>
    </div>
</form>

<p class="text-muted"><?= e(date('d/m/Y', strtotime($data))) ?> · <?= count($agendamentos) ?> atendimento(s)</p>

<?php require __DIR__ . '/agendamentos/_tabela.php'; ?>
