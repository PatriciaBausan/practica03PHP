<?php
require_once __DIR__ . '/lib/canciones.php';
$songs = require __DIR__ . '/lib/catalogo-inicial.php';
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Catálogo</title></head>
<body>
<h1>Catálogo</h1>
<?php foreach ($songs as $song) { ?>
  <article>
    <h2><?php echo e($song['title']); ?></h2>
    <p><?php echo e($song['artist']); ?> · <?php echo duracionLegible($song['duration']); ?></p>
  </article>
<?php } ?>
</body>
</html>