<?php
// Carrega tudo o que as páginas precisam. Cada controller começa com:
//   require __DIR__ . '/app/bootstrap.php';

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/helpers.php';

require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/models/Cliente.php';
require_once __DIR__ . '/models/Profissional.php';
require_once __DIR__ . '/models/Servico.php';
require_once __DIR__ . '/models/Agendamento.php';

/** Renderiza uma view dentro do layout (header + conteúdo + footer). */
function renderizar(string $view, array $dados = []): void
{
    extract($dados);
    require __DIR__ . '/../views/layout/header.php';
    require __DIR__ . '/../views/' . $view . '.php';
    require __DIR__ . '/../views/layout/footer.php';
}
