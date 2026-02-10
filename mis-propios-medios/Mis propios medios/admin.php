<?php
require_once 'auth.php';
require_admin_session();
require_once 'conexion.php';

try {
    $sql = 'SELECT * FROM citas ORDER BY fecha_registro DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error al consultar las citas: ' . e($e->getMessage());
    exit;
}

$csrf = csrf_token();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración - Citas</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
  <header>
    <h1>Panel de administración - Citas agendadas</h1>
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
      <th>Fecha de registro</th>
      <th>Acciones</th>
    </tr>

    <?php if (count($citas) > 0): ?>
      <?php foreach ($citas as $fila): ?>
        <tr>
          <td><?= e((string) $fila['id']) ?></td>
          <td><?= e((string) $fila['documento']) ?></td>
          <td><?= e((string) $fila['nombre']) ?></td>
          <td><?= e((string) $fila['apellido']) ?></td>
          <td><?= e((string) $fila['correo']) ?></td>
          <td><?= e((string) $fila['telefono']) ?></td>
          <td><?= e((string) $fila['lugar']) ?></td>
          <td><?= e((string) $fila['descripcion']) ?></td>
          <td><?= e((string) $fila['fecha_registro']) ?></td>
          <td class="acciones">
            <form method="POST" action="eliminar_cita.php" onsubmit="return confirm('¿Deseas eliminar esta cita?');">
              <input type="hidden" name="id" value="<?= e((string) $fila['id']) ?>">
              <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
              <button type="submit" title="Eliminar" class="delete-button">
                <i class="fa-solid fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="10">No hay citas registradas aún.</td></tr>
    <?php endif; ?>
  </table>

  <div class="acciones-globales">
    <a href="index.php" class="boton">Volver al inicio</a>
    <a href="logout.php" class="boton">Cerrar sesión</a>
  </div>
</body>
</html>
