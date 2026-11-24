# Bloque 19. Integrar, mejorar la respuesta y probar también los fallos

## Objetivo y encargo

Las cuatro operaciones ya existen. Ahora deben formar una pantalla coherente y convivir con el jukebox y el CRUD clásico. También hay que demostrar que la API rechaza peticiones incorrectas.

## Integración y mejora de la respuesta

Añadir enlaces claros entre la home, la gestión clásica y la gestión AJAX. Las tres pantallas deben requerir sesión. Cerrar sesión debe impedir nuevas operaciones en la API.

Revisar el uso con teclado. Todos los campos deben tener etiqueta y los botones deben indicar claramente su acción. Al iniciar una edición, llevar el foco al primer campo. Al cancelar, permitir continuar sin perder la orientación.

Mantener mensajes diferenciados:

- «Cargando catálogo…»
- «Guardando…»
- «Cambios guardados»
- Mensajes de error concretos.

No mostrar éxito desde `finally`: ese bloque se ejecuta tanto si la operación funciona como si falla.

Cuando las cuatro operaciones estén comprendidas, extraer el envío y la comprobación comunes a una función reutilizable. Mantener pequeñas las funciones de carga, representación, alta, edición y borrado.

## Mantener el contrato del reproductor

La home continúa consultando `api/canciones.php` y recibe una lista, no el objeto `ok/data` de la API de gestión. Ambos endpoints leen el mismo catálogo.

Una modificación confirmada desde AJAX aparecerá cuando la home vuelva a consultar los datos. No se exige que un reproductor ya abierto se sincronice automáticamente: basta con volver a la home o recargarla.

Comprobar la coherencia entre las tres pantallas:

1. Crear una ficha desde AJAX y verla en el CRUD clásico.
2. Editarla desde el CRUD clásico y volver a consultarla desde AJAX.
3. Abrir la home y reproducir su audio autorizado.
4. Eliminarla y comprobar que desaparece de las consultas posteriores.

## Probar también los fallos

Realizar las pruebas con datos ficticios. Para operaciones destructivas, preparar una copia de pruebas y un respaldo fuera de `src`. No dañar el catálogo real ni eliminar volúmenes para provocar errores.

| Área | Caso | Resultado esperado |
|---|---|---|
| Acceso | Consulta sin sesión | 401 JSON |
| Acceso | POST con sesión y sin token | 403 |
| Acceso | Sesión cerrada en otra pestaña | Las nuevas modificaciones no se autorizan |
| Contrato | GET para create | 405 |
| Contrato | Tipo de cuerpo incorrecto | 415 |
| Contrato | JSON mal escrito | 400 |
| Validación | Campos inválidos | 422 |
| Identidad | Id inexistente | 404 |
| Interfaz | Doble clic | Una sola operación desde la pantalla |
| Interfaz | Error de validación | Se conservan los datos |
| Interfaz | Catálogo vacío | Mensaje visible |
| Interfaz | Cancelar edición o borrado | Ningún cambio |
| Persistencia | Recargar tras un cambio | Datos conservados |
| Almacén | JSON dañado en copia de pruebas | Error; no se sustituye por `[]` |
| Red | Navegador en Offline | Error comprensible |

Para probar la red, activar temporalmente Offline en las herramientas del navegador durante una consulta. Volver a Online y repetir.

## Observar las peticiones

En Red/Network filtrar por Fetch/XHR. Revisar URL, método, estado, cuerpo enviado y respuesta. Para demostrar que no hay recarga, distinguir las peticiones Fetch/XHR de una nueva navegación de tipo Document.

Comandos de diagnóstico desde la carpeta `docker`:

```powershell
docker compose ps
docker compose logs --tail=50
```

Si aparece un 500 o HTML inesperado, revisar los logs. No copiar el registro entero al informe.

## Resultado

La aplicación queda integrada cuando las pantallas AJAX, clásica y el reproductor muestran los cambios de un único catálogo, y cuando las operaciones correctas y los fallos producen respuestas coherentes.
