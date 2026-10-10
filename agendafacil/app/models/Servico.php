<?php
require_once __DIR__ . '/BaseModel.php';

class Servico extends BaseModel
{
    protected string $tabela   = 'servicos';
    protected array  $campos   = ['nome', 'descricao', 'duracao_min', 'preco', 'ativo'];
    protected string $colunaFk = 'servico_id';

    /** Serviços ativos para o select do agendamento (mantém o atual ao editar). */
    public function listarAtivos(?int $incluirId = null): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM servicos WHERE ativo = 1 OR id = :id ORDER BY nome'
        );
        $stmt->execute([':id' => $incluirId ?? 0]);
        return $stmt->fetchAll();
    }
}
