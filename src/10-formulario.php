<?php
require_once __DIR__ . '/lib/canciones.php';

$allowedCategories = ['electronica', 'rock', 'bandas-sonoras'];
$allowedSrc = ['audio/Orgullo.mp3', 'audio/Pa Madrid.mp3'];

$old = ['title' => '', 'artist' => '', 'category' => '', 'duration' => '', 'src' => ''];
$errors = [];
$preview = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $campo => $vacio) {
        $valor = $_POST[$campo] ?? '';
        if (!is_string($valor)) {
            $errors[$campo] = 'El dato no tiene un formato válido.';
            continue;
        }
        $old[$campo] = ($campo === 'title' || $campo === 'artist') ? trim($valor) : $valor;
    }

    if (!isset($errors['title']) && $old['title'] === '') {
        $errors['title'] = 'El título es obligatorio.';
    }
    if (!isset($errors['artist']) && $old['artist'] === '') {
        $errors['artist'] = 'El artista es obligatorio.';
    }
    if (!isset($errors['category']) && !in_array($old['category'], $allowedCategories, true)) {
        $errors['category'] = 'Categoría no admitida.';
    }
    if (!isset($errors['src']) && !in_array($old['src'], $allowedSrc, true)) {
        $errors['src'] = 'Audio no admitido.';
    }
    $duration = 0;
    if (!isset($errors['duration'])) {
        $d = filter_var($old['duration'], FILTER_VALIDATE_INT); // false = no es entero
        if ($d === false || $d < 1 || $d > 1800) {
            $errors['duration'] = 'La duración debe ser un entero entre 1 y 1800.';
        } else {
            $duration = $d;
        }
    }

    if ($errors) {
        http_response_code(400);
    } else {
        $preview = $old;
        $preview['duration'] = $duration;
    }
}

function mensajeError(array $errors, string $campo): string
{
    return isset($errors[$campo]) ? '<span class="error">' . e($errors[$campo]) . '</span>' : '';
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Nueva canción (vista previa)</title></head>
<body>
<h1>Nueva canción</h1>

<?php if ($preview !== null) { ?>
  <h2>Vista previa, canción no guardada</h2>
  <p><?php echo e($preview['title']); ?> · <?php echo e($preview['artist']); ?> ·
     <?php echo duracionLegible($preview['duration']); ?></p>
<?php } ?>

<form method='post' action='10-formulario.php'>
  <p><label>Título <input type='text' name='title' value='<?php echo e($old['title']); ?>'></label>
     <?php echo mensajeError($errors, 'title'); ?></p>
  <p><label>Artista <input type='text' name='artist' value='<?php echo e($old['artist']); ?>'></label>
     <?php echo mensajeError($errors, 'artist'); ?></p>
  <p><label>Categoría
    <select name='category'>
      <?php foreach ($allowedCategories as $c) { ?>
        <option value='<?php echo e($c); ?>' <?php echo $old['category'] === $c ? 'selected' : ''; ?>><?php echo e($c); ?></option>
      <?php } ?>
    </select></label>
     <?php echo mensajeError($errors, 'category'); ?></p>
  <p><label>Duración (s) <input type='text' name='duration' value='<?php echo e($old['duration']); ?>'></label>
     <?php echo mensajeError($errors, 'duration'); ?></p>
  <p><label>Audio
    <select name='src'>
      <?php foreach ($allowedSrc as $s) { ?>
        <option value='<?php echo e($s); ?>' <?php echo $old['src'] === $s ? 'selected' : ''; ?>><?php echo e($s); ?></option>
      <?php } ?>
    </select></label>
     <?php echo mensajeError($errors, 'src'); ?></p>
  <button type='submit'>Previsualizar</button>
</form>
</body>
</html>