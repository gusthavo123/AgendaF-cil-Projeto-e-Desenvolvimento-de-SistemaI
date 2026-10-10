<?php
require_once __DIR__ . '/../Database.php';

class Agendamento
{
    public const STATUS = ['agendado', 'concluido', 'cancelado'];

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    // Consulta base com os nomes do cliente, profissional e serviço.
    private const SELECT_BASE = "
        SELECT a.*,
               c.nome AS cliente_nome,
               p.nome AS profissional_nome,
               s.nome AS servico_nome,
               s.duracao_min,
               s.preco
        FROM agendamentos a
        JOIN clientes      c ON c.id = a.cliente_id
        JOIN profissionais p ON p.id = a.profissional_id
        JOIN servicos      s ON s.id = a.servico_id
    ";

    /**
     * READ (RF06): lista com filtros opcionais: data (Y-m-d), cliente, profissional e status.
     */
    public function listar(array $filtros = []): array
    {
        $onde = [];
        $params = [];

        if (!empty($filtros['data'])) {
            $inicio = $filtros['data'] . ' 00:00:00';
            $fim    = date('Y-m-d', strtotime($filtros['data'] . ' +1 day')) . ' 00:00:00';
            $onde[] = 'a.data_hora_inicio >= :data_ini AND a.data_hora_inicio < :data_fim';
            $params[':data_ini'] = $inicio;
            $params[':data_fim'] = $fim;
        }
        if (!empty($filtros['cliente_id'])) {
            $onde[] = 'a.cliente_id = :cliente_id';
            $params[':cliente_id'] = (int) $filtros['cliente_id'];
        }
        if (!empty($filtros['profissional_id'])) {
            $onde[] = 'a.profissional_id = :profissional_id';
            $params[':profissional_id'] = (int) $filtros['profissional_id'];
        }
        if (!empty($filtros['status']) && in_array($filtros['status'], self::STATUS, true)) {
            $onde[] = 'a.status = :status';
            $params[':status'] = $filtros['status'];
        }

        $sql = self::SELECT_BASE;
        if ($onde) {
            $sql .= ' WHERE ' . implode(' AND ', $onde);
        }
        $sql .= ' ORDER BY a.data_hora_inicio DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** RF11: agenda de um dia, em ordem de horário. */
    public function agendaDoDia(string $data): array
    {
        $stmt = $this->db->prepare(
            self::SELECT_BASE .
            ' WHERE a.data_hora_inicio >= :ini AND a.data_hora_inicio < :fim
              ORDER BY a.data_hora_inicio ASC'
        );
        $stmt->execute([
            ':ini' => $data . ' 00:00:00',
            ':fim' => date('Y-m-d', strtotime($data . ' +1 day')) . ' 00:00:00',
        ]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare(self::SELECT_BASE . ' WHERE a.id = :id');
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();
        return $linha ?: null;
    }

    /** CREATE (RF05): devolve o id gerado. */
    public function inserir(array $d): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO agendamentos
                (cliente_id, profissional_id, servico_id, usuario_id, data_hora_inicio, status, observacoes)
             VALUES
                (:cliente_id, :profissional_id, :servico_id, :usuario_id, :inicio, :status, :observacoes)'
        );
        $stmt->execute([
            ':cliente_id'      => $d['cliente_id'],
            ':profissional_id' => $d['profissional_id'],
            ':servico_id'      => $d['servico_id'],
            ':usuario_id'      => $d['usuario_id'],
            ':inicio'          => $d['data_hora_inicio'],
            ':status'          => 'agendado',
            ':observacoes'     => $d['observacoes'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /** UPDATE (RF07): remarcar / editar os dados do agendamento. */
    public function atualizar(int $id, array $d): void
    {
        $stmt = $this->db->prepare(
            'UPDATE agendamentos
                SET cliente_id = :cliente_id,
                    profissional_id = :profissional_id,
                    servico_id = :servico_id,
                    data_hora_inicio = :inicio,
                    observacoes = :observacoes
              WHERE id = :id'
        );
        $stmt->execute([
            ':cliente_id'      => $d['cliente_id'],
            ':profissional_id' => $d['profissional_id'],
            ':servico_id'      => $d['servico_id'],
            ':inicio'          => $d['data_hora_inicio'],
            ':observacoes'     => $d['observacoes'],
            ':id'              => $id,
        ]);
    }

    /** RF08: concluir ou cancelar. */
    public function atualizarStatus(int $id, string $status): void
    {
        if (!in_array($status, self::STATUS, true)) {
            throw new InvalidArgumentException('Status inválido.');
        }
        $stmt = $this->db->prepare('UPDATE agendamentos SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    /** DELETE (RF08). */
    public function excluir(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM agendamentos WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * RF09: o profissional já tem atendimento que se sobrepõe a este horário?
     * Dois intervalos se sobrepõem quando: inicio_existente < fim_novo E fim_existente > inicio_novo.
     * Agendamentos cancelados não ocupam horário. $ignorarId serve para a edição.
     */
    public function haConflito(int $profissionalId, string $inicio, int $duracaoMin, ?int $ignorarId = null): bool
    {
        $fim = date('Y-m-d H:i:s', strtotime($inicio) + $duracaoMin * 60);

        $sql = "SELECT COUNT(*)
                  FROM agendamentos a
                  JOIN servicos s ON s.id = a.servico_id
                 WHERE a.profissional_id = :profissional_id
                   AND a.status <> 'cancelado'
                   AND a.data_hora_inicio < :fim
                   AND DATE_ADD(a.data_hora_inicio, INTERVAL s.duracao_min MINUTE) > :inicio";
        $params = [
            ':profissional_id' => $profissionalId,
            ':fim'             => $fim,
            ':inicio'          => $inicio,
        ];

        if ($ignorarId !== null) {
            $sql .= ' AND a.id <> :ignorar_id';
            $params[':ignorar_id'] = $ignorarId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
