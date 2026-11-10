<?php
require_once __DIR__ . '/lib/sesion.php';
require_once __DIR__ . '/lib/almacen-json.php';

$error = null;
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!is_string($email) || !is_string($password)) {
        $error = 'Credenciales no válidas';
    } else {
        $email = strtolower(trim($email)); 
        $oldEmail = $email;
        $user = null;
        try {
            foreach (leerColeccion(RUTA_USUARIOS) as $u) {
                if ($u['email'] === $email) {
                    $user = $u;
                    break;
                }
            }
        } catch (JsonException | RuntimeException $e) {
            error_log('Login: ' . $e->getMessage());
            http_response_code(500);
            $error = 'Servicio no disponible.';
        }

        if ($error === null) {
            if ($user !== null && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true); // renueva el identificador
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['alias'] = $user['alias'];
                header('Location: home.php', true, 303);
                exit;
            }
            $error = 'Credenciales no válidas'; 
        }
    }
}
?>
<!DOCTYPE html>
<html lang='es'>
<head><meta charset='utf-8'><title>Entrar</title></head>
<body>
<h1>Entrar</h1>
<?php if ($error !== null) { ?><p><?php echo e($error); ?></p><?php } ?>
<form method='post' action='login.php'>
  <?php echo campoCsrf(); ?>
  <p><label>Correo <input type='text' name='email' value='<?php echo e($oldEmail); ?>'></label></p>
  <p><label>Contraseña <input type='password' name='password'></label></p>
  <button type='submit'>Entrar</button>
</form>
<p><a href='register.php'>Crear cuenta</a></p>
</body>
</html>