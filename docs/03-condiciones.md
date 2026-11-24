# Bloque 3. Calcular y decidir sobre una canción

## Objetivo y encargo

El cliente necesita mostrar la duración de una canción de forma comprensible y clasificarla según su duración. Se practican operaciones aritméticas y estructuras condicionales.

## Archivos trabajados

- `src/04-duracion.php`: cálculo de minutos y segundos.
- `src/05-estado-cancion.php`: decisión del mensaje según la duración.

## Desarrollo y explicación

La duración se expresa en segundos. Con `intdiv($duration, 60)` se obtienen los minutos completos y con `%` se calcula el resto. Por ejemplo, 185 segundos equivalen a 3 minutos y 5 segundos.

La clasificación utiliza `if`, `elseif` y `else`:

- Duración igual a 0: «Sin duración».
- Duración entre 1 y 240 segundos: «Hasta cuatro minutos».
- Duración superior a 240 segundos: «Más de cuatro minutos».

El operador `=` asigna un valor. El operador `===` compara valor y tipo, por lo que `0` y `"0"` no son idénticos.

## Ejercicios realizados

1. Calcular minutos y segundos sin escribir el resultado manualmente.
2. Aplicar las tres ramas condicionales y mostrar un único estado.

## Pruebas

| Duración | Resultado esperado |
|---:|---|
| 0 | Sin duración |
| 1 | Hasta cuatro minutos |
| 239 | Hasta cuatro minutos |
| 240 | Hasta cuatro minutos |
| 241 | Más de cuatro minutos |

