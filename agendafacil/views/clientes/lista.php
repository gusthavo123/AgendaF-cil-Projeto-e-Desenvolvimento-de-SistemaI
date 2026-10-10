<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Clientes</h1>
    <a href="clientes.php?acao=novo" class="btn btn-primary">Novo cliente</a>
</div>

<?php if (empty($clientes)): ?>
    <div class="alert alert-light border">Nenhum cliente cadastrado.</div>
<?php else: ?>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= e($c['nome']) ?></td>
                    <td><?= e($c['telefone']) ?></td>
                    <td><?= e($c['email']) ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="agendamentos.php?cliente_id=<?= (int) $c['id'] ?>">Agendamentos</a>
                        <a class="btn btn-sm btn-outline-primary" href="clientes.php?acao=editar&id=<?= (int) $c['id'] ?>">Editar</a>
                        <form method="post" action="clientes.php?acao=excluir" class="d-inline"
                              onsubmit="return confirm('Excluir este cliente?');">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
