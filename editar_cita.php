<?php
session_start();
require_once "conexion.php";

// Seguridad: solo admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Validar ID
if (!isset($_GET['id'])) {
    echo "ID no proporcionado";
    exit;
}

$id = $_GET['id'];

// CONSULTAR CITA
$sql = "SELECT * FROM citas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cita) {
    echo "Cita no encontrada.";
    exit;
}

// ACTUALIZAR CITA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servicio = $_POST["servicio"];
    $fecha = $_POST["fecha_registro"];
    $hora = $_POST["hora"];
    $estado = $_POST["estado"];

    $sqlUpdate = "UPDATE citas SET servicio=?, fecha_registro=?, hora=?, estado=? WHERE id=?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->execute([$servicio, $fecha, $hora, $estado, $id]);

    header("Location: admin_dashboard.php?view=citas");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Cita</title>
<style>
body {
    background: #f4f4f4;
    font-family: Arial;
    margin: 0;
    padding: 20px;
}

.container {
    width: 450px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.2);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}

input, select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #bbb;
}

button {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background: #e91e63;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background: #d21857;
}

.back {
    display: block;
    text-align: center;
    margin-top: 15px;
    text-decoration: none;
    color: #555;
}
</style>
</head>
<body>

<div class="container">
    <h2>Editar Cita</h2>

    <form method="POST">

        <label>Servicio</label>
        <input type="text" name="servicio" value="<?= $cita['servicio'] ?>" required>

        <label>Fecha</label>
        <input type="date" name="fecha_registro" value="<?= $cita['fecha_registro'] ?>" required>

        <label>Hora</label>
        <input type="time" name="hora" value="<?= $cita['hora'] ?>" required>

        <label>Estado</label>
        <select name="estado">
            <option value="pendiente" <?= $cita['estado']=="pendiente"?"selected":"" ?>>Pendiente</option>
            <option value="confirmada" <?= $cita['estado']=="confirmada"?"selected":"" ?>>Confirmada</option>
            <option value="cancelada" <?= $cita['estado']=="cancelada"?"selected":"" ?>>Cancelada</option>
        </select>

        <button type="submit">Guardar Cambios</button>
    </form>

    <a class="back" href="admin_dashboard.php?view=citas">← Volver</a>
</div>

</body>
</html>
