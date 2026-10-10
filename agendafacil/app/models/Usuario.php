<?php
require_once __DIR__ . '/../Database.php';

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /** Usado no login (UC01): devolve o usuário ou null. */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $linha = $stmt->fetch();
        return $linha ?: null;
    }
}
