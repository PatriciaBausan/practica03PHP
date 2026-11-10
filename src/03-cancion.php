<?php
$title = 'Danzar de Mariposas';
$artist = 'El Barrio';
$duration = 185;
$volume = 0.5;
$available = true;
const APP_NAME = 'Jukebox de la asociación';

$safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeArtist = htmlspecialchars($artist, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title><?php echo APP_NAME; ?></title></head>
<body>
  <h1><?php echo $safeTitle; ?></h1>
  <p>Ahora suena «<?php echo $safeTitle; ?>» de <?php echo $safeArtist; ?>.</p>
</body>
</html>