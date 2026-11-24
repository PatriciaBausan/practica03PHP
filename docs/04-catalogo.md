# Bloque 4. Construir el catálogo musical

## Objetivo y encargo

La asociación necesita pasar de una canción a varias fichas sin duplicar el código HTML. Se introducen los bucles y los arrays.

## Archivos trabajados

- `src/06-listas.php`: recorridos con `for`, `while` y `foreach`.
- `src/07-catalogo.php`: colección de canciones mediante arrays asociativos.

## Desarrollo y explicación

Primero se generan las posiciones del 1 al 3 con `for` y se repite el recorrido con `while`. Se identifica el contador, la condición y el momento en que deja de cumplirse.

Después se crea un array de títulos y se recorre con `foreach`. Se recuerda que el primer índice interno es 0 y que no tiene por qué coincidir con el número que se muestra al usuario.

El catálogo se representa mediante arrays asociativos. Cada canción contiene las claves:

- `id`: identificador estable.
- `title`: título.
- `artist`: artista.
- `category`: categoría musical.
- `duration`: duración en segundos.
- `src`: ruta relativa del audio.

La presentación recorre la colección y genera las fichas sin repetir manualmente el HTML. Si el catálogo está vacío, se muestra «Todavía no hay canciones».

## Ejercicios realizados

1. Crear listas con `for`, `while` y `foreach`.
2. Construir varias fichas con arrays asociativos y recorrerlas con `foreach`.



