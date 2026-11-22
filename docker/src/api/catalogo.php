<?php
require_once __DIR__ . '/../lib/respuestas.php';
require_once __DIR__ . '/../lib/canciones.php';

$action = $_GET['action'] ?? 'list';
$permitidas = ['list', 'create', 'update', 'delete'];
if (!is_string($action) || !in_array($action, $permitidas, true)) {
    responderJson(400, ['ok' => false, 'error' => 'Acción desconocida.']);
}

if ($action === 'list') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('Allow: GET');
        responderJson(405, ['ok' => false, 'error' => 'Esta acción solo admite GET.']);
    }
    try {
        $canciones = NOMBRE_DE_TU_FUNCION_DE_LECTURA();
    } catch (Throwable $e) {
        error_log($e->getMessage());
        responderJson(500, ['ok' => false, 'error' => 'No se pudo leer el catálogo.']);
    }
    responderJson(200, ['ok' => true, 'data' => array_values($canciones)]);
}

responderJson(405, ['ok' => false, 'error' => 'Método no permitido para esta acción.']);