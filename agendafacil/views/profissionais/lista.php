<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Profissionais</h1>
    <a href="profissionais.php?acao=novo" class="btn btn-primary">Novo profissional</a>
</div>

<?php if (empty($profissionais)): ?>
    <div class="alert alert-light border">Nenhum profissional cadastrado.</div>
<?php else: ?>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Especialidade</th>
                    <th>Telefone</th>
                    <th>Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($profissionais as $p): ?>
                <tr>
                    <td><?= e($p['nome']) ?></td>
                    <td><?= e($p['especialidade']) ?></td>
                    <td><?= e($p['telefone']) ?></td>
                    <td>
                        <?php if ($p['ativo']): ?>
                            <span class="badge text-bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="profissionais.php?acao=editar&id=<?= (int) $p['id'] ?>">Editar</a>
                        <form method="post" action="profissionais.php?acao=excluir" class="d-inline"
                              onsubmit="return confirm('Excluir este profissional?');">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
