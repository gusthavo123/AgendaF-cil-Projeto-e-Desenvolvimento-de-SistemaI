<?php
// Model base: o CRUD genérico (Create, Read, Update, Delete) usado por
// Cliente, Profissional e Servico. Todas as consultas usam prepared statements.
// Os nomes de tabela e coluna vêm das subclasses (nunca do usuário).

require_once __DIR__ . '/../Database.php';

abstract class BaseModel
{
    protected string $tabela;       // nome da tabela
    protected array  $campos;       // colunas editáveis (sem o id)
    protected string $colunaFk;     // coluna em "agendamentos" que aponta para esta tabela
    protected string $ordem = 'nome';

    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /** READ: lista todos os registros. */
    public function listarTodos(): array
    {
        $sql = "SELECT * FROM {$this->tabela} ORDER BY {$this->ordem}";
        return $this->db->query($sql)->fetchAll();
    }

    /** READ: busca um registro pelo id (ou null). */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabela} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();
        return $linha ?: null;
    }

    /** CREATE: insere e devolve o id gerado. */
    public function inserir(array $dados): int
    {
        $colunas = implode(', ', $this->campos);
        $marcas  = implode(', ', array_map(fn($c) => ':' . $c, $this->campos));

        $stmt = $this->db->prepare("INSERT INTO {$this->tabela} ($colunas) VALUES ($marcas)");
        $stmt->execute($this->parametros($dados));

        return (int) $this->db->lastInsertId();
    }

    /** UPDATE: atualiza as colunas editáveis do registro. */
    public function atualizar(int $id, array $dados): void
    {
        $atribuicoes = implode(', ', array_map(fn($c) => "$c = :$c", $this->campos));

        $stmt = $this->db->prepare("UPDATE {$this->tabela} SET $atribuicoes WHERE id = :id");
        $stmt->execute($this->parametros($dados) + [':id' => $id]);
    }

    /** DELETE: remove o registro. */
    public function excluir(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->tabela} WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    /** Regra RF10: existe agendamento ligado a este registro? */
    public function temAgendamentos(int $id): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM agendamentos WHERE {$this->colunaFk} = :id"
        );
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Monta o array de parâmetros (:coluna => valor) só com os campos permitidos. */
    private function parametros(array $dados): array
    {
        $parametros = [];
        foreach ($this->campos as $campo) {
            $parametros[':' . $campo] = $dados[$campo] ?? null;
        }
        return $parametros;
    }
}
