<?php
// UC01: logout (só aceita POST com token CSRF)
require __DIR__ . '/app/bootstrap.php';

if (eh_post()) {
    csrf_validar();
    $_SESSION = [];
    session_destroy();
}

redirecionar('login.php');
