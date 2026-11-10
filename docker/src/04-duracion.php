<?php
$duration = 60;
$minutes = intdiv($duration, 60); 
$seconds = $duration % 60;        
echo 'Duración: ' . $minutes . ' minutos y ' . $seconds . ' segundos';