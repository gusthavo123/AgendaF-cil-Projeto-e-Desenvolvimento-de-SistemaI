<?php
// Funções auxiliares: sessão, login, CSRF, mensagens e formatação.

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

/** Escapa texto para exibir no HTML (proteção contra XSS). */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// ---------- Mensagens (flash) ----------

function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function flash_obter(): array
{
    $mensagens = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensagens;
}

// ---------- CSRF ----------

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Interrompe a requisição POST se o token CSRF for inválido. */
function csrf_validar(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!hash_equals(csrf_token(), $enviado)) {
        http_response_code(400);
        exit('Requisição inválida (token CSRF).');
    }
}

// ---------- Autenticação ----------

function usuario_logado(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function exigir_login(): void
{
    if (usuario_logado() === null) {
        redirecionar('login.php');
    }
}

function eh_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// ---------- Formatação ----------

function fmt_data_hora(string $dataHora): string
{
    return date('d/m/Y H:i', strtotime($dataHora));
}

function fmt_hora(string $dataHora): string
{
    return date('H:i', strtotime($dataHora));
}

function fmt_moeda($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

/** A data está no formato Y-m-d e existe no calendário? */
function data_valida(string $data): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d !== false && $d->format('Y-m-d') === $data;
}

/** Converte "35,50" ou "35.50" em float; devolve null se não for número. */
function para_decimal(string $texto): ?float
{
    $texto = str_replace(',', '.', trim($texto));
    return is_numeric($texto) ? (float) $texto : null;
}

/** Rótulo amigável para o status do agendamento. */
function rotulo_status(string $status): string
{
    return [
        'agendado'  => 'Agendado',
        'concluido' => 'Concluído',
        'cancelado' => 'Cancelado',
    ][$status] ?? $status;
}

/** Classe de cor do Bootstrap para cada status. */
function cor_status(string $status): string
{
    return [
        'agendado'  => 'primary',
        'concluido' => 'success',
        'cancelado' => 'secondary',
    ][$status] ?? 'light';
}
