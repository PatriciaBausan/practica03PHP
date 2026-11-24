# Bloque 13. Preparar la ampliación AJAX

## Objetivo y encargo

La aplicación ya permite registrarse, iniciar sesión, acceder a una home privada y gestionar un catálogo JSON. La asociación quiere mejorar la gestión: al añadir, editar o eliminar una canción, la tabla debe actualizarse sin recargar toda la página.

Esta ampliación no crea otra aplicación. El registro, el login, el reproductor y el CRUD clásico deben seguir funcionando.

## Preparación del entorno

Antes de modificar nada, guardar un punto de recuperación: un commit de Git o una copia fechada fuera de la carpeta pública. Comprobar que se puede iniciar sesión, consultar el catálogo y reproducir un audio.

La segunda etapa continúa dentro de:

`C:\server\asociacion-jukebox-php\docker`

No se modifica la primera etapa del puerto 8090.

```powershell
cd C:\server\asociacion-jukebox-php\docker
docker compose up -d
docker compose ps
```

Abrir `http://localhost:8091`. Mantener el mismo nombre de host durante las pruebas: `localhost` y `127.0.0.1` no comparten necesariamente las cookies de sesión. No utilizar Live Server para ejecutar PHP.

## Nuevos archivos

```text
docker/
  src/
    canciones-ajax.php
    api/
      catalogo.php
    scripts/
      catalogo-ajax.js
    lib/
  data/
    canciones.json
    usuarios.json
docs/
  13-preparacion-ajax.md
```

Los datos siguen en `/var/www/data`, fuera de `/var/www/html`. No se copia `canciones.json` a `src`. PostgreSQL y MariaDB pueden continuar en Compose, pero esta ampliación mantiene JSON como almacenamiento.

## Comprobaciones

Abrir el CRUD clásico y la home en el puerto 8091 antes de comenzar. Ambos deben funcionar. Revisar el estado de los servicios y guardar una copia de seguridad.

