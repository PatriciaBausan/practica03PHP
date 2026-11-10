<?php
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function duracionLegible(int $total): string
{
    $minutes = intdiv($total, 60);
    $seconds = $total % 60;
    $palabraMin = $minutes === 1 ? 'minuto' : 'minutos';
    $palabraSeg = $seconds === 1 ? 'segundo' : 'segundos';
    return $minutes . ' ' . $palabraMin . ' y ' . $seconds . ' ' . $palabraSeg;
}