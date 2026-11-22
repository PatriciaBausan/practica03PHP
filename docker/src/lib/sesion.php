<?php
require_once __DIR__ . '/canciones.php'; 

function parametrosCookie(): array
{
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    return ['path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => $https];
}

ini_set('session.use_strict_mode', '1');
session_name('JUKEBOXSESSID');
session_set_cookie_params(parametrosCookie());
session_start();

if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function campoCsrf(): string
{
    return "<input type='hidden' name='csrf' value='" . e($_SESSION['csrf']) . "'>";
}

function csrfValido(): bool
{
    $recibido = $_POST['csrf'] ?? null;
    return is_string($recibido) && hash_equals($_SESSION['csrf'], $recibido);
}

function exigirCsrf(): void
{
    if (!csrfValido()) {
        http_response_code(403);
        echo 'Petición no válida.';
        exit;
    }
}