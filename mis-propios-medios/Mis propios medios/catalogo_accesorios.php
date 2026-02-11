<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();
$csrf = csrf_token();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        $mensaje = 'Sesión inválida. Recarga la página.';
    } else {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

        if ($id && $cantidad && $cantidad > 0) {
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = 0;
            }
            $_SESSION['cart'][$id] += $cantidad;
            $mensaje = 'Accesorio agregado al carrito.';
        } else {
            $mensaje = 'Datos inválidos para agregar al carrito.';
        }
    }
}

$sql = 'SELECT id, nombre, descripcion, precio, imagen FROM accesorios ORDER BY id ASC';
$stmt = $conn->prepare($sql);
$stmt->execute();
$accesorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$itemsEnCarrito = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo de accesorios</title>
  <link rel="stylesheet" href="css/catalogo.css">
</head>
<body>
  <header class="topbar">
    <h1>🛍️ Catálogo de accesorios</h1>
    <nav>
      <a href="index.php">Inicio</a>
      <a href="carrito.php">Carrito (<?= e((string) $itemsEnCarrito) ?>)</a>
    </nav>
  </header>

  <?php if ($mensaje !== ''): ?>
    <p class="mensaje"><?= e($mensaje) ?></p>
  <?php endif; ?>

  <main class="grid">
    <?php if (count($accesorios) === 0): ?>
      <p>No hay accesorios disponibles por ahora.</p>
    <?php endif; ?>

    <?php foreach ($accesorios as $item): ?>
      <article class="card">
        <h2><?= e((string) $item['nombre']) ?></h2>
        <p class="descripcion"><?= e((string) ($item['descripcion'] ?? 'Sin descripción')) ?></p>
        <p class="precio">$ <?= e(number_format((float) $item['precio'], 0, ',', '.')) ?></p>

        <form method="POST" action="">
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
          <label>Cantidad</label>
          <input type="number" name="cantidad" min="1" max="10" value="1" required>
          <button type="submit">Agregar al carrito</button>
        </form>
      </article>
    <?php endforeach; ?>
  </main>
</body>
</html>
