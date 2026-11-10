<?php
require_once __DIR__ . '/lib/canciones.php';
$songs = require __DIR__ . '/lib/catalogo-inicial.php';

$allowed = ['todas', 'electronica', 'rock', 'bandas-sonoras'];
$category = $_GET['category'] ?? 'todas'; 
$invalid = !is_string($category) || !in_array($category, $allowed, true);
$categories = [
    'todas' => 'Todas',
    'electronica' => 'Electrónica',
    'rock' => 'Rock',
    'bandas-sonoras' => 'Bandas sonoras',
];

$shown = [];
if ($invalid) {
    http_response_code(400); 
} elseif ($category === 'todas') {
    $shown = $songs;
} else {
    foreach ($songs as $song) {
        if ($song['category'] === $category) {
            $shown[] = $song;
        }
    }
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Filtro</title></head>
<body>
<h1>Catálogo por categoría</h1>
<form method="get">
  <label for="category">Categoría:</label>
  <select id="category" name="category">
    <?php foreach ($categories as $value => $label) { ?>
      <option value="<?php echo e($value); ?>" <?php echo !$invalid && $category === $value ? 'selected' : ''; ?>>
        <?php echo e($label); ?>
      </option>
    <?php } ?>
  </select>
  <button type="submit">Buscar</button>
</form>

<?php if ($invalid) { ?>

    <p>Categoría no válida</p>

<?php } elseif (count($shown) === 0) { ?>

    <p>No hay canciones en esta categoría.</p>

<?php } else { ?>

    <?php foreach ($shown as $song) { ?>
        <article>
            <h2><?php echo e($song['title']); ?></h2>
            <p>Artista: <?php echo e($song['artist']); ?></p>
            <p>Categoría: <?php echo e($categories[$song['category']]); ?></p>
        </article>
    <?php } ?>

<?php } ?>

</body>
</html>