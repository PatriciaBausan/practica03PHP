<?php
require_once __DIR__ . '/lib/sesion.php';
require_once __DIR__ . '/lib/almacen-json.php';

$errors = [];
$old = ['alias' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $in = [];
    foreach (['alias', 'email', 'password', 'password2'] as $campo) {
        $v = $_POST[$campo] ?? '';
        if (!is_string($v)) {
            $errors[$campo] = 'Dato no válido.';
            $v = '';
        }
        $in[$campo] = $v;
    }

    $alias = trim($in['alias']);
    $email = strtolower(trim($in['email']));
    $password = $in['password']; 
    $old = ['alias' => $alias, 'email' => $email];

    if (!isset($errors['alias']) && (mb_strlen($alias) < 2 || mb_strlen($alias) > 30)) {
        $errors['alias'] = 'El alias debe tener entre 2 y 30 caracteres.';
    }
    if (!isset($errors['email']) && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'El correo no tiene un formato válido.';
    }
    if (!isset($errors['password'])) {
        if (mb_strlen($password) < 12 || mb_strlen($password) > 64) {
            $errors['password'] = 'La contraseña debe tener entre 12 y 64 caracteres.';
        } elseif (strlen($password) > 72) {
            $errors['password'] = 'La contraseña es demasiado larga.'; // límite de bcrypt
        }
    }
    if (!isset($errors['password2']) && $in['password2'] !== $password) {
        $errors['password2'] = 'Las contraseñas no coinciden.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            modificarColeccion(RUTA_USUARIOS, function (array $users) use ($alias, $email, $hash): array {
                $maxId = 0;
                foreach ($users as $u) {
                    if ($u['email'] === $email) {
                        throw new ErrorDeNegocio('Correo duplicado.');
                    }
                    if ($u['id'] > $maxId) {
                        $maxId = $u['id'];
                    }
                }
                $users[] = [
                    'id' => $maxId + 1, 
                    'alias' => $alias,
                    'email' => $email,
                    'password_hash' => $hash, 
                ];
                return [$users, null];
            });
            header('Location: login.php', true, 303);
            exit;
        } catch (ErrorDeNegocio $e) {
            $errors['email'] = 'Ese correo ya está registrado.';
        } catch (JsonException | RuntimeException $e) {
            error_log('Registro: ' . $e->getMessage());
            $errors['general'] = 'No se pudo completar el registro. Inténtalo más tarde.';
        }
    }

    if ($errors) {
        http_response_code(isset($errors['general']) ? 500 : 400);
    }
}

function err(array $errors, string $campo): string
{
    return isset($errors[$campo]) ? '<span>' . e($errors[$campo]) . '</span>' : '';
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Registro</title></head>
<body>
<h1>Crear cuenta de prueba</h1>
<?php echo err($errors, 'general'); ?>
<form method='post' action='register.php'>
  <?php echo campoCsrf(); ?>
  <p><label>Alias <input type='text' name='alias' value='<?php echo e($old['alias']); ?>'></label><?php echo err($errors, 'alias'); ?></p>
  <p><label>Correo <input type='text' name='email' value='<?php echo e($old['email']); ?>'></label><?php echo err($errors, 'email'); ?></p>
  <p><label>Contraseña <input type='password' name='password'></label><?php echo err($errors, 'password'); ?></p>
  <p><label>Repite la contraseña <input type='password' name='password2'></label><?php echo err($errors, 'password2'); ?></p>
  <button type='submit'>Registrarme</button>
</form>
<p><a href='login.php'>Ya tengo cuenta</a></p>
</body>
</html>