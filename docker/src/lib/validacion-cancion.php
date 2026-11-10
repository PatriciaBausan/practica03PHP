<?php
require_once __DIR__ . '/canciones.php';

const CATEGORIAS = ['electronica', 'rock', 'bandas-sonoras'];
const AUDIOS_ADMITIDOS = [
    'audio/Prefekt.mp3',
    'audio/vientodeandalucia.mp3',
    'audio/pasiondelanoche.mp3',
    'audio/fuegogitano.mp3',
    'audio/gvidon-gvidon-medicine.mp3',
    'audio/navidad.mp3',
    'audio/bring.mp3',
    'audio/Worth.mp3',
    'audio/Noche.mp3',
    'audio/Ciume.mp3',
    'audio/Adeus.mp3',
];

function validarCancion(array $post): array
{
    $old = ['title' => '', 'artist' => '', 'category' => '', 'duration' => '', 'src' => ''];
    $errors = [];

    foreach ($old as $campo => $vacio) {
        $v = $post[$campo] ?? '';
        if (!is_string($v)) {
            $errors[$campo] = 'El dato no tiene un formato válido.';
            continue;
        }
        $old[$campo] = ($campo === 'title' || $campo === 'artist') ? trim($v) : $v;
    }

    foreach (['title' => 'título', 'artist' => 'artista'] as $campo => $nombre) {
        if (isset($errors[$campo])) {
            continue;
        }
        $largo = mb_strlen($old[$campo]);
        if ($largo === 0) {
            $errors[$campo] = 'El ' . $nombre . ' es obligatorio.';
        } elseif ($largo > 80) {
            $errors[$campo] = 'El ' . $nombre . ' no puede superar 80 caracteres.';
        }
    }
    if (!isset($errors['category']) && !in_array($old['category'], CATEGORIAS, true)) {
        $errors['category'] = 'Categoría no admitida.';
    }
    if (!isset($errors['src']) && !in_array($old['src'], AUDIOS_ADMITIDOS, true)) {
        $errors['src'] = 'Audio no admitido.';
    }
    $duration = 0;
    if (!isset($errors['duration'])) {
        $d = filter_var($old['duration'], FILTER_VALIDATE_INT);
        if ($d === false || $d < 1 || $d > 1800) {
            $errors['duration'] = 'La duración debe ser un entero entre 1 y 1800.';
        } else {
            $duration = $d;
        }
    }

    $datos = null;
    if (!$errors) {
        $datos = [
            'title' => $old['title'],
            'artist' => $old['artist'],
            'category' => $old['category'],
            'duration' => $duration,
            'src' => $old['src'],
        ];
    }
    return [$errors, $old, $datos];
}