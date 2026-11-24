# Bloque 14. Entender la nueva petición y acordar las operaciones

## Objetivo y encargo

La asociación quiere gestionar canciones sin sustituir el documento completo después de cada acción. Para ello, JavaScript enviará peticiones con `fetch` a una API PHP y recibirá respuestas JSON.

AJAX es el nombre habitual de esta comunicación asíncrona. En esta práctica se intercambia JSON, no XML.

## Recorrido de una petición

1. El usuario realiza una acción en la página.
2. JavaScript ejecuta `fetch`.
3. La API PHP comprueba sesión, método, token y datos.
4. PHP lee o modifica el archivo JSON privado.
5. PHP devuelve una respuesta JSON.
6. JavaScript actualiza la tabla sin recargar el documento.

PHP se ejecuta en el contenedor. JavaScript se ejecuta en el navegador. El navegador nunca abre directamente el archivo privado: solicita a PHP una operación permitida.

## Dos endpoints, dos responsabilidades

- `api/canciones.php` continúa devolviendo la lista que necesita el reproductor. No se cambia su formato: la home espera una lista con campos como `songs[i].src`.
- `api/catalogo.php` será la API de gestión. Devuelve un objeto con `ok` y, si la operación funciona, `data` con la lista actualizada. Si falla, devuelve `ok: false` y un mensaje en `error`.

Respuesta correcta de gestión:

```json
{
  "ok": true,
  "data": []
}
```

Respuesta de error:

```json
{
  "ok": false,
  "error": "Debes iniciar sesión."
}
```

Los comentarios explicativos no forman parte del JSON transmitido. Una respuesta JSON real no contiene comentarios ni HTML.

## Contrato de operaciones

| Acción | Método y URL | Cuerpo | Respuesta correcta |
|---|---|---|---|
| Consultar | `GET api/catalogo.php?action=list` | Ninguno | 200 y lista en `data` |
| Crear | `POST api/catalogo.php?action=create` | Los cinco campos de canción | 201 y lista actualizada |
| Editar | `POST api/catalogo.php?action=update` | `id` y los cinco campos | 200 y lista actualizada |
| Eliminar | `POST api/catalogo.php?action=delete` | `id` | 200 y lista actualizada |

Ninguna petición GET puede crear, editar ni eliminar canciones.

## Respuestas de error acordadas

| Estado | Significado |
|---:|---|
| 400 | JSON mal escrito o acción desconocida |
| 401 | Falta una sesión válida |
| 403 | Token CSRF ausente o incorrecto |
| 404 | La canción no existe |
| 405 | Método no permitido; incluir `Allow` |
| 415 | El cuerpo no se envía como `application/json` |
| 422 | Campos con valores no válidos |
| 500 | No se puede leer o guardar el catálogo |

## Decisiones de implementación

Preparar una función común de respuesta que establezca el estado HTTP, la cabecera `Content-Type: application/json; charset=utf-8`, codifique con `json_encode()` y termine la ejecución.

No imprimir espacios, avisos PHP, `var_dump()` ni HTML antes del JSON. Los detalles técnicos se registran en los logs, pero no se devuelven rutas privadas ni contraseñas al navegador.

Las dos APIs leen el mismo catálogo mediante las funciones compartidas. No se crean dos copias de los datos.

## Comprobaciones

Explicar qué respuesta corresponde a consultar sin sesión (401), editar un id inexistente (404) y enviar una duración negativa (422). Si la API devolviera una página HTML de login, `response.json()` no podría interpretar correctamente el cuerpo.
