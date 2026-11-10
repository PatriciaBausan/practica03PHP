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
function esCancionValida(mixed $s): bool
{
    return is_array($s)
        && isset($s['id'], $s['title'], $s['artist'], $s['category'], $s['duration'], $s['src'])
        && is_int($s['id']) && is_int($s['duration'])
        && is_string($s['title']) && is_string($s['artist'])
        && is_string($s['category']) && is_string($s['src']);
}
function idPositivo(mixed $valor): ?int
{
    if (!is_string($valor)) {
        return null;
    }
    $n = filter_var($valor, FILTER_VALIDATE_INT);
    return ($n !== false && $n > 0) ? $n : null;
}