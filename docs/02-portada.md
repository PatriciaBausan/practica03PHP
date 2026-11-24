# Bloque 2. Describir la primera canción

## Objetivo y encargo

La asociación quiere presentar el jukebox y destacar una canción. Se comienza a generar HTML desde PHP y se utilizan variables, tipos de datos, constantes y concatenación.

## Archivos y elementos trabajados

- `src/02-portada.php`: portada HTML completa.
- `src/03-cancion.php`: datos de una canción ficticia.
- Variables: `$title`, `$artist`, `$duration`, `$volume` y `$available`.
- Constante: `APP_NAME`.

## Desarrollo y explicación

La portada contiene una estructura HTML completa, con `lang="es"`, codificación UTF-8, encabezado, párrafo generado mediante PHP y un enlace al saludo del bloque anterior. Se explica que el catálogo está en preparación.

Los datos de canción se guardan en variables de distintos tipos: `string`, `int`, `float` y `bool`. La constante `APP_NAME` identifica el nombre de la aplicación y no se reasigna durante la ejecución.

Se utiliza temporalmente `var_dump()` para observar los tipos y comparar, por ejemplo, el entero `185` con el texto `"185"`. Esta comprobación se elimina al terminar.

Al insertar datos en HTML se utiliza:

```php
htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
```

Esto evita que un valor introducido como texto se interprete como marcado HTML.

## Ejercicios realizados

1. Crear `02-portada.php` con HTML válido y contenido dinámico.
2. Crear `03-cancion.php` con los datos ficticios de una canción.
3. Probar un título como `<b>Demo</b>` y comprobar que se muestra literalmente, sin convertirse en negrita.

