<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();
$csrf = csrf_token();

/**
 * Devuelve la configuración de autenticación detectada para administradores o usuarios.
 */
function detect_auth_source($conn, $tipo)
{
    if ($tipo === 'usuario') {
        $candidates = [
            ['table' => 'usuarios', 'user_col' => 'usuario', 'pass_col' => 'clave', 'id_col' => 'id', 'where' => " AND rol = 'cliente'"],
            ['table' => 'usuarios', 'user_col' => 'usuario', 'pass_col' => 'clave', 'id_col' => 'id', 'where' => ''],
        ];
    } else {
        $candidates = [
            ['table' => 'admin', 'user_col' => 'usuario', 'pass_col' => 'contrasena', 'id_col' => 'id', 'where' => ''],
            ['table' => 'admin', 'user_col' => 'usuario', 'pass_col' => 'clave', 'id_col' => 'id', 'where' => ''],
            ['table' => 'usuarios', 'user_col' => 'usuario', 'pass_col' => 'clave', 'id_col' => 'id', 'where' => " AND rol = 'admin'"],
        ];
    }

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

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'admin';
if ($tipo !== 'admin' && $tipo !== 'usuario') {
    $tipo = 'admin';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
        http_response_code(403);
        $error = 'Sesión inválida. Recarga la página e inténtalo de nuevo.';
    } else {
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'admin';
        if ($tipo !== 'admin' && $tipo !== 'usuario') {
            $tipo = 'admin';
        }

        $usuario = trim(isset($_POST['usuario']) ? $_POST['usuario'] : '');
        $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

        try {
            $source = detect_auth_source($conn, $tipo);

            if ($source === null) {
                $error = $tipo === 'admin'
                    ? 'No se encontró una tabla de usuarios administradores. Verifica la base de datos.'
                    : 'No se encontró una tabla de usuarios clientes. Verifica la base de datos.';
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

                $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
                $credenciales_validas = false;

                if ($cuenta) {
                    $hashGuardado = isset($cuenta[$source['pass_col']]) ? (string) $cuenta[$source['pass_col']] : '';

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
                        $update->bindParam(':id', $cuenta[$source['id_col']], PDO::PARAM_INT);
                        $update->execute();
                    }
                }

                if ($credenciales_validas) {
                    session_regenerate_id(true);

                    if ($tipo === 'admin') {
                        $_SESSION['admin'] = $usuario;
                        unset($_SESSION['usuario']);
                        header('Location: admin.php');
                    } else {
                        $_SESSION['usuario'] = $usuario;
                        unset($_SESSION['admin']);
                        header('Location: catalogo_accesorios.php');
                    }

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
  <title>Iniciar sesión - Boutique</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <header class="site-header">
    <img src="img/logo.jpeg" alt="Logo Boutique">
    <h1>Arreglos y confecciones Felicias</h1>
    <nav><ul><li><a href="index.php">Inicio</a></li><li><a href="formulario.php">Agendar</a></li></ul></nav>
  </header>
  <main class="login-wrap">
  <form method="POST" action="">
    <h2>🔐 Iniciar sesión</h2>

    <div class="tipo-acceso">
      <a class="<?= $tipo === 'admin' ? 'activo' : '' ?>" href="login.php?tipo=admin">Administrador</a>
      <a class="<?= $tipo === 'usuario' ? 'activo' : '' ?>" href="login.php?tipo=usuario">Usuario</a>
    </div>

    <?php if (!empty($error)): ?>
      <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <p class="subtitulo">
      <?= $tipo === 'admin' ? 'Ingresa como administrador.' : 'Ingresa como usuario.' ?>
    </p>

    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="hidden" name="tipo" value="<?= e($tipo) ?>">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Entrar</button>
    <a href="index.php">Volver al inicio</a>
  </form>
  </main>
  <footer class="site-footer" id="contacto">
    <p><i>📍</i> Calle 123, Bogotá</p>
    <p><i>📞</i> +57 300 123 4567</p>
    <p><i>✉️</i> contacto@mi-boutique.com</p>
  </footer>
</body>
</html>