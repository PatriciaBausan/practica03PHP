<?php
$duration = 185;
if ($duration === 0) {
    $estado = 'Sin duración';
} elseif ($duration <= 240) {
    $estado = 'Hasta cuatro minutos';
} else {
    $estado = 'Más de cuatro minutos';
}
echo $estado;
