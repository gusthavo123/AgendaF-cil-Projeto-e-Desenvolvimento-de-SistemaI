<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 m-0">Agendamentos</h1>
    <a href="agendamentos.php?acao=novo" class="btn btn-primary">Novo agendamento</a>
</div>

<form method="get" action="agendamentos.php" class="row g-2 align-items-end mb-3">
    <div class="col-sm-auto">
        <label for="data" class="form-label mb-1">Data</label>
        <input type="date" class="form-control" id="data" name="data" value="<?= e($filtros['data']) ?>">
    </div>

    <div class="col-sm-auto">
        <label for="cliente_id" class="form-label mb-1">Cliente</label>
        <select class="form-select" id="cliente_id" name="cliente_id">
            <option value="">Todos</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= (int) $filtros['cliente_id'] === (int) $c['id'] ? 'selected' : '' ?>>
                    <?= e($c['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-sm-auto">
        <label for="profissional_id" class="form-label mb-1">Profissional</label>
        <select class="form-select" id="profissional_id" name="profissional_id">
            <option value="">Todos</option>
            <?php foreach ($profissionais as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= (int) $filtros['profissional_id'] === (int) $p['id'] ? 'selected' : '' ?>>
                    <?= e($p['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-sm-auto">
        <label for="status" class="form-label mb-1">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="">Todos</option>
            <?php foreach (Agendamento::STATUS as $s): ?>
                <option value="<?= e($s) ?>" <?= $filtros['status'] === $s ? 'selected' : '' ?>><?= e(rotulo_status($s)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-sm-auto">
        <button type="submit" class="btn btn-outline-secondary">Filtrar</button>
        <a href="agendamentos.php" class="btn btn-link">Limpar</a>
    </div>
</form>

<?php require __DIR__ . '/_tabela.php'; ?>
