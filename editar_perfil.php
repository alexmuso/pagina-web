<?php
session_start();
require_once "conexion.php";
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// obtener datos actuales
$sql = "SELECT id, nombre, usuario, correo FROM usuarios WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $nueva_clave = trim($_POST['clave']);

    if ($nombre === '' || $correo === '') {
        $mensaje = "Nombre y correo no pueden quedar vacíos.";
    } else {
        if ($nueva_clave !== '') {
            $hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $sql_update = "UPDATE usuarios SET nombre = :nombre, correo = :correo, clave = :clave WHERE id = :id";
            $stmt_u = $conn->prepare($sql_update);
            $stmt_u->bindParam(':clave', $hash);
        } else {
            $sql_update = "UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :id";
            $stmt_u = $conn->prepare($sql_update);
        }
        $stmt_u->bindParam(':nombre', $nombre);
        $stmt_u->bindParam(':correo', $correo);
        $stmt_u->bindParam(':id', $usuario_id, PDO::PARAM_INT);

        if ($stmt_u->execute()) {
            $mensaje = "Datos actualizados correctamente.";
            // recargar datos
            $stmt = $conn->prepare("SELECT id, nombre, usuario, correo FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $mensaje = "Error al actualizar los datos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Perfil</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: url("img/patron.jfif") no-repeat center;
    background-size: cover;
    padding: 30px;
    margin: 0;
}
.container {
    max-width: 480px;
    margin: auto;
    background: #fff;
    padding: 22px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}
h2{
    text-align:center;
    background:#e91e63;
    color:white;
    padding:10px;
    border-radius:8px;
}
label{display:block;margin-top:12px;font-weight:600}
input{width:100%;padding:10px;margin-top:6px;border-radius:6px;border:1px solid #ccc}
button{width:100%;padding:12px;margin-top:16px;background:#e91e63;color:#fff;border:none;border-radius:8px;cursor:pointer}
.mensaje{background:#4caf50;color:#fff;padding:8px;text-align:center;border-radius:6px;margin-bottom:8px}
.btn-volver{display:block;text-align:center;margin-top:12px;text-decoration:none;padding:10px;background:#333;color:#fff;border-radius:6px}



</style>
</head>
<body>
<div class="container">
<h2>👤 Editar Perfil</h2>
<?php if ($mensaje): ?>
    <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<form method="post" action="editar_perfil.php">
    <label>Nombre</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

    <label>Correo</label>
    <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>

    <label>Nueva contraseña (opcional)</label>
    <input type="password" name="clave" placeholder="Dejar vacío para mantener actual">

    <button type="submit">Guardar cambios</button>
</form>

<a class="btn-volver" href="mis_citas_pedidos.php">⬅ Volver</a>
</div>
</body>
</html>

