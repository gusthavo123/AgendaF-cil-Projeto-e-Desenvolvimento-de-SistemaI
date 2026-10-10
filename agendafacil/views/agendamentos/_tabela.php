<?php
// Tabela de agendamentos usada na agenda do dia e na listagem.
// Variáveis esperadas: $agendamentos, $voltar (para onde retornar após a ação), $mostrarData (bool)
?>
<?php if (empty($agendamentos)): ?>
    <div class="alert alert-light border">Nenhum agendamento encontrado.</div>
<?php else: ?>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th><?= $mostrarData ? 'Data e hora' : 'Horário' ?></th>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Profissional</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($agendamentos as $a): ?>
                <tr>
                    <td class="text-nowrap">
                        <?= $mostrarData ? e(fmt_data_hora($a['data_hora_inicio'])) : e(fmt_hora($a['data_hora_inicio'])) ?>
                    </td>
                    <td><?= e($a['cliente_nome']) ?></td>
                    <td>
                        <?= e($a['servico_nome']) ?>
                        <div class="text-muted small"><?= (int) $a['duracao_min'] ?> min · <?= e(fmt_moeda($a['preco'])) ?></div>
                    </td>
                    <td><?= e($a['profissional_nome']) ?></td>
                    <td><span class="badge text-bg-<?= e(cor_status($a['status'])) ?>"><?= e(rotulo_status($a['status'])) ?></span></td>
                    <td class="text-end text-nowrap">
                        <?php if ($a['status'] === 'agendado'): ?>
                            <form method="post" action="agendamentos.php?acao=status" class="d-inline">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                <input type="hidden" name="status" value="concluido">
                                <input type="hidden" name="voltar" value="<?= e($voltar) ?>">
                                <button class="btn btn-sm btn-success" type="submit">Concluir</button>
                            </form>

                            <a class="btn btn-sm btn-outline-primary" href="agendamentos.php?acao=editar&id=<?= (int) $a['id'] ?>">Remarcar</a>

                            <form method="post" action="agendamentos.php?acao=status" class="d-inline"
                                  onsubmit="return confirm('Cancelar este agendamento?');">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                <input type="hidden" name="status" value="cancelado">
                                <input type="hidden" name="voltar" value="<?= e($voltar) ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit">Cancelar</button>
                            </form>
                        <?php endif; ?>

                        <form method="post" action="agendamentos.php?acao=excluir" class="d-inline"
                              onsubmit="return confirm('Excluir este agendamento definitivamente?');">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                            <input type="hidden" name="voltar" value="<?= e($voltar) ?>">
                            <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
