<?php
require_once __DIR__ . '/BaseModel.php';

class Profissional extends BaseModel
{
    protected string $tabela   = 'profissionais';
    protected array  $campos   = ['nome', 'telefone', 'especialidade', 'ativo'];
    protected string $colunaFk = 'profissional_id';

    /**
     * Profissionais ativos para o select do agendamento.
     * $incluirId mantém o profissional atual ao editar um agendamento antigo.
     */
    public function listarAtivos(?int $incluirId = null): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM profissionais WHERE ativo = 1 OR id = :id ORDER BY nome'
        );
        $stmt->execute([':id' => $incluirId ?? 0]);
        return $stmt->fetchAll();
    }
}
