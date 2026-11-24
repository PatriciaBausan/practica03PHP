# Bloque 9. Registro, sesiones y autenticación

## Objetivo y encargo

La aplicación debe permitir crear una cuenta ficticia, iniciar sesión y acceder a una home privada. La identificación se conserva mediante sesiones del servidor.

## Archivos trabajados

- `docker/src/lib/sesion.php`
- `docker/src/register.php`
- `docker/src/login.php`
- `docker/src/home.php`
- `docker/src/logout.php`
- `docker/src/lib/acceso.php`
- `docker/data/usuarios.json`
- `docker/src/lib/almacen-json.php`

## Desarrollo y explicación

La sesión se inicia antes de generar HTML. La cookie se configura con `httponly` y `samesite=Lax`. La contraseña no se guarda en la sesión ni en JavaScript.

Los formularios incluyen un token CSRF asociado a la sesión. En cada POST se comprueba mediante `hash_equals()`. Si falta o no coincide, la petición se rechaza con HTTP 403.

El registro valida alias, correo y contraseña. El correo se normaliza y se comprueba que no esté duplicado. La contraseña se almacena mediante `password_hash()`, nunca en texto plano. La confirmación de contraseña no se guarda.

La escritura del JSON se realiza de forma controlada: se abre el archivo, se obtiene un bloqueo exclusivo, se leen y validan los datos, se modifica la colección y se escribe el resultado completo. El bloqueo se libera y el archivo se cierra incluso si ocurre un error.

El login busca el usuario por correo y verifica la contraseña con `password_verify()`. Si las credenciales son correctas, se renueva el identificador de sesión y se guardan el id y alias del usuario.

Las páginas privadas comprueban que exista `usuario_id`. Si no hay sesión, redirigen al login. El logout acepta solo POST con token válido, vacía la sesión y elimina su cookie.

## Ejercicios realizados

1. Crear `lib/sesion.php` y cargarlo antes de cualquier HTML.
2. Probar el token CSRF con una página temporal y comprobar que su ausencia produce 403.
3. Preparar `usuarios.json` y añadir escritura segura al almacén.
4. Crear `register.php`, validar y guardar solo el hash de contraseña.
5. Crear `login.php` y verificar credenciales.
6. Proteger `home.php` y crear `logout.php`.

## Comprobaciones

- Registro válido y correo duplicado.
- Contraseña incorrecta y login correcto.
- Acceso a home sin sesión.
- Logout mediante POST con token.
- POST sin token: HTTP 403.


