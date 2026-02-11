<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();
$csrf = csrf_token();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart_snapshot'])) {
    $_SESSION['cart_snapshot'] = [];
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        $mensaje = 'Sesión inválida. Recarga la página.';
    } else {
        $accion = $_POST['accion'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($accion === 'eliminar' && $id) {
            unset($_SESSION['cart'][$id]);
            unset($_SESSION['cart_snapshot'][$id]);
            $mensaje = 'Producto eliminado del carrito.';
        }

        if ($accion === 'vaciar') {
            $_SESSION['cart'] = [];
            $_SESSION['cart_snapshot'] = [];
            $mensaje = 'Carrito vaciado.';
        }
    }
}

$ids = array_keys($_SESSION['cart']);
$items = [];
$total = 0;
$rowsById = [];

if (count($ids) > 0) {
    try {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, nombre, precio FROM accesorios WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($ids);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $rowsById[(int) $row['id']] = $row;
        }
    } catch (PDOException $e) {
        $mensaje = 'No fue posible consultar accesorios en base de datos. Mostrando datos guardados del carrito.';
    }

    foreach ($ids as $idCart) {
        $id = (int) $idCart;
        $cantidad = (int) ($_SESSION['cart'][$id] ?? 0);
        if ($cantidad <= 0) {
            continue;
        }

        if (isset($rowsById[$id])) {
            $nombre = (string) $rowsById[$id]['nombre'];
            $precio = (float) $rowsById[$id]['precio'];
        } else {
            $snapshot = $_SESSION['cart_snapshot'][$id] ?? null;
            if (!is_array($snapshot)) {
                continue;
            }
            $nombre = (string) ($snapshot['nombre'] ?? 'Producto');
            $precio = (float) ($snapshot['precio'] ?? 0);
        }

        $subtotal = $cantidad * $precio;
        $total += $subtotal;
        $items[] = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrito de compra</title>
  <link rel="stylesheet" href="css/catalogo.css">
</head>
<body>
  <header class="topbar">
    <h1>🧾 Carrito de compra</h1>
    <nav>
      <a href="catalogo_accesorios.php">Seguir comprando</a>
      <a href="index.php">Inicio</a>
    </nav>
  </header>

  <?php if ($mensaje !== ''): ?>
    <p class="mensaje"><?= e($mensaje) ?></p>
  <?php endif; ?>

  <main class="tabla-wrap">
    <?php if (count($items) === 0): ?>
      <p>Tu carrito está vacío.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= e((string) $item['nombre']) ?></td>
              <td>$ <?= e(number_format($item['precio'], 0, ',', '.')) ?></td>
              <td><?= e((string) $item['cantidad']) ?></td>
              <td>$ <?= e(number_format($item['subtotal'], 0, ',', '.')) ?></td>
              <td>
                <form method="POST" action="">
                  <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                  <input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
                  <input type="hidden" name="accion" value="eliminar">
                  <button type="submit">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <p class="total">Total: $ <?= e(number_format($total, 0, ',', '.')) ?></p>

      <div class="acciones">
        <form method="POST" action="">
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <input type="hidden" name="accion" value="vaciar">
          <button type="submit">Vaciar carrito</button>
        </form>
        <a class="btn" href="factura.php">Generar factura</a>
      </div>
    <?php endif; ?>
  </main>
  <footer class="site-footer" id="contacto">
    <p><i>📍</i> Calle 123, Bogotá</p>
    <p><i>📞</i> +57 300 123 4567</p>
    <p><i>✉️</i> contacto@mi-boutique.com</p>
  </footer>
</body>
</html>
