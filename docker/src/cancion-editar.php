<?php
require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/almacen-json.php';
require_once __DIR__ . '/lib/formulario-cancion.php';

$esPost = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($esPost) {
    exigirCsrf();
}

$id = idPositivo($esPost ? ($_POST['id'] ?? null) : ($_GET['id'] ?? null));
if ($id === null) {
    http_response_code(400);
    echo 'Identificador no válido.';
    exit;
}

try {
    $songs = leerColeccion(RUTA_CANCIONES);
} catch (JsonException | RuntimeException $e) {
    error_log('Edición: ' . $e->getMessage());
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

$errors = [];
$old = [
    'title' => $actual['title'],
    'artist' => $actual['artist'],
    'category' => $actual['category'],
    'duration' => (string) $actual['duration'],
    'src' => $actual['src'],
];

if ($esPost) {
    [$errors, $old, $datos] = validarCancion($_POST);
    if ($datos !== null) {
        try {
            modificarColeccion(RUTA_CANCIONES, function (array $songs) use ($id, $datos): array {
                foreach ($songs as $position => $song) {
                    if ($song['id'] === $id) {
                        $songs[$position] = ['id' => $id] + $datos; // se conserva el id
                        return [$songs, null];
                    }
                }
                throw new ErrorDeNegocio('La canción ya no existe.');
            });
            header('Location: canciones.php', true, 303);
            exit;
        } catch (ErrorDeNegocio $e) {
            http_response_code(404);
            echo 'La canción no existe.';
            exit;
        } catch (JsonException | RuntimeException $e) {
            error_log('Edición: ' . $e->getMessage());
            $errors['general'] = 'No se pudo guardar el cambio.';
        }
    }
    if ($errors) {
        http_response_code(isset($errors['general']) ? 500 : 400);
    }
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Editar canción</title></head>
<body>
<h1>Editar canción</h1>
<?php if (isset($errors['general'])) { ?><p><?php echo e($errors['general']); ?></p><?php } ?>
<?php pintarFormulario($old, $errors, 'cancion-editar.php', 'Guardar cambios', $id); ?>
<p><a href='canciones.php'>Cancelar</a></p>
</body>
</html>