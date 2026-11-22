<?php
require_once __DIR__ . '/../lib/respuestas.php';
require_once __DIR__ . '/../lib/sesion.php';          
require_once __DIR__ . '/../lib/almacen-json.php';    

const CATEGORIAS_PERMITIDAS = ['rumba', 'flamenco', 'pop'];
const AUDIOS_PERMITIDOS = ['audio/demo-01.mp3'];


function validarCancion(array $d): ?string
{
    // Primero los tipos...
    foreach (['title', 'artist', 'category', 'src'] as $campo) {
        if (!isset($d[$campo]) || !is_string($d[$campo])) {
            return "El campo $campo debe ser un texto.";
        }
    }
    if (!isset($d['duration']) || !is_int($d['duration'])) {
        return 'La duración debe ser un entero.';
    }
    $title = trim($d['title']);
    $artist = trim($d['artist']);
    if ($title === '' || mb_strlen($title) > 80) {
        return 'El título es obligatorio y admite hasta 80 caracteres.';
    }
    if ($artist === '' || mb_strlen($artist) > 80) {
        return 'El artista es obligatorio y admite hasta 80 caracteres.';
    }
    if (!in_array($d['category'], CATEGORIAS_PERMITIDAS, true)) {
        return 'La categoría no está admitida.';
    }
    if ($d['duration'] < 1 || $d['duration'] > 1800) {
        return 'La duración debe ser un entero entre 1 y 1800 segundos.';
    }
    if (!in_array($d['src'], AUDIOS_PERMITIDOS, true)) {
        return 'El audio no pertenece a la lista autorizada.';
    }
    return null;
}

if (!isset($_SESSION['usuario_id'])) {
    responderJson(401, ['ok' => false, 'error' => 'Debes iniciar sesión.']);
}

$action = $_GET['action'] ?? 'list';
$permitidas = ['list', 'create', 'update', 'delete'];
if (!is_string($action) || !in_array($action, $permitidas, true)) {
    responderJson(400, ['ok' => false, 'error' => 'Acción desconocida.']);
}

$metodo = $action === 'list' ? 'GET' : 'POST';
if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
    header('Allow: ' . $metodo);
    responderJson(405, ['ok' => false, 'error' => "Esta acción solo admite $metodo."]);
}

if ($action === 'list') {
    try {
        $canciones = leerColeccion(RUTA_CANCIONES);
    } catch (JsonException | RuntimeException $e) {
        error_log('API catalogo: ' . $e->getMessage()); // el detalle va al log
        responderJson(500, ['ok' => false, 'error' => 'No se pudo leer el catálogo.']);
    }
    responderJson(200, ['ok' => true, 'data' => array_values($canciones)]);
}

$tokenRecibido = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
$tokenSesion = $_SESSION['csrf'] ?? null;
if (!is_string($tokenRecibido) || !is_string($tokenSesion) || !hash_equals($tokenSesion, $tokenRecibido)) {
    responderJson(403, ['ok' => false, 'error' => 'Token de protección ausente o incorrecto.']);
}

$tipo = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($tipo, 'application/json') !== 0) {
    responderJson(415, ['ok' => false, 'error' => 'El cuerpo debe enviarse como application/json.']);
}

try {
    $cuerpo = file_get_contents('php://input');
    $objeto = json_decode($cuerpo, false, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    responderJson(400, ['ok' => false, 'error' => 'El JSON recibido no es válido.']);
}
if (!is_object($objeto)) {
    responderJson(400, ['ok' => false, 'error' => 'Se esperaba un objeto JSON.']);
}
$datos = get_object_vars($objeto);

if ($action === 'create') {
    $error = validarCancion($datos);
    if ($error !== null) {
        responderJson(422, ['ok' => false, 'error' => $error]);
    }
    try {
        $lista = modificarColeccion(RUTA_CANCIONES, function (array $canciones) use ($datos) {
            $maxId = 0;
            foreach ($canciones as $c) {
                $maxId = max($maxId, (int) ($c['id'] ?? 0));
            }
            $canciones[] = [
                'id'       => $maxId + 1,
                'title'    => trim($datos['title']),
                'artist'   => trim($datos['artist']),
                'category' => $datos['category'],
                'duration' => $datos['duration'],
                'src'      => $datos['src'],
            ];
            return [$canciones, $canciones]; 
        });
    } catch (ErrorDeNegocio $e) {
        responderJson(422, ['ok' => false, 'error' => $e->getMessage()]);
    } catch (JsonException | RuntimeException $e) {
        error_log('API catalogo create: ' . $e->getMessage());
        responderJson(500, ['ok' => false, 'error' => 'No se pudo guardar el catálogo.']);
    }
    responderJson(201, ['ok' => true, 'data' => array_values($lista)]);
}

responderJson(400, ['ok' => false, 'error' => 'Acción todavía no disponible.']);