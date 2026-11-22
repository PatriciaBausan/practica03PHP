<?php
require_once __DIR__ . '/lib/sesion.php';
require_once __DIR__ . '/lib/almacen-json.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $opciones = opcionesCatalogo();
} catch (JsonException | RuntimeException $e) {
    error_log('canciones-ajax: ' . $e->getMessage());
    $opciones = ['categorias' => [], 'audios' => []];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Catálogo AJAX</title>
  <meta name="csrf-token" content="<?= e($_SESSION['csrf']) ?>">
  <script src="scripts/catalogo-ajax.js" defer></script>
</head>
<body>
  <h1>Catálogo (AJAX)</h1>

  <p id="mensajes" role="status" aria-live="polite"></p>

 <form id="formulario-cancion">
    <h2 id="titulo-formulario">Añadir canción</h2>

    <p id="modo-edicion"></p>

    <label for="title">Título</label>
    <input id="title" name="title" type="text" maxlength="80" required>

    <label for="artist">Artista</label>
    <input id="artist" name="artist" type="text" maxlength="80" required>

    <label for="category">Categoría</label>
    <select id="category" name="category" required>
      <option value="">Elige una categoría</option>
      <?php foreach ($opciones['categorias'] as $categoria) { ?>
        <option value="<?= e($categoria) ?>"><?= e($categoria) ?></option>
      <?php } ?>
    </select>

    <label for="duration">Duración (segundos)</label>
    <input id="duration" name="duration" type="number" min="1" max="1800" step="1" required>

    <label for="src">Audio</label>
    <select id="src" name="src" required>
      <option value="">Elige un audio</option>
      <?php foreach ($opciones['audios'] as $audio) { ?>
        <option value="<?= e($audio) ?>"><?= e($audio) ?></option>
      <?php } ?>
    </select>

    <button type="submit" id="boton-guardar">Añadir canción</button>

<button type="button" id="boton-cancelar" hidden>
    Cancelar edición
</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Título</th>
        <th>Artista</th>
        <th>Categoría</th>
        <th>Duración</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="tabla-canciones"></tbody>
  </table>
</body>
</html>