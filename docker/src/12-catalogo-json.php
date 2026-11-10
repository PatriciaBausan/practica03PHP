<?php
require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/almacen-json.php';

$songs = [];
$error = null;
try {
    $songs = leerColeccion(RUTA_CANCIONES);
} catch (JsonException | RuntimeException $e) {
    error_log('Catálogo: ' . $e->getMessage());
    http_response_code(500);
    $error = 'No se puede mostrar el catálogo ahora mismo.';
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Catálogo</title></head>
<body>
<h1>Catálogo</h1>
<p><a href='home.php'>Inicio</a> · <a href='cancion-nueva.php'>Añadir canción</a></p>
<?php if ($error !== null) { ?>
  <p><?php echo e($error); ?></p>
<?php } elseif (count($songs) === 0) { ?>
  <p>Todavía no hay canciones.</p>
<?php } else { ?>
  <?php foreach ($songs as $song) { ?>
    <article>
      <h2><?php echo e($song['title']); ?></h2>
      <p><?php echo e($song['artist']); ?> · <?php echo e($song['category']); ?> · <?php echo duracionLegible($song['duration']); ?></p>
      <a href='cancion-editar.php?id=<?php echo $song['id']; ?>'>Editar</a>
      <a href='cancion-eliminar.php?id=<?php echo $song['id']; ?>'>Eliminar</a>    
    </article>
  <?php } ?>
<?php } ?>
</body>
</html>