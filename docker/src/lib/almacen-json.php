<?php
const RUTA_CANCIONES = '/var/www/data/canciones.json';
const RUTA_USUARIOS = '/var/www/data/usuarios.json';

function leerColeccion(string $ruta): array
{
    $f = fopen($ruta, 'r');
    if ($f === false) {
        throw new RuntimeException('No se puede abrir el archivo de datos.');
    }
    try {
        if (!flock($f, LOCK_SH)) {
            throw new RuntimeException('No se pudo bloquear el archivo.');
        }
        $text = stream_get_contents($f);
        flock($f, LOCK_UN);
    } finally {
        fclose($f);
    }
    if ($text === false) {
        throw new RuntimeException('No se pudo leer el archivo.');
    }
    $data = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($data) || !array_is_list($data)) {
        throw new RuntimeException('El archivo no contiene una lista.');
    }
    return $data;
}
class ErrorDeNegocio extends RuntimeException
{
}

function modificarColeccion(string $ruta, callable $cambio): mixed
{
    $f = fopen($ruta, 'r+'); // r+ lee y escribe sin vaciar el archivo
    if ($f === false) {
        throw new RuntimeException('No se puede abrir el archivo de datos.');
    }
    try {
        if (!flock($f, LOCK_EX)) {
            throw new RuntimeException('No se pudo bloquear el archivo.');
        }
        $text = stream_get_contents($f);
        if ($text === false) {
            throw new RuntimeException('No se pudo leer el archivo.');
        }
        $records = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($records) || !array_is_list($records)) {
            throw new RuntimeException('El archivo no contiene una lista.');
        }

        [$nuevos, $resultado] = $cambio($records);

        $json = json_encode(
            array_values($nuevos),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        if (!rewind($f)) {
            throw new RuntimeException('No se pudo volver al inicio.');
        }
        $bytes = fwrite($f, $json);
        if ($bytes === false || $bytes !== strlen($json)) {
            throw new RuntimeException('Escritura incompleta.');
        }
        if (!ftruncate($f, strlen($json)) || !fflush($f)) {
            throw new RuntimeException('No se pudo cerrar la escritura.');
        }
        return $resultado;
    } finally {
        flock($f, LOCK_UN);
        fclose($f);
    }
}