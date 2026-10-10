<?php
$classe = fn(string $campo) => isset($erros[$campo]) ? ' is-invalid' : '';
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<form method="post" action="profissionais.php?acao=salvar" class="bg-white border rounded p-4" novalidate>
    <?= csrf_campo() ?>
    <input type="hidden" name="id" value="<?= (int) ($profissional['id'] ?? 0) ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control<?= $classe('nome') ?>" id="nome" name="nome" maxlength="100"
                   value="<?= e($profissional['nome'] ?? '') ?>" required>
            <?php if (isset($erros['nome'])): ?><div class="invalid-feedback"><?= e($erros['nome']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="especialidade" class="form-label">Especialidade (opcional)</label>
            <input type="text" class="form-control<?= $classe('especialidade') ?>" id="especialidade" name="especialidade"
                   maxlength="80" value="<?= e($profissional['especialidade'] ?? '') ?>">
            <?php if (isset($erros['especialidade'])): ?><div class="invalid-feedback"><?= e($erros['especialidade']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="telefone" class="form-label">Telefone (opcional)</label>
            <input type="text" class="form-control<?= $classe('telefone') ?>" id="telefone" name="telefone" maxlength="20"
                   placeholder="(43) 98888-0000" value="<?= e($profissional['telefone'] ?? '') ?>">
            <?php if (isset($erros['telefone'])): ?><div class="invalid-feedback"><?= e($erros['telefone']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                       <?= !empty($profissional['ativo']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="ativo">Ativo (aparece na hora de agendar)</label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="profissionais.php" class="btn btn-outline-secondary">Voltar</a>
    </div>
</form>
