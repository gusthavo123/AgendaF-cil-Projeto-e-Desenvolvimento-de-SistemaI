<?php
$a = $agendamento;
$classe = fn(string $campo) => isset($erros[$campo]) ? ' is-invalid' : '';
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<form method="post" action="agendamentos.php?acao=salvar" class="bg-white border rounded p-4" novalidate>
    <?= csrf_campo() ?>
    <input type="hidden" name="id" value="<?= (int) ($a['id'] ?? 0) ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label for="cliente_id" class="form-label">Cliente</label>
            <select class="form-select<?= $classe('cliente_id') ?>" id="cliente_id" name="cliente_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int) ($a['cliente_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($erros['cliente_id'])): ?><div class="invalid-feedback"><?= e($erros['cliente_id']) ?></div><?php endif; ?>
            <div class="form-text">Cliente novo? <a href="clientes.php?acao=novo">Cadastre aqui</a>.</div>
        </div>

        <div class="col-md-6">
            <label for="profissional_id" class="form-label">Profissional</label>
            <select class="form-select<?= $classe('profissional_id') ?>" id="profissional_id" name="profissional_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($profissionais as $p): ?>
                    <option value="<?= (int) $p['id'] ?>" <?= (int) ($a['profissional_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                        <?= e($p['nome']) ?><?= $p['especialidade'] ? ' (' . e($p['especialidade']) . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($erros['profissional_id'])): ?><div class="invalid-feedback"><?= e($erros['profissional_id']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="servico_id" class="form-label">Serviço</label>
            <select class="form-select<?= $classe('servico_id') ?>" id="servico_id" name="servico_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= (int) $s['id'] ?>" <?= (int) ($a['servico_id'] ?? 0) === (int) $s['id'] ? 'selected' : '' ?>>
                        <?= e($s['nome']) ?> · <?= (int) $s['duracao_min'] ?> min · <?= e(fmt_moeda($s['preco'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($erros['servico_id'])): ?><div class="invalid-feedback"><?= e($erros['servico_id']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="data_hora_inicio" class="form-label">Data e hora de início</label>
            <input type="datetime-local" class="form-control<?= $classe('data_hora_inicio') ?>" id="data_hora_inicio"
                   name="data_hora_inicio" value="<?= e($a['data_hora_inicio'] ?? '') ?>" required>
            <?php if (isset($erros['data_hora_inicio'])): ?><div class="invalid-feedback"><?= e($erros['data_hora_inicio']) ?></div><?php endif; ?>
        </div>

        <div class="col-12">
            <label for="observacoes" class="form-label">Observações</label>
            <input type="text" class="form-control<?= $classe('observacoes') ?>" id="observacoes" name="observacoes"
                   maxlength="255" value="<?= e($a['observacoes'] ?? '') ?>">
            <?php if (isset($erros['observacoes'])): ?><div class="invalid-feedback"><?= e($erros['observacoes']) ?></div><?php endif; ?>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="agendamentos.php" class="btn btn-outline-secondary">Voltar</a>
    </div>
</form>
