<?php
$classe = fn(string $campo) => isset($erros[$campo]) ? ' is-invalid' : '';
?>
<h1 class="h3 mb-3"><?= e($titulo) ?></h1>

<form method="post" action="clientes.php?acao=salvar" class="bg-white border rounded p-4" novalidate>
    <?= csrf_campo() ?>
    <input type="hidden" name="id" value="<?= (int) ($cliente['id'] ?? 0) ?>">

    <div class="row g-3">
        <div class="col-md-6">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control<?= $classe('nome') ?>" id="nome" name="nome" maxlength="100"
                   value="<?= e($cliente['nome'] ?? '') ?>" required>
            <?php if (isset($erros['nome'])): ?><div class="invalid-feedback"><?= e($erros['nome']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" class="form-control<?= $classe('telefone') ?>" id="telefone" name="telefone" maxlength="20"
                   placeholder="(43) 99999-0000" value="<?= e($cliente['telefone'] ?? '') ?>" required>
            <?php if (isset($erros['telefone'])): ?><div class="invalid-feedback"><?= e($erros['telefone']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">E-mail (opcional)</label>
            <input type="email" class="form-control<?= $classe('email') ?>" id="email" name="email" maxlength="120"
                   value="<?= e($cliente['email'] ?? '') ?>">
            <?php if (isset($erros['email'])): ?><div class="invalid-feedback"><?= e($erros['email']) ?></div><?php endif; ?>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="clientes.php" class="btn btn-outline-secondary">Voltar</a>
    </div>
</form>
