<?php
require_once __DIR__ . '/../lib/sesion.php';
require_once __DIR__ . '/../lib/almacen-json.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

try {
    $songs = leerColeccion(RUTA_CANCIONES);
    echo json_encode($songs, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (JsonException | RuntimeException $e) {
    error_log('API canciones: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo leer el catálogo'], JSON_UNESCAPED_UNICODE);
}