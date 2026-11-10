<?php
require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/almacen-json.php';
require_once __DIR__ . '/lib/formulario-cancion.php';

$errors = [];
$old = ['title' => '', 'artist' => '', 'category' => '', 'duration' => '', 'src' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    [$errors, $old, $datos] = validarCancion($_POST);

    if ($datos !== null) {
        try {
            modificarColeccion(RUTA_CANCIONES, function (array $songs) use ($datos): array {
                $maxId = 0;
                foreach ($songs as $s) {
                    if ($s['id'] > $maxId) {
                        $maxId = $s['id'];
                    }
                }
                $songs[] = ['id' => $maxId + 1] + $datos; // el id lo pone el servidor
                return [$songs, null];
            });
            header('Location: canciones.php', true, 303);
            exit;
        } catch (JsonException | RuntimeException $e) {
            error_log('Alta de canción: ' . $e->getMessage());
            $errors['general'] = 'No se pudo guardar la canción.';
        }
    }
    if ($errors) {
        http_response_code(isset($errors['general']) ? 500 : 400);
    }
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Nueva canción</title></head>
<body>
<h1>Nueva canción</h1>
<?php if (isset($errors['general'])) { ?><p><?php echo e($errors['general']); ?></p><?php } ?>
<?php pintarFormulario($old, $errors, 'cancion-nueva.php', 'Guardar'); ?>
<p><a href='canciones.php'>Volver al catálogo</a></p>
</body>
</html>