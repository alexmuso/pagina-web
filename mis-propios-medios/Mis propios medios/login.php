<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();
$csrf = csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'Sesión inválida. Recarga la página e inténtalo de nuevo.';
    } else {
        $usuario = trim($_POST['usuario'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        $sql = 'SELECT * FROM admin WHERE usuario = :usuario LIMIT 1';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        $credenciales_validas = false;
        if ($admin) {
            $hashGuardado = $admin['contrasena'];

            if (password_verify($contrasena, $hashGuardado)) {
                $credenciales_validas = true;
            } elseif (hash_equals($hashGuardado, md5($contrasena))) {
                $credenciales_validas = true;
                $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
                $update = $conn->prepare('UPDATE admin SET contrasena = :contrasena WHERE id = :id');
                $update->bindParam(':contrasena', $nuevoHash);
                $update->bindParam(':id', $admin['id'], PDO::PARAM_INT);
                $update->execute();
            }
        }

        if ($credenciales_validas) {
            session_regenerate_id(true);
            $_SESSION['admin'] = $usuario;
            header('Location: admin.php');
            exit;
        }

        $error = 'Usuario o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión - Boutique</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <form method="POST" action="">
    <h2>🔐 Iniciar sesión</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?= e($error) ?></p>
    <?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Entrar</button>
    <a href="index.php">Volver al inicio</a>
  </form>
</body>
</html>
