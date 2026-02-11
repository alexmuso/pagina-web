<?php
require_once 'auth.php';
require_once 'conexion.php';

ensure_session_started();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.php');
    exit;
}


if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['form_errors'] = ['Sesión inválida. Recarga la página e inténtalo de nuevo.'];
    $_SESSION['old_form'] = $_POST;
    header('Location: formulario.php');
    exit;
}
$documento = trim($_POST['documento'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$lugar = trim($_POST['lugar'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$servicio = trim($_POST['servicio'] ?? '');
$crearUsuario = (isset($_POST['crear_usuario']) && $_POST['crear_usuario'] === '1');

$errores = [];

if (!preg_match('/^[0-9]{6,20}$/', $documento)) {
    $errores[] = 'El documento debe tener entre 6 y 20 dígitos.';
}
if (!preg_match('/^[\p{L} ]{2,60}$/u', $nombre)) {
    $errores[] = 'El nombre debe tener entre 2 y 60 letras.';
}
if (!preg_match('/^[\p{L} ]{2,60}$/u', $apellido)) {
    $errores[] = 'El apellido debe tener entre 2 y 60 letras.';
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 120) {
    $errores[] = 'Correo electrónico inválido.';
}
if (!preg_match('/^[0-9+\- ]{7,20}$/', $telefono)) {
    $errores[] = 'Teléfono inválido.';
}
if (!in_array($lugar, ['local', 'domicilio'], true)) {
    $errores[] = 'Lugar de cita inválido.';
}
if (!in_array($servicio, ['arreglo', 'confeccion', 'accesorios'], true)) {
    $errores[] = 'Servicio inválido.';
}
if (strlen($descripcion) > 500) {
    $errores[] = 'La descripción no debe superar 500 caracteres.';
}

if (!empty($errores)) {
    $_SESSION['form_errors'] = $errores;
    $_SESSION['old_form'] = $_POST;
    header('Location: formulario.php');
    exit;
}


function generar_usuario_sugerido(string $nombre, string $correo, string $documento): string
{
    $base = strtolower(trim((string) strstr($correo, '@', true)));
    if ($base === '') {
        $base = strtolower(preg_replace('/[^a-z0-9]/i', '', $nombre));
    }
    if ($base === '') {
        $base = 'cliente';
    }

    $base = preg_replace('/[^a-z0-9_]/', '', $base);
    if ($base === null || $base === '') {
        $base = 'cliente';
    }

    return substr($base, 0, 20) . substr($documento, -4);
}

try {
    $sql = 'INSERT INTO citas (documento, nombre, apellido, correo, telefono, lugar, descripcion, servicio)
            VALUES (:documento, :nombre, :apellido, :correo, :telefono, :lugar, :descripcion, :servicio)';
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':documento', $documento);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':lugar', $lugar);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':servicio', $servicio);

    $stmt->execute();

    $mensajeCuenta = '';

    if ($crearUsuario) {
        try {
            $usuarioGenerado = generar_usuario_sugerido($nombre, $correo, $documento);
            $stmtExiste = $conn->prepare('SELECT id, usuario FROM usuarios WHERE correo = :correo OR usuario = :usuario LIMIT 1');
            $stmtExiste->bindParam(':correo', $correo);
            $stmtExiste->bindParam(':usuario', $usuarioGenerado);
            $stmtExiste->execute();
            $existente = $stmtExiste->fetch(PDO::FETCH_ASSOC);

            if ($existente) {
                $mensajeCuenta = ' Ya existe una cuenta relacionada. Puedes iniciar sesión con tu usuario actual.';
            } else {
                $claveTemporalPlano = $documento;
                $claveTemporalHash = password_hash($claveTemporalPlano, PASSWORD_DEFAULT);
                $nombreCompleto = trim($nombre . ' ' . $apellido);
                $rol = 'cliente';

                $sqlUsuario = 'INSERT INTO usuarios (nombre, usuario, correo, clave, rol) VALUES (:nombre, :usuario, :correo, :clave, :rol)';
                $stmtUsuario = $conn->prepare($sqlUsuario);
                $stmtUsuario->bindParam(':nombre', $nombreCompleto);
                $stmtUsuario->bindParam(':usuario', $usuarioGenerado);
                $stmtUsuario->bindParam(':correo', $correo);
                $stmtUsuario->bindParam(':clave', $claveTemporalHash);
                $stmtUsuario->bindParam(':rol', $rol);
                $stmtUsuario->execute();

                $mensajeCuenta = ' Usuario creado: ' . $usuarioGenerado . '. Contraseña temporal: tu número de documento. Inicia sesión y cámbiala luego.';
            }
        } catch (PDOException $e) {
            $mensajeCuenta = ' No fue posible crear la cuenta automáticamente, pero tu cita sí quedó registrada.';
        }
    }

    $_SESSION['success_message'] = '🎉 Cita registrada exitosamente. Gracias ' . $nombre . ' ' . $apellido . ', te contactaremos pronto.' . $mensajeCuenta;
    unset($_SESSION['old_form'], $_SESSION['form_errors']);
    header('Location: formulario.php?ok=1');
    exit;
} catch (PDOException $e) {
    $_SESSION['form_errors'] = ['❌ Error al guardar la cita. Inténtalo nuevamente.'];
    $_SESSION['old_form'] = $_POST;
    header('Location: formulario.php');
    exit;
}
?>
