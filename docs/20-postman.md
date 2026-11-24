# Apoyo. Probar la API con Postman

## Objetivo

Postman permite enviar peticiones de prueba manuales a la API. No comparte automáticamente la sesión del navegador: iniciar sesión en Chrome o Firefox no inicia sesión en Postman.

La pestaña Red/Network del navegador es suficiente para observar las peticiones reales de la interfaz. Postman es una herramienta complementaria.

## 1. Mantener el mismo host

Utilizar siempre:

`http://localhost:8091`

No alternar entre `localhost` y `127.0.0.1`, porque las cookies de sesión asociadas a esos hosts pueden ser distintas.

## 2. Iniciar sesión en Postman

1. Enviar GET a `http://localhost:8091/login.php`.
2. Conservar la cookie de sesión en el gestor de cookies de Postman.
3. Leer el token CSRF del formulario recibido.
4. Enviar el login como formulario, utilizando los nombres reales de los campos de la aplicación: `correo`, `contraseña` y el campo del token.
5. Permitir la redirección de acceso y conservar la cookie actualizada.
6. Si el login falla, corregirlo antes de probar el catálogo.

Usar siempre una cuenta ficticia. No guardar credenciales reales en una colección que se vaya a entregar.

## 3. Obtener el token de la pantalla AJAX

Solicitar `http://localhost:8091/canciones-ajax.php` con la misma sesión. Leer el token de la etiqueta `meta` llamada `csrf-token`.

No confundir el token CSRF con el identificador de la cookie de sesión: cumplen funciones diferentes.

## 4. Consultar el catálogo

Enviar:

- Método: GET
- URL: `http://localhost:8091/api/catalogo.php?action=list`

Resultado esperado: HTTP 200 y un JSON con `ok: true` y `data` como lista de canciones.

## 5. Crear una canción

Enviar POST a:

`http://localhost:8091/api/catalogo.php?action=create`

En Postman seleccionar Body → raw → JSON. Añadir la cabecera `X-CSRF-Token` con el token obtenido y mantener la cookie de sesión.

Ejemplo de cuerpo:

```json
{
  "title": "Tema de prueba",
  "artist": "Artista de prueba",
  "category": "electronica",
  "duration": 120,
  "src": "audio/demo-01.mp3"
}
```

Cambiar `src` por una ruta que esté autorizada en el catálogo. El id lo asigna PHP.

Resultado esperado: HTTP 201 y la lista actualizada.

## 6. Editar una canción

Enviar POST a:

`http://localhost:8091/api/catalogo.php?action=update`

Utilizar un id real obtenido de la consulta. En el cuerpo JSON enviar `id` y todos los campos de la canción. Mantener cookie y cabecera CSRF.

Resultado esperado: HTTP 200 y la lista actualizada. El id debe mantenerse.

## 7. Eliminar una canción

Enviar POST a:

`http://localhost:8091/api/catalogo.php?action=delete`

El cuerpo JSON solo necesita el id:

```json
{
  "id": 12
}
```

Sustituir 12 por un id real de pruebas. Mantener cookie y token. Resultado esperado: HTTP 200 y la lista sin esa ficha. El archivo MP3 no debe eliminarse.

## 8. Interpretar errores

| Estado | Qué revisar |
|---:|---|
| 401 | Sesión y cookie |
| 403 | Token CSRF |
| 404 | Id inexistente |
| 405 | Método HTTP |
| 415 | `Content-Type: application/json` |
| 422 | Valores y validación de campos |
| 500 | Lectura/escritura JSON y logs |

No desactivar la seguridad para conseguir que una petición pase. Para comprobar 422, mantener sesión y token válidos y enviar un dato incorrecto. Así se alcanza el control de validación y no se confunde con un error de acceso.

## Evidencias

![A1](/imagenes/61.png)\
![A1](/imagenes/62.png)\
![A1](/imagenes/63.png)\
![A1](/imagenes/64.png)\
![A1](/imagenes/65.png)\
![A1](/imagenes/66.png)\
![A1](/imagenes/67.png)\
![A1](/imagenes/68.png)\
![A1](/imagenes/69.png)\
![A1](/imagenes/70.png)\
![A1](/imagenes/71.png)\
![A1](/imagenes/72.png)\
![A1](/imagenes/73.png)\
![A1](/imagenes/74.png)\
![A1](/imagenes/78.png)\
![A1](/imagenes/79.png)\
![A1](/imagenes/80.png)\
![A1](/imagenes/81.png)\
![A1](/imagenes/82.png)\
![A1](/imagenes/83.png)\
![A1](/imagenes/84.png)\
![A1](/imagenes/85.png)\
![A1](/imagenes/86.png)\
![A1](/imagenes/87.png)