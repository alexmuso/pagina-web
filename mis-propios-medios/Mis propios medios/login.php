<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();
$csrf = csrf_token();

/**
 * Devuelve la configuración de autenticación detectada en la BD o null si no hay una válida.
 */
function detect_auth_source($conn)
{
    $candidates = [
        ['table' => 'admin', 'user_col' => 'usuario', 'pass_col' => 'contrasena', 'id_col' => 'id', 'where' => ''],
        ['table' => 'admin', 'user_col' => 'usuario', 'pass_col' => 'clave', 'id_col' => 'id', 'where' => ''],
        ['table' => 'usuarios', 'user_col' => 'usuario', 'pass_col' => 'clave', 'id_col' => 'id', 'where' => " AND rol = 'admin'"],
        ['table' => 'usuarios', 'user_col' => 'usuario', 'pass_col' => 'clave', 'id_col' => 'id', 'where' => ''],
    ];

    foreach ($candidates as $source) {
        $probeSql = sprintf(
            'SELECT %s, %s FROM %s WHERE 1=0 LIMIT 1',
            $source['user_col'],
            $source['pass_col'],
            $source['table']
        );

        try {
            $conn->query($probeSql);
            return $source;
        } catch (PDOException $e) {
            // Intentar siguiente esquema.
        }
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
        http_response_code(403);
        $error = 'Sesión inválida. Recarga la página e inténtalo de nuevo.';
    } else {
        $usuario = trim(isset($_POST['usuario']) ? $_POST['usuario'] : '');
        $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

        try {
            $source = detect_auth_source($conn);

            if ($source === null) {
                $error = 'No se encontró una tabla de usuarios administradores. Verifica la base de datos.';
            } else {
                $sql = sprintf(
                    'SELECT * FROM %s WHERE %s = :usuario%s LIMIT 1',
                    $source['table'],
                    $source['user_col'],
                    $source['where']
                );

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':usuario', $usuario);
                $stmt->execute();

                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                $credenciales_validas = false;

                if ($admin) {
                    $hashGuardado = isset($admin[$source['pass_col']]) ? (string) $admin[$source['pass_col']] : '';

                    if ($hashGuardado !== '' && password_verify($contrasena, $hashGuardado)) {
                        $credenciales_validas = true;
                    } elseif ($hashGuardado !== '' && hash_equals($hashGuardado, md5($contrasena))) {
                        $credenciales_validas = true;
                    } elseif ($hashGuardado !== '' && hash_equals($hashGuardado, $contrasena)) {
                        $credenciales_validas = true;
                    }

                    if ($credenciales_validas) {
                        $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
                        $updateSql = sprintf(
                            'UPDATE %s SET %s = :contrasena WHERE %s = :id',
                            $source['table'],
                            $source['pass_col'],
                            $source['id_col']
                        );
                        $update = $conn->prepare($updateSql);
                        $update->bindParam(':contrasena', $nuevoHash);
                        $update->bindParam(':id', $admin[$source['id_col']], PDO::PARAM_INT);
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
        } catch (PDOException $e) {
            $error = 'Error al consultar credenciales. Verifica que la base de datos esté importada correctamente.';
        }
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
