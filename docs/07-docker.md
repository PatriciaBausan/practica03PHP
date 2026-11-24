# Bloque 7. Preparar la segunda etapa de Docker

## Objetivo y encargo

Antes de guardar información, se amplía el entorno para separar el código público de los datos privados. También se comparan dos motores de bases de datos mediante conexiones PDO.

## Archivos y elementos trabajados

- Se conserva el Compose inicial en la raíz.
- Nueva carpeta `docker/` con `compose.yaml`, `Dockerfile`, `.env.example`, `apache/jukebox.conf`, `src/` y `data/`.
- Servicio web publicado en `127.0.0.1:8091`.
- Servicios `postgres:17-bookworm` y `mariadb:11.4`.
- `docker/src/11-conexiones.php`: diagnóstico de conexiones PDO.

## Desarrollo y explicación

La primera etapa permanece en el puerto 8090. La segunda etapa trabaja en el puerto 8091 con una copia del código.

El Dockerfile parte de `php:8.5-apache-bookworm` e instala los controladores `pdo_pgsql` y `pdo_mysql`. Los controladores permiten que PHP se conecte a los motores, pero no son los propios motores.

Apache se configura con `jukebox.conf`, `DocumentRoot /var/www/html` y un alias local. La carpeta `/var/www/data` queda fuera de la raíz pública. Compose levanta web, PostgreSQL y MariaDB. Los puertos internos 5432 y 3306 no se publican en Windows.

Se distinguen dos tipos de persistencia:

- Bind mounts: `./src` para el código y `./data` para los JSON.
- Volúmenes Docker: `pg_data` y `maria_data` para los datos propios de cada motor.

`depends_on` con `service_healthy` permite esperar a que las bases estén preparadas. No se fija `container_name`.

El archivo `11-conexiones.php` prueba una conexión PDO a PostgreSQL y otra a MariaDB. Consulta el nombre de cada base y devuelve un resultado JSON sin mostrar contraseñas.

Aunque se prueban ambos motores, las canciones y los usuarios de esta práctica se almacenarán en archivos JSON.

## Ejercicios realizados

1. Crear `docker/` y copiar el código de la primera etapa.
2. Crear Dockerfile, configuración Apache, `.env.example`, `.env`, `.gitignore`, `.dockerignore` y Compose.
3. Construir, arrancar y comprobar los servicios.
4. Probar las dos conexiones desde PHP.

## Comprobaciones

Ejecutar desde `docker/`:

```powershell
docker compose config --quiet
docker compose up -d --build
docker compose ps
docker compose logs --tail=50
```

Acceder a `http://localhost:8091/11-conexiones.php` y comprobar las respuestas. No incluir secretos en capturas ni en el repositorio.

