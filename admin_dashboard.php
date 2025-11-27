
<?php
// admin_dashboard.php
session_start();
require_once "conexion.php";

// Seguridad: Redirigir si no es admin o no está logueado
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Lógica para alternar entre vistas (Citas, Pedidos, Usuarios)
$view = isset($_GET['view']) ? $_GET['view'] : 'citas';

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Boutique</title>
  <link rel="stylesheet" href="cdnjs.cloudflare.com">
  <style>
    /* Estilos responsive básicos para el dashboard */
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
    .dashboard-container { display: flex; min-height: 100vh; }
    nav.sidebar { background: #333; color: white; width: 220px; padding: 20px; }
    nav.sidebar a { display: block; color: white; padding: 10px 0; text-decoration: none; border-bottom: 1px solid #555; }
    nav.sidebar a:hover { background: #555; }
    .main-content { flex-grow: 1; padding: 20px; }
    header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 10px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .action-btn { padding: 5px 10px; margin: 2px; }
    .status-badge { padding: 5px 8px; border-radius: 4px; color: white; }
    .status-pendiente { background: orange; }
    .status-cancelada { background: red; }
    .status-confirmada { background: green; }
    @media (max-width: 768px) {
        .dashboard-container { flex-direction: column; }
        nav.sidebar { width: 100%; padding: 10px; display: flex; justify-content: center; }
    }
  </style>
</head>
<body>

<div class="dashboard-container">
    <nav class="sidebar">
        <h2>Admin Panel</h2>
        <a href="?view=citas"><i class="fas fa-calendar-alt"></i> Citas</a>
        <a href="?view=pedidos"><i class="fas fa-box"></i> Pedidos</a>
        <a href="admin_accesorios.php"><i class="fas fa-shirt"></i> Productos</a>
        <a href="?view=usuarios"><i class="fas fa-users"></i> Usuarios</a>
        <a href="logout.php" style="margin-top: 20px; color: #e91e63;">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </nav>

    <main class="main-content">
        <header>
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin']); ?></h1>
            <span>Gestionando: <?php echo ucfirst($view); ?></span>
        </header>

        <section class="data-section">
            <!-- Contenido dinámico basado en $view -->
            <?php if ($view == 'citas'): ?>
                <h2>Gestión de Citas</h2>
                <!-- Aquí iría la lógica PHP para consultar la tabla `citas` y mostrarla en una tabla HTML -->
                <table>
                    <thead>
                        <tr><th>ID</th><th>Cliente ID</th><th>Servicio</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplo de fila (sustituir con bucle PHP) -->
                        <tr>
                            <td>1</td>
                            <td>5 (Nombre Cliente)</td>
                            <td>Arreglo</td>
                            <td><span class="status-badge status-pendiente">Pendiente</span></td>
                            <td>
                                <button class="action-btn">Ver</button>
                                <button class="action-btn">Cancelar</button>
                            </td>
                        </tr>
                        <!-- Fin ejemplo -->
                    </tbody>
                </table>
            
            <?php elseif ($view == 'pedidos'): ?>
                <h2>Gestión de Pedidos</h2>
                 <!-- Aquí iría la lógica PHP para consultar la tabla `pedidos` -->
                 <p>Contenido para gestionar pedidos...</p>

            <?php elseif ($view == 'productos'): ?>
                <h2>Gestión de Productos</h2>
                 <!-- Aquí iría la lógica PHP para consultar la tabla `productos` -->
                 <p>Contenido para gestionar productos (agregar, modificar, eliminar)...</p>

            <?php elseif ($view == 'usuarios'): ?>
                <h2>Gestión de Usuarios</h2>
                 <!-- Aquí iría la lógica PHP para consultar la tabla `usuarios` -->
                 <p>Contenido para gestionar usuarios (clientes y admins)...</p>

            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>