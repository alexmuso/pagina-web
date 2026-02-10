<?php
require_once 'auth.php';
require_admin_session();
require_once 'conexion.php';

$sql = 'SELECT * FROM citas ORDER BY fecha_registro DESC';
$stmt = $conn->prepare($sql);
$stmt->execute();
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$csrf = csrf_token();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Citas registradas</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
  <header>
    <h1>📋 Citas agendadas</h1>
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
      <th>Fecha registro</th>
      <th>Acciones</th>
    </tr>

    <?php foreach ($citas as $cita): ?>
      <tr>
        <td><?= e((string) $cita['id']) ?></td>
        <td><?= e((string) $cita['documento']) ?></td>
        <td><?= e((string) $cita['nombre']) ?></td>
        <td><?= e((string) $cita['apellido']) ?></td>
        <td><?= e((string) $cita['correo']) ?></td>
        <td><?= e((string) $cita['telefono']) ?></td>
        <td><?= e((string) $cita['lugar']) ?></td>
        <td><?= e((string) $cita['descripcion']) ?></td>
        <td><?= e((string) $cita['fecha_registro']) ?></td>
        <td class="acciones">
          <form method="POST" action="eliminar_cita.php" onsubmit="return confirm('¿Deseas eliminar esta cita?');">
            <input type="hidden" name="id" value="<?= e((string) $cita['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <button type="submit" class="delete-button">🗑️ Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <div class="acciones-globales">
    <a href="admin.php" class="boton">Volver al panel</a>
  </div>
</body>
</html>
