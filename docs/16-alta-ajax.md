# Bloque 16. Preparar un alta segura, recibir y validar JSON, enviar el alta y confirmar

## Objetivo y encargo

La asociación quiere añadir canciones desde la nueva pantalla sin recargar el documento. El navegador enviará un objeto JSON y PHP validará y guardará los datos antes de confirmar el alta.

## Preparar un alta segura

La página `canciones-ajax.php` incorpora un formulario con campos para título, artista, categoría, duración y audio. Cada campo debe tener una etiqueta visible. La página privada debe proporcionar el token CSRF a JavaScript, por ejemplo mediante una etiqueta `meta` cuyo nombre sea `csrf-token`.

La operación de alta se realizará mediante POST. El token se enviará en la cabecera `X-CSRF-Token`.

## Recibir y validar JSON en PHP

En `api/catalogo.php`, para las operaciones que modifican datos:

1. Comprobar que el método sea POST. Si no lo es, responder 405 e indicar el método permitido en `Allow`.
2. Comprobar que el tipo de contenido sea `application/json`; si no, responder 415.
3. Leer el cuerpo con `file_get_contents('php://input')`.
4. Decodificar con `json_decode()` y controlar `JsonException`. Un JSON mal escrito devuelve 400.
5. Comprobar el token CSRF con `hash_equals()`. Si no coincide, devolver 403.
6. Validar todos los campos en el servidor. Los valores no válidos devuelven 422 con un mensaje concreto.
7. Modificar el catálogo dentro del bloqueo exclusivo del almacén.

Para el alta se validan `title`, `artist`, `category`, `duration` y `src`. La duración debe ser un entero entre 1 y 1800. La categoría debe pertenecer a la lista permitida y `src` debe corresponder a un audio autorizado. El id lo calcula PHP: máximo id existente más uno, o 1 si el catálogo está vacío.

No confiar en los datos del navegador ni aceptar una ruta de audio arbitraria.

## Enviar el alta y confirmar

El formulario escucha el evento `submit` y utiliza `preventDefault()` para evitar el envío tradicional. JavaScript construye el objeto y convierte `duration` a número.

```javascript
const respuesta = await fetch('api/catalogo.php?action=create', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': token
    },
    body: JSON.stringify(nuevaCancion)
});

const contenido = await respuesta.json();
```

`JSON.stringify()` convierte el objeto en texto JSON. `Content-Type` informa a PHP de cómo interpretar el cuerpo. La respuesta se interpreta aparte con `respuesta.json()`.

Solo después de confirmar `respuesta.ok`, `contenido.ok` y que `contenido.data` sea una lista, se sustituye la colección local, se vuelve a dibujar la tabla y se limpia el formulario. Mostrar «Canción añadida» únicamente tras el éxito.

Si falla la operación, conservar los datos escritos para que puedan corregirse. Mantener una variable de operación en curso y deshabilitar temporalmente el envío y las demás acciones de modificación. Reactivar los controles en `finally`.

Una caída de red no demuestra que el servidor no haya guardado. Si se pierde la respuesta de un POST, no repetir automáticamente el alta: consultar la lista cuando vuelva la conexión antes de reenviar.

## Comprobaciones

- Alta válida: HTTP 201 y canción visible sin navegación completa.
- Recargar: la canción continúa guardada.
- Doble clic: la interfaz envía una sola operación.
- Campos inválidos: HTTP 422 y formulario conservado.
- Token incorrecto: HTTP 403.
- Cuerpo JSON mal formado: HTTP 400.

