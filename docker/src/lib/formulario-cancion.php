<?php
require_once __DIR__ . '/validacion-cancion.php';

function pintarFormulario(array $old, array $errors, string $accion, string $boton, ?int $id = null): void
{
    $err = fn(string $c): string => isset($errors[$c]) ? '<span>' . e($errors[$c]) . '</span>' : '';
    ?>
    <form method='post' action='<?php echo e($accion); ?>'>
      <?php echo campoCsrf(); ?>
      <?php if ($id !== null) { ?><input type='hidden' name='id' value='<?php echo $id; ?>'><?php } ?>
      <p><label>Título <input type='text' name='title' value='<?php echo e($old['title']); ?>'></label><?php echo $err('title'); ?></p>
      <p><label>Artista <input type='text' name='artist' value='<?php echo e($old['artist']); ?>'></label><?php echo $err('artist'); ?></p>
      <p><label>Categoría <select name='category'>
        <?php foreach (CATEGORIAS as $c) { ?>
          <option value='<?php echo e($c); ?>' <?php echo $old['category'] === $c ? 'selected' : ''; ?>><?php echo e($c); ?></option>
        <?php } ?>
      </select></label><?php echo $err('category'); ?></p>
      <p><label>Duración (s) <input type='text' name='duration' value='<?php echo e($old['duration']); ?>'></label><?php echo $err('duration'); ?></p>
      <p><label>Audio <select name='src'>
        <?php foreach (AUDIOS_ADMITIDOS as $s) { ?>
          <option value='<?php echo e($s); ?>' <?php echo $old['src'] === $s ? 'selected' : ''; ?>><?php echo e($s); ?></option>
        <?php } ?>
      </select></label><?php echo $err('src'); ?></p>
      <button type='submit'><?php echo e($boton); ?></button>
    </form>
    <?php
}