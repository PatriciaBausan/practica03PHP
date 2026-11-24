# Bloque 1. Levantar el primer servicio

## Objetivo y encargo

La asociación necesita comprobar que su futura aplicación puede ejecutar PHP. En este primer bloque se prepara un entorno mínimo con Docker Compose, Apache y PHP. Todavía no existen usuarios ni un catálogo persistente.

## Archivos y elementos trabajados

- Carpeta raíz: `C:\server\asociacion-jukebox-php`.
- `compose.yaml`: define el servicio web, la imagen `php:8.5-apache-bookworm`, el puerto 8090 y el montaje del código.
- `src/01-bienvenida.php`: muestra el primer saludo.
- `docs/`: contiene los resúmenes de cada bloque.

## Desarrollo y explicación

Se crea la estructura inicial del proyecto y un archivo PHP que utiliza `echo` para mostrar un saludo que identifica el jukebox de la asociación.

Docker Compose monta la carpeta local `src` en `/var/www/html` dentro del contenedor. Apache sirve esa carpeta y ejecuta los archivos PHP. El navegador accede a través de `http://localhost:8090/01-bienvenida.php`.

Comandos utilizados desde PowerShell:

```powershell
cd C:\server\asociacion-jukebox-php
docker compose config --quiet
docker compose up -d
docker compose ps
curl.exe -i http://localhost:8090/01-bienvenida.php
```

La sintaxis se comprueba dentro del contenedor:

```powershell
docker compose exec web php -l /var/www/html/01-bienvenida.php
```

## Ejercicios realizados

1. Preparar la carpeta raíz, `src/`, `docs/` y el archivo de bienvenida.
2. Completar `compose.yaml` con el montaje `./src:/var/www/html` y publicar `127.0.0.1:8090:80`.
3. Comparar un 404 con un fallo de conexión: el 404 indica que Apache responde pero la ruta no existe; el fallo de conexión indica que el servicio no está disponible.
4. Provocar temporalmente un error de sintaxis, detectarlo con `php -l` y corregirlo.

## Comprobaciones y resultados

- Resultado correcto esperado: HTTP 200 y el saludo.
- Ruta inexistente: HTTP 404 con el servicio activo.
- Servicio detenido: el navegador o `curl` no consigue establecer conexión.
- Sintaxis correcta: `No syntax errors detected`.
