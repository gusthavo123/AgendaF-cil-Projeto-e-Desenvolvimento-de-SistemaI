<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Serviços</h1>
    <a href="servicos.php?acao=novo" class="btn btn-primary">Novo serviço</a>
</div>

<?php if (empty($servicos)): ?>
    <div class="alert alert-light border">Nenhum serviço cadastrado.</div>
<?php else: ?>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Duração</th>
                    <th>Preço</th>
                    <th>Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($servicos as $s): ?>
                <tr>
                    <td><?= e($s['nome']) ?></td>
                    <td><?= e($s['descricao']) ?></td>
                    <td><?= (int) $s['duracao_min'] ?> min</td>
                    <td><?= e(fmt_moeda($s['preco'])) ?></td>
                    <td>
                        <?php if ($s['ativo']): ?>
                            <span class="badge text-bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="servicos.php?acao=editar&id=<?= (int) $s['id'] ?>">Editar</a>
                        <form method="post" action="servicos.php?acao=excluir" class="d-inline"
                              onsubmit="return confirm('Excluir este serviço?');">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
