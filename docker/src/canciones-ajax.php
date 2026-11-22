<?php
require_once __DIR__ . '/lib/canciones.php';
require_once __DIR__ . '/lib/acceso.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Catálogo AJAX</title>
  <script src="scripts/catalogo-ajax.js" defer></script>
</head>
<body>
  <h1>Catálogo (AJAX)</h1>

  <p id="mensajes" role="status" aria-live="polite"></p>

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