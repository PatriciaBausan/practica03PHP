# Bloque 11. Integrar el jukebox

## Objetivo y encargo

Se conecta el reproductor desarrollado en la asignatura de entorno cliente con el catálogo que proporciona PHP. El reproductor debe utilizar las canciones guardadas en el servidor.

## Archivos trabajados

- `docker/src/home.php`: incorpora el reproductor en la home privada.
- `docker/src/api/canciones.php`: endpoint que devuelve el catálogo en JSON.
- `docker/src/scripts/` y `docker/src/assets/`: JavaScript y estilos.
- `docker/src/audio/`: audios autorizados.
- Howler.js: gestión de reproducción.

## Desarrollo y explicación

Se copian los archivos del reproductor y se conservan sus identificadores y clases para que el JavaScript existente siga funcionando.

El endpoint `api/canciones.php` inicia la sesión, comprueba que exista `usuario_id` y devuelve una lista JSON. Si no hay sesión, responde con HTTP 401 y un error JSON, sin redirigir a una página HTML.

El JavaScript cambia la fuente de datos y utiliza `fetch('./api/canciones.php')` en lugar de leer un `songsData.json` independiente. Comprueba `response.ok` y que la respuesta sea una lista válida.

Los campos `title` y `artist` se utilizan para los textos de la interfaz y `src` para la ruta del audio. Para insertar texto recibido del servidor se utiliza `textContent`, no `innerHTML`.

Howler.js crea la reproducción. Se comprueba que el usuario pueda reproducir, pausar y cambiar de canción. El audio debe iniciarse mediante una acción del usuario si el navegador bloquea la reproducción automática.

Al crear, editar o eliminar una canción, al recargar la home el reproductor debe reflejar el catálogo actualizado. Si no quedan canciones, se muestra un estado vacío sin errores.

## Ejercicios realizados

1. Colocar los archivos del cliente en la estructura de `docker/src/`.
2. Autorizar las rutas de audio en la validación de canciones.
3. Crear fichas desde el formulario utilizando las rutas permitidas.
4. Crear el endpoint `api/canciones.php`.
5. Cambiar la fuente de datos de `scripts/app.js`.
6. Realizar una prueba completa de alta, reproducción, edición y eliminación.

## Comprobaciones y resultados

Verificar que el endpoint devuelve una lista, que sin sesión devuelve 401, que los audios responden correctamente y que el reproductor mantiene reproducción, pausa y cambio de canción.

