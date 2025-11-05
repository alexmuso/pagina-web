<?php
require_once "conexion.php";

$sql = "SELECT * FROM citas ORDER BY fecha_registro DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Citas Registradas</title>
  <style>
    body { font-family: Arial; background: #f5f5f5; color: #333; text-align:center; }
    table { margin: 30px auto; border-collapse: collapse; width: 90%; background:white; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #e91e63; color:white; }
    tr:nth-child(even) { background: #f9f9f9; }
    a { color:#e91e63; text-decoration:none; }
    a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <h2>📋 Citas Agendadas</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Documento</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>Correo</th>
      <th>Teléfono</th>
      <th>Lugar</th>
      <th>Descripción</th>
      <th>Fecha Registro</th>
      <th>Acciones</th>

    </tr>

    <?php foreach ($citas as $cita): ?>
      <tr>
        <td><?= htmlspecialchars($cita['id']) ?></td>
        <td><?= htmlspecialchars($cita['documento']) ?></td>
        <td><?= htmlspecialchars($cita['nombre']) ?></td>
        <td><?= htmlspecialchars($cita['apellido']) ?></td>
        <td><?= htmlspecialchars($cita['correo']) ?></td>
        <td><?= htmlspecialchars($cita['telefono']) ?></td>
        <td><?= htmlspecialchars($cita['lugar']) ?></td>
        <td><?= htmlspecialchars($cita['descripcion']) ?></td>
        <td><?= htmlspecialchars($cita['fecha_registro']) ?></td>
        <td><a href='eliminar.php?id=<?= $cita["id"] ?>' onclick="return confirm('¿Deseas eliminar esta cita?')">🗑️ Eliminar</a></td>

      </tr>
    <?php endforeach; ?>
  </table>

  <a href="index.php">Volver al inicio</a>
</body>
</html>
