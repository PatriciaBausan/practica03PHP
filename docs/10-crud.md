# Bloque 10. CRUD de canciones

## Objetivo y encargo

La asociación necesita administrar el catálogo desde la web. Se implementan las operaciones de crear, consultar, editar y eliminar canciones sobre el mismo archivo JSON.

## Archivos trabajados

- `docker/src/canciones.php`: consulta.
- `docker/src/cancion-nueva.php`: alta.
- `docker/src/cancion-editar.php`: edición.
- `docker/src/cancion-eliminar.php`: confirmación y borrado.
- `docker/src/lib/almacen-json.php`: actualización protegida.
- `docker/data/canciones.json`: catálogo persistente.
- `docker/src/lib/validacion-cancion.php` y `formulario-cancion.php`: piezas comunes.

## Desarrollo y explicación

Todas las páginas del CRUD requieren una sesión autenticada. Los cambios se realizan mediante POST y token CSRF válido.

- **Read:** muestra las fichas y enlaces para editar.
- **Create:** valida los campos y calcula el nuevo id en el servidor: máximo id existente más uno, o 1 si el catálogo está vacío.
- **Update:** recibe el id por GET para cargar el formulario, pero nunca guarda mediante GET. En POST vuelve a comprobar sesión, token, id y campos. Busca de nuevo la canción bajo bloqueo y actualiza sus datos conservando el id.
- **Delete:** puede mostrar confirmación mediante GET, pero el borrado se realiza únicamente por POST. Localiza el id bajo bloqueo, elimina el elemento y reindexa con `array_values()`.

Eliminar una ficha no elimina el archivo MP3. Si el id no existe, se devuelve 404 sin modificar el catálogo. Los métodos no permitidos se rechazan.

El identificador `id` es estable y no equivale a la posición que ocupa la canción dentro del array.

## Ejercicios realizados

1. Añadir funciones comunes y validación de canciones.
2. Crear `canciones.php` para consultar.
3. Crear `cancion-nueva.php` para añadir.
4. Crear `cancion-editar.php` para modificar por id.
5. Crear `cancion-eliminar.php` para confirmar y eliminar por id.

## Comprobaciones

Probar alta válida, datos inválidos, edición que conserve el id, cancelación, eliminación de una ficha intermedia y eliminación de un id inexistente. Comprobar que los MP3 permanecen en disco.


