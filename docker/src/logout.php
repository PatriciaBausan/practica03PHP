<?php
require_once __DIR__ . '/lib/sesion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}
exigirCsrf();

$_SESSION = [];
setcookie(session_name(), '', ['expires' => time() - 42000] + parametrosCookie());
session_destroy();
header('Location: /login.php', true, 303);
exit;