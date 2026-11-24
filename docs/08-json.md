# Bloque 8. Leer el catálogo desde JSON

## Objetivo y encargo

El catálogo deja de estar escrito dentro de un archivo PHP y pasa a almacenarse en un archivo JSON privado, fuera de la carpeta pública.

## Archivos trabajados

- `docker/data/canciones.json`: lista persistente de canciones.
- `docker/src/lib/almacen-json.php`: funciones de lectura.
- `docker/src/12-catalogo-json.php`: lectura y presentación.

## Desarrollo y explicación

El archivo JSON contiene una lista de canciones. Las claves y cadenas utilizan comillas dobles, no se incluyen comentarios y no se deja una coma después del último elemento.

La aplicación lee desde la ruta fija `/var/www/data/canciones.json`; nunca utiliza una ruta proporcionada por el navegador. `file_get_contents()` obtiene el contenido como texto y `json_decode()` lo convierte en arrays PHP.

Se comprueba que la lectura no haya fallado y se controlan los errores de formato mediante `JSON_THROW_ON_ERROR` y `JsonException`.

Una lista vacía `[]` es válida y debe mostrar el mensaje correspondiente. Si el JSON está mal formado, se presenta un error controlado. No se debe sustituir el archivo dañado por una lista vacía, porque eso podría ocultar o destruir información.

La ruta privada no debe poder descargarse directamente por HTTP.

## Ejercicios realizados

1. Crear `canciones.json` con fichas que respeten las claves acordadas.
2. Añadir una comprobación de estructura de las canciones en `lib/canciones.php`.
3. Crear las funciones de lectura en `lib/almacen-json.php`.
4. Crear `12-catalogo-json.php` para presentar los datos.

## Comprobaciones y resultados

- JSON correcto con canciones: se muestran las fichas.
- JSON vacío `[]`: se muestra el estado vacío.
- JSON mal formado: se devuelve un error controlado y se conserva el archivo para poder repararlo.
- Acceso directo a `/var/www/data/canciones.json`: no debe ser posible mediante una URL pública.
