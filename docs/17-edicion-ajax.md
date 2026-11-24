# Bloque 17. Editar la canción elegida

## Objetivo y encargo

La asociación necesita corregir una ficha sin crear una canción duplicada ni modificar otra fila. La edición reutiliza el formulario de alta, pero conserva la identidad de la canción.

## Seleccionar la ficha

Al pulsar Editar, leer el id del botón y convertirlo a número. Comprobar que sea un entero positivo. Buscar la canción en la colección local por su id, nunca por la posición de la fila.

```javascript
const id = Number(boton.dataset.id);
const elegida = canciones.find(cancion => cancion.id === id);
```

Comprobar que `elegida` existe antes de rellenar el formulario. Cargar sus valores en los campos, conservar el id en una variable o campo oculto y cambiar el botón principal a «Guardar cambios».

Mostrar un aviso como «Editando: título de la canción» y un botón Cancelar de tipo `button`.

## Enviar la modificación

Al enviar el formulario en modo edición, utilizar:

`POST api/catalogo.php?action=update`

Enviar el id y todos los campos de la canción como JSON. Mantener las mismas validaciones, el token CSRF y el control de operación en curso que se utilizan en el alta.

## Actualizar en PHP

PHP valida el id y los campos. Dentro de la operación protegida por bloqueo exclusivo, busca la ficha actual por id, sustituye sus datos y conserva ese id.

Si la canción ya no existe, responder 404. No crearla de nuevo. Aunque el navegador tuviera la ficha al abrir el formulario, otra pestaña podría haberla eliminado; por eso el servidor debe comprobar su existencia justo al guardar.

## Cancelar y finalizar

Tras un éxito, dibujar la lista devuelta en `data` y volver al modo alta. Cancelar solo limpia el formulario y abandona el modo edición: no envía ninguna petición ni cambia el archivo.

## Comprobaciones

1. Editar el título y la duración de una canción.
2. Confirmar que su id y el número de fichas se mantienen.
3. Pulsar Cancelar y comprobar que no se envía ninguna petición.
4. Eliminar la ficha desde otra pestaña y tratar de guardar la edición: debe responder 404.
5. Comprobar que un id inválido no modifica el catálogo.


