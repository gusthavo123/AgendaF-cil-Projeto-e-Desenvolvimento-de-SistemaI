<?php
$classe = fn(string $campo) => isset($erros[$campo]) ? ' is-invalid' : '';
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<form method="post" action="servicos.php?acao=salvar" class="bg-white border rounded p-4" novalidate>
    <?= csrf_campo() ?>
    <input type="hidden" name="id" value="<?= (int) ($servico['id'] ?? 0) ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control<?= $classe('nome') ?>" id="nome" name="nome" maxlength="100"
                   value="<?= e($servico['nome'] ?? '') ?>" required>
            <?php if (isset($erros['nome'])): ?><div class="invalid-feedback"><?= e($erros['nome']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="descricao" class="form-label">Descrição (opcional)</label>
            <input type="text" class="form-control<?= $classe('descricao') ?>" id="descricao" name="descricao" maxlength="255"
                   value="<?= e($servico['descricao'] ?? '') ?>">
            <?php if (isset($erros['descricao'])): ?><div class="invalid-feedback"><?= e($erros['descricao']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-3">
            <label for="duracao_min" class="form-label">Duração (minutos)</label>
            <input type="number" class="form-control<?= $classe('duracao_min') ?>" id="duracao_min" name="duracao_min"
                   min="5" max="480" step="5" value="<?= e((string) ($servico['duracao_min'] ?? '')) ?>" required>
            <?php if (isset($erros['duracao_min'])): ?><div class="invalid-feedback"><?= e($erros['duracao_min']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-3">
            <label for="preco" class="form-label">Preço (R$)</label>
            <input type="text" class="form-control<?= $classe('preco') ?>" id="preco" name="preco" inputmode="decimal"
                   placeholder="40,00" value="<?= e(isset($servico['preco']) ? number_format((float) str_replace(',', '.', (string) $servico['preco']), 2, ',', '') : '') ?>" required>
            <?php if (isset($erros['preco'])): ?><div class="invalid-feedback"><?= e($erros['preco']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                       <?= !empty($servico['ativo']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="ativo">Ativo (aparece na hora de agendar)</label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="servicos.php" class="btn btn-outline-secondary">Voltar</a>
    </div>
</form>
