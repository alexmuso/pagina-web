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
if (strlen($descripcion) > 500) {
    $errores[] = 'La descripción no debe superar 500 caracteres.';
}

if (!empty($errores)) {
    $_SESSION['form_errors'] = $errores;
    $_SESSION['old_form'] = $_POST;
    header('Location: formulario.php');
    exit;
}

try {
    $sql = 'INSERT INTO citas (documento, nombre, apellido, correo, telefono, lugar, descripcion)
            VALUES (:documento, :nombre, :apellido, :correo, :telefono, :lugar, :descripcion)';
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':documento', $documento);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':lugar', $lugar);
    $stmt->bindParam(':descripcion', $descripcion);

    $stmt->execute();

    $_SESSION['success_message'] = '🎉 Cita registrada exitosamente. Gracias ' . $nombre . ' ' . $apellido . ', te contactaremos pronto.';
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
