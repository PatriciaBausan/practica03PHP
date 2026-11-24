# Bloque 5. Separar funciones y datos

## Objetivo y encargo

El equipo necesita reutilizar el cálculo de duración en distintas páginas. Se crea una función común y se separa la lógica de los datos del catálogo.

## Archivos trabajados

- `src/08-funciones.php`: prueba de la función.
- `src/lib/canciones.php`: definición común de `duracionLegible`.
- `src/lib/catalogo-inicial.php`: datos iniciales.
- `src/07-catalogo.php`: carga de biblioteca y datos.

## Desarrollo y explicación

Se define `duracionLegible(int $total): string`. Recibe una duración entera y devuelve un texto con minutos y segundos mediante `return`. La función no imprime HTML: calcula y devuelve un valor para que cada página decida cómo presentarlo.

La función se prueba con 60 y 185 segundos. Los resultados esperados son «1 minuto y 0 segundos» y «3 minutos y 5 segundos».

La definición se mueve a `src/lib/canciones.php` y se carga con `require_once`, que evita incluir repetidamente el mismo archivo durante una petición.

El catálogo se traslada a `catalogo-inicial.php`, que devuelve la colección mediante `return`. Así se separan las funciones reutilizables de los datos.

## Ejercicios realizados

1. Crear y probar `duracionLegible`.
2. Mover la función a un archivo común y cargarla con `require_once`.
3. Mover el array de canciones a `catalogo-inicial.php` y devolverlo con `return`.

## Comprobaciones y resultados

Probar los casos de 60 y 185 segundos. Cambiar temporalmente una ruta de `require_once` permite observar el error y revisar los registros del contenedor. Restaurar después la ruta correcta.

