# Bloque 12. Verificar y presentar el proyecto

## Objetivo y encargo

El cliente necesita saber qué funciona y cómo repetir las pruebas. Se comprueba la aplicación completa, se prepara una copia compartible y se organiza la presentación técnica.

## Verificación funcional

Realizar las pruebas con una cuenta ficticia y datos de demostración:

- Registro de un usuario nuevo y rechazo de un correo duplicado.
- Inicio y cierre de sesión.
- Acceso protegido a la home.
- Consulta del catálogo.
- Alta, edición y eliminación de canciones.
- Persistencia tras recargar el navegador y reiniciar el contenedor.
- Reproducción, pausa y cambio de canción.
- Rechazo de peticiones sin sesión o sin token CSRF.

## Revisión técnica

Comprobar que Docker Compose arranca correctamente, que Apache sirve PHP, que los datos permanecen en `/var/www/data` y que los JSON no son accesibles directamente por HTTP.

Explicar qué función cumplen Compose, Dockerfile, Apache, PHP y los archivos JSON. Distinguir el código público de los datos privados. Aclarar que PostgreSQL y MariaDB se probaron mediante PDO, pero que el CRUD de esta práctica utiliza JSON.

## Preparar una copia compartible

- Incluir el código fuente y los archivos de configuración necesarios.
- Incluir `.env.example`, pero no el `.env` real con credenciales.
- Excluir contraseñas, cookies, tokens y datos personales.
- Comprobar `.gitignore` y `.dockerignore`.
- Documentar los comandos para arrancar el proyecto y la URL de acceso.
- Conservar una copia de seguridad de los JSON antes de realizar pruebas destructivas.

## Presentación técnica

La demostración debe seguir una petición concreta desde el navegador hasta PHP y de vuelta. Identificar puerto, ruta, método, estado HTTP, `Content-Type` y cuerpo de respuesta.

Explicar cómo una canción pasa de JSON a array PHP y vuelve a guardarse; la diferencia entre id y posición; validación y escape; hash de contraseña y sesión; y la razón por la que los datos privados quedan fuera de `DocumentRoot`.

## Evidencias

Añadir capturas legibles con pies explicativos. Cada prueba debe indicar acción, resultado esperado y resultado observado. No presentar capturas como si fueran una ejecución en directo.

