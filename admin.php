<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include("conexion.php");

// Consultar las citas guardadas usando PDO
try {
    $sql = "SELECT * FROM citas ORDER BY fecha_registro DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al consultar las citas: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración - Citas</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Georgia', serif;
      background: #f5f5f5;
      color: #333;
      margin: 0;
      padding: 0;
    }
    header {
      background: #222;
      color: white;
      padding: 20px;
      text-align: center;
    }
    h1 {
      font-family: "Brush Script MT", cursive;
      font-size: 32px;
      margin: 0;
    }
    table {
      width: 90%;
      margin: 30px auto;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }
    th {
      background: #e91e63;
      color: white;
    }
    tr:nth-child(even) {
      background: #f9f9f9;
    }
    a.boton {
      display: inline-block;
      background: #e91e63;
      color: white;
      text-decoration: none;
      padding: 8px 14px;
      border-radius: 6px;
      margin-top: 10px;
    }
    a.boton:hover {
      background: #c2185b;
    }
    .acciones a {
      margin: 0 5px;
      color: #e91e63;
      text-decoration: none;
    }
    .acciones a:hover {
      color: #c2185b;
    }
  </style>
</head>
<body>
  <header>
    <h1>Panel de Administración - Citas Agendadas</h1>
  </header>

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
      <th>Fecha de Registro</th>
      <th>Acciones</th>
    </tr>

    <?php if (count($citas) > 0): ?>
      <?php foreach ($citas as $fila): ?>
        <tr>
          <td><?= htmlspecialchars($fila['id']) ?></td>
          <td><?= htmlspecialchars($fila['documento']) ?></td>
          <td><?= htmlspecialchars($fila['nombre']) ?></td>
          <td><?= htmlspecialchars($fila['apellido']) ?></td>
          <td><?= htmlspecialchars($fila['correo']) ?></td>
          <td><?= htmlspecialchars($fila['telefono']) ?></td>
          <td><?= htmlspecialchars($fila['lugar']) ?></td>
          <td><?= htmlspecialchars($fila['descripcion']) ?></td>
          <td><?= htmlspecialchars($fila['fecha_registro']) ?></td>
          <td class="acciones">
            <a href="eliminar.php?id=<?= $fila['id'] ?>" title="Eliminar" onclick="return confirm('¿Deseas eliminar esta cita?')">
              <i class="fa-solid fa-trash"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="10">No hay citas registradas aún.</td></tr>
    <?php endif; ?>
  </table>

  <div style="text-align:center;">
    <a href="index.php" class="boton">Volver al inicio</a>
  </div>
<div style="text-align:center; margin-top:20px;">
  <a href="logout.php" class="boton">Cerrar sesión</a>
</div>

</body>
</html>
