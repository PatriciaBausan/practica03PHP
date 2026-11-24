# Bloque 18. Eliminar sin confundir fichas

## Objetivo y encargo

La asociación necesita completar el CRUD asíncrono eliminando una ficha concreta. La interfaz debe pedir confirmación y el servidor debe modificar el JSON solo cuando la petición sea válida.

## Confirmar antes de eliminar

Al pulsar Eliminar, obtener el id de la canción desde `dataset.id` y localizar el título correspondiente en la colección local. Mostrar una confirmación que identifique la canción, por ejemplo con `window.confirm()`.

Si el usuario cancela, no se envía ninguna petición y el catálogo no cambia.

## Enviar la operación

Enviar un POST a `api/catalogo.php?action=delete`, con el token CSRF en `X-CSRF-Token` y un cuerpo JSON que contenga el id:

```javascript
const datos = { id: id };
```

El cuerpo se convierte mediante `JSON.stringify(datos)`. Mientras se espera la respuesta, deshabilitar temporalmente las acciones para evitar operaciones simultáneas.

## Eliminar en PHP

PHP comprueba sesión, método, tipo de contenido, token y formato del cuerpo. El id debe ser un entero positivo.

Dentro de la operación de lectura, modificación y escritura bloqueada:

1. Buscar la canción por su id.
2. Si existe, eliminarla.
3. Reordenar las posiciones con `array_values()`.
4. Guardar el catálogo.
5. Responder 200 y la lista actualizada.

Si el id no existe, responder 404. No utilizar `unlink()` para borrar el archivo de audio: eliminar una ficha no significa borrar el MP3.

Los ids restantes no se renumeran. Si el catálogo contiene ids 2, 5 y 9 y se elimina el 5, deben quedar los ids 2 y 9. La segunda fila pasa a representar la ficha 9, pero su id sigue siendo 9.

## Actualizar la interfaz

Solo después de recibir una respuesta correcta, sustituir la lista local por `data` y volver a dibujar la tabla. Si se estaba editando esa canción, abandonar el modo edición. Si no quedan canciones, mostrar el estado vacío.

## Comprobaciones

- Cancelar la confirmación: no se envía petición.
- Eliminar una canción intermedia: desaparece la ficha elegida.
- Editar después la fila que quedó debajo: debe modificarse la canción correcta.
- Eliminar la última ficha: aparece el mensaje de catálogo vacío.
- Id inexistente: HTTP 404 y catálogo sin cambios.
- El audio permanece en el disco.


