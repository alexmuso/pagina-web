<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();
$csrf = csrf_token();

/**
 * @return array{table:string,user_column:string,pass_column:string,id_column:string,extra_where:string}|null
 */
function resolveAuthSource(PDO $conn): ?array
{
    $database = $conn->query('SELECT DATABASE()')->fetchColumn();
    if (!is_string($database) || $database === '') {
        return null;
    }

    $existsStmt = $conn->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :db AND table_name = :table'
    );

    $columnStmt = $conn->prepare(
        'SELECT COUNT(*) FROM information_schema.columns
         WHERE table_schema = :db AND table_name = :table AND column_name = :column'
    );

    $tableExists = function (string $table) use ($existsStmt, $database): bool {
        $existsStmt->execute([':db' => $database, ':table' => $table]);
        return (int) $existsStmt->fetchColumn() > 0;
    };

    $columnExists = function (string $table, string $column) use ($columnStmt, $database): bool {
        $columnStmt->execute([':db' => $database, ':table' => $table, ':column' => $column]);
        return (int) $columnStmt->fetchColumn() > 0;
    };

    if ($tableExists('admin')) {
        $passColumn = $columnExists('admin', 'contrasena') ? 'contrasena' : ($columnExists('admin', 'clave') ? 'clave' : null);
        if ($passColumn !== null) {
            return [
                'table' => 'admin',
                'user_column' => 'usuario',
                'pass_column' => $passColumn,
                'id_column' => 'id',
                'extra_where' => '',
            ];
        }

    if ($tableExists('usuarios') && $columnExists('usuarios', 'usuario') && $columnExists('usuarios', 'clave')) {
        $extra = $columnExists('usuarios', 'rol') ? ' AND rol = "admin"' : '';
        return [
            'table' => 'usuarios',
            'user_column' => 'usuario',
            'pass_column' => 'clave',
            'id_column' => 'id',
            'extra_where' => $extra,
        ];
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'Sesión inválida. Recarga la página e inténtalo de nuevo.';
    } else {
        $usuario = trim($_POST['usuario'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        try {
            $source = resolveAuthSource($conn);
            if ($source === null) {
                $error = 'No se encontró una tabla de usuarios administradores. Verifica la base de datos.';
            } else {
                $sql = sprintf(
                    'SELECT * FROM %s WHERE %s = :usuario%s LIMIT 1',
                    $source['table'],
                    $source['user_column'],
                    $source['extra_where']
                );

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':usuario', $usuario);
                $stmt->execute();

                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                $credenciales_validas = false;

                if ($admin) {
                    $hashGuardado = (string) ($admin[$source['pass_column']] ?? '');

                    if ($hashGuardado !== '' && password_verify($contrasena, $hashGuardado)) {
                        $credenciales_validas = true;
                    } elseif ($hashGuardado !== '' && hash_equals($hashGuardado, md5($contrasena))) {
                        $credenciales_validas = true;
                        $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
                        $update = $conn->prepare(sprintf(
                            'UPDATE %s SET %s = :contrasena WHERE %s = :id',
                            $source['table'],
                            $source['pass_column'],
                            $source['id_column']
                        ));
                        $update->bindParam(':contrasena', $nuevoHash);
                        $update->bindParam(':id', $admin[$source['id_column']], PDO::PARAM_INT);
                        $update->execute();
                    } elseif ($hashGuardado !== '' && hash_equals($hashGuardado, $contrasena)) {
                        // Compatibilidad para datos antiguos en texto plano.
                        $credenciales_validas = true;
                        $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
                        $update = $conn->prepare(sprintf(
                            'UPDATE %s SET %s = :contrasena WHERE %s = :id',
                            $source['table'],
                            $source['pass_column'],
                            $source['id_column']
                        ));
                        $update->bindParam(':contrasena', $nuevoHash);
                        $update->bindParam(':id', $admin[$source['id_column']], PDO::PARAM_INT);
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
