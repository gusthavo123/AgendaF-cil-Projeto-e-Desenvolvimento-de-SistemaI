<?php
// UC01: Autenticar no sistema (login)
require __DIR__ . '/app/bootstrap.php';

if (usuario_logado() !== null) {
    redirecionar('index.php');
}

$email = '';

if (eh_post()) {
    csrf_validar();

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $usuario = (new Usuario())->buscarPorEmail($email);

    // A senha é conferida contra o hash guardado no banco (RNF02)
    if ($usuario !== null && password_verify($senha, $usuario['senha_hash'])) {
        session_regenerate_id(true); // evita fixação de sessão
        $_SESSION['usuario'] = ['id' => (int) $usuario['id'], 'nome' => $usuario['nome']];
        redirecionar('index.php');
    }

    flash('danger', 'E-mail ou senha inválidos.');
}

renderizar('login', ['titulo' => 'Entrar', 'email' => $email]);
