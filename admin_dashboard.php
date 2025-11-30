<?php
session_start();
require_once "conexion.php";

// Seguridad: el login que tienes establece $_SESSION['admin'] (usuario admin)
// Si prefieres comprobar rol, puedes usar $_SESSION['rol'] si tu login lo guarda.
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Vista activa (citas, pedidos, productos, usuarios)
$view = isset($_GET['view']) ? $_GET['view'] : 'citas';

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body { font-family: Arial; margin: 0; background: #f4f4f4; }
.dashboard-container { display: flex; min-height:100vh; }
.sidebar { width: 220px; background: #222; color: white; padding: 20px; }
.sidebar h2 { margin-top:0; }
.sidebar a { color: white; display: block; padding: 10px; text-decoration: none; border-bottom: 1px solid #444; }
.sidebar a:hover { background: #444; }
.main-content { flex-grow: 1; padding: 20px; }
.table-wrap { background: white; padding: 12px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
th { background: #f1f1f1; }
.action-btn { padding: 6px 10px; margin-right:6px; background: #e91e63; color:white; border: none; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-block; }
.delete-btn { background: #d32f2f; }
@media (max-width: 768px) {
  .dashboard-container { flex-direction: column; }
  .sidebar { width: 100%; height: auto; display:flex; gap:10px; overflow:auto; }
  .sidebar a { flex:1; text-align:center; border-bottom:none; }
}
</style>
</head>
<body>

<div class="dashboard-container">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <h2>ADMIN</h2>
    <a href="?view=citas">Citas</a>
    <a href="?view=pedidos">Pedidos</a>
    <a href="?view=productos">Productos</a>
    <a href="?view=usuarios">Usuarios</a>
    <a href="logout.php" style="color:#ff8080; margin-top:10px; display:block;">Cerrar sesión</a>
  </aside>

  <!-- MAIN -->
  <main class="main-content">
    <h1>Panel Administrador</h1>

    <div class="table-wrap">

    <?php
    // =====================================================
    // VIEW = CITAS
    // =====================================================
    if ($view === 'citas'):

        try {
            // Consulta usando los nombres reales de tus tablas y columnas
            $sql = "SELECT c.id, u.usuario AS usuario, c.nombre AS nombre, c.apellido AS apellido,
                           c.servicio AS servicio, c.fecha_registro AS fecha_registro, c.hora AS hora, c.estado AS estado
                    FROM citas c
                    LEFT JOIN usuarios u ON c.usuario_id = u.id
                    ORDER BY c.fecha_registro DESC";
            $stmt = $conn->query($sql);
            $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al consultar citas: " . htmlspecialchars($e->getMessage()) . "</p>";
            $citas = [];
        }
    ?>

      <h2>Gestión de Citas</h2>

      <?php if (count($citas) === 0): ?>
        <p>No hay citas registradas.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Cliente</th>
              <th>Servicio</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($citas as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['id']) ?></td>
                <td><?= htmlspecialchars($c['usuario'] ?? '—') ?></td>
                <td><?= htmlspecialchars(trim(($c['nombre'] ?? '') . ' ' . ($c['apellido'] ?? ''))) ?></td>
                <td><?= htmlspecialchars($c['servicio'] ?? '—') ?></td>
                <td><?= htmlspecialchars($c['fecha_registro'] ?? '—') ?></td>
                <td><?= htmlspecialchars($c['hora'] ?? '—') ?></td>
                <td><?= htmlspecialchars($c['estado'] ?? '—') ?></td>
                <td>
                  <a class="action-btn" href="editar_cita.php?id=<?= urlencode($c['id']) ?>">Editar</a>
                  <a class="action-btn delete-btn" href="eliminar_cita.php?id=<?= urlencode($c['id']) ?>"
                     onclick="return confirm('¿Eliminar esta cita?');">Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    <?php
    // =====================================================
    // VIEW = PEDIDOS
    // =====================================================
    elseif ($view === 'pedidos'):

        try {
            $sql = "SELECT p.id, p.usuario_id, u.usuario AS usuario, p.total, p.fecha_pedido, p.estado
                    FROM pedidos p
                    LEFT JOIN usuarios u ON p.usuario_id = u.id
                    ORDER BY p.fecha_pedido DESC";
            $stmt = $conn->query($sql);
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al consultar pedidos: " . htmlspecialchars($e->getMessage()) . "</p>";
            $pedidos = [];
        }
    ?>

      <h2>Gestión de Pedidos</h2>

      <?php if (count($pedidos) === 0): ?>
        <p>No hay pedidos registrados.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>ID</th><th>Usuario</th><th>Total</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['id']) ?></td>
              <td><?= htmlspecialchars($p['usuario'] ?? '—') ?></td>
              <td>$<?= htmlspecialchars(number_format($p['total'],2)) ?></td>
              <td><?= htmlspecialchars($p['fecha_pedido'] ?? '—') ?></td>
              <td><?= htmlspecialchars($p['estado'] ?? '—') ?></td>
              <td>
                <a class="action-btn" href="ver_pedido.php?id=<?= urlencode($p['id']) ?>">Ver</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    <?php
    // =====================================================
    // VIEW = PRODUCTOS
    // =====================================================
    elseif ($view === 'productos'):

        try {
            $sql = "SELECT * FROM accesorios ORDER BY id DESC";
            $stmt = $conn->query($sql);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al consultar productos: " . htmlspecialchars($e->getMessage()) . "</p>";
            $productos = [];
        }
    ?>

      <h2>Gestión de Productos</h2>

      <a class="action-btn" href="admin_accesorios.php">Agregar producto</a>

      <?php if (count($productos) === 0): ?>
        <p>No hay productos.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Imagen</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            <?php foreach ($productos as $prod): ?>
            <tr>
              <td><?= htmlspecialchars($prod['id']) ?></td>
              <td><?= htmlspecialchars($prod['nombre']) ?></td>
              <td>$<?= htmlspecialchars(number_format($prod['precio'],2)) ?></td>
              <td><?= htmlspecialchars($prod['stock'] ?? '—') ?></td>
              <td>
                <?php if (!empty($prod['imagen'])): ?>
                  <img src="<?= htmlspecialchars($prod['imagen']) ?>" alt="" style="height:40px;">
                <?php endif; ?>
              </td>
              <td>
                <a class="action-btn" href="editar_producto.php?id=<?= urlencode($prod['id']) ?>">Editar</a>
                <a class="action-btn delete-btn" href="eliminar_producto.php?id=<?= urlencode($prod['id']) ?>"
                   onclick="return confirm('¿Eliminar producto?');">Eliminar</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    <?php
    // =====================================================
    // VIEW = USUARIOS
    // =====================================================
    elseif ($view === 'usuarios'):

        try {
            $sql = "SELECT id, nombre, usuario, correo, rol, fecha_registro FROM usuarios ORDER BY id DESC";
            $stmt = $conn->query($sql);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al consultar usuarios: " . htmlspecialchars($e->getMessage()) . "</p>";
            $usuarios = [];
        }
    ?>

      <h2>Gestión de Usuarios</h2>

      <?php if (count($usuarios) === 0): ?>
        <p>No hay usuarios.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Fecha registro</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['id']) ?></td>
              <td><?= htmlspecialchars($u['nombre']) ?></td>
              <td><?= htmlspecialchars($u['usuario']) ?></td>
              <td><?= htmlspecialchars($u['correo']) ?></td>
              <td><?= htmlspecialchars($u['rol']) ?></td>
              <td><?= htmlspecialchars($u['fecha_registro']) ?></td>
              <td>
                <a class="action-btn" href="editar_usuario_admin.php?id=<?= urlencode($u['id']) ?>">Editar</a>
                <a class="action-btn delete-btn" href="eliminar_usuario.php?id=<?= urlencode($u['id']) ?>"
                   onclick="return confirm('¿Eliminar usuario?');">Eliminar</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    <?php endif; // end view handling ?>
    </div>

  </main>

</div>

</body>
</html>
