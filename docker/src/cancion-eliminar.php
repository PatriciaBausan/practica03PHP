<?php
require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/almacen-json.php';

$metodo = $_SERVER['REQUEST_METHOD'];
if ($metodo !== 'GET' && $metodo !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    exit;
}
if ($metodo === 'POST') {
    exigirCsrf();
}

$id = idPositivo($metodo === 'POST' ? ($_POST['id'] ?? null) : ($_GET['id'] ?? null));
if ($id === null) {
    http_response_code(400);
    echo 'Identificador no válido.';
    exit;
}

if ($metodo === 'POST') {
    try {
        modificarColeccion(RUTA_CANCIONES, function (array $songs) use ($id): array {
            foreach ($songs as $position => $song) {
                if ($song['id'] === $id) {
                    unset($songs[$position]);
                    return [array_values($songs), null]; 
                }
            }
            throw new ErrorDeNegocio('No existe.');
        });
        header('Location: canciones.php', true, 303);
        exit;
    } catch (ErrorDeNegocio $e) {
        http_response_code(404);
        echo 'La canción no existe.';
        exit;
    } catch (JsonException | RuntimeException $e) {
        error_log('Borrado: ' . $e->getMessage());
        http_response_code(500);
        echo 'No se pudo eliminar.';
        exit;
    }
}

try {
    $songs = leerColeccion(RUTA_CANCIONES);
} catch (JsonException | RuntimeException $e) {
    error_log('Confirmación: ' . $e->getMessage());
    http_response_code(500);
    echo 'No se puede leer el catálogo.';
    exit;
}
$actual = null;
foreach ($songs as $song) {
    if ($song['id'] === $id) {
        $actual = $song;
        break;
    }
}
if ($actual === null) {
    http_response_code(404);
    echo 'La canción no existe.';
    exit;
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Eliminar canción</title></head>
<body>
<h1>Eliminar canción</h1>
<p>¿Eliminar «<?php echo e($actual['title']); ?>»? El archivo de audio no se borra.</p>
<form method='post' action='cancion-eliminar.php'>
  <?php echo campoCsrf(); ?>
  <input type='hidden' name='id' value='<?php echo $actual['id']; ?>'>
  <button type='submit'>Sí, eliminar</button>
</form>
<p><a href='canciones.php'>Cancelar</a></p>
</body>
</html>