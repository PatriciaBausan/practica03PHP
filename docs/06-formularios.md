# Bloque 6. Recibir peticiones y validar formularios

## Objetivo y encargo

La asociación quiere filtrar el catálogo por categoría y revisar una ficha antes de guardarla. Se utilizan los métodos GET y POST y se validan los datos recibidos.

## Archivos trabajados

- `src/09-filtro.php`: lectura de `category` mediante `$_GET`.
- `src/10-formulario.php`: formulario POST y vista previa.
- Se reutiliza `duracionLegible()`.

## Desarrollo y explicación

En el filtro se recibe la categoría mediante `$_GET`. Primero se comprueba que sea una cadena y después que pertenezca a la lista permitida: `todas`, `electronica`, `rock` o `bandas-sonoras`.

Si la categoría no es válida o llega como array, se responde con HTTP 400 y «Categoría no válida». Una categoría permitida sin canciones devuelve HTTP 200 y un mensaje de lista vacía.

El formulario utiliza `method="post"` y los campos `title`, `artist`, `category`, `duration` y `src`. El atributo `name` define la clave que PHP recibe en `$_POST`.

La validación del servidor comprueba que título y artista no estén vacíos, que la categoría esté permitida y que la duración sea un entero entre 1 y 1800. La ruta `src` se selecciona de una lista cerrada de audios.

Si hay errores, se muestran mensajes y se conservan los valores válidos escapados. Si todo es correcto, se presenta una vista previa indicando que la canción todavía no se ha guardado.

## Ejercicios y pruebas

```powershell
curl.exe -i http://localhost:8090/09-filtro.php
curl.exe -i "http://localhost:8090/09-filtro.php?category=rock"
curl.exe -i "http://localhost:8090/09-filtro.php?category=desconocida"
curl.exe -g -i "http://localhost:8090/09-filtro.php?category[]=rock"
```

Comprobar también una categoría permitida que no tenga canciones. Para probarla, utilizar datos de prueba y restaurar después el catálogo.

