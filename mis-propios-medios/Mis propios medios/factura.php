<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart_snapshot'])) {
    $_SESSION['cart_snapshot'] = [];
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
        // Si falla la BD, se usan datos guardados en sesión.
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
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
        ];
    }
}

$numeroFactura = 'FAC-' . date('Ymd-His');
$fecha = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Factura</title>
  <link rel="stylesheet" href="css/catalogo.css">
</head>
<body>
  <main class="factura">
    <h1>Factura de compra</h1>
    <p><strong>Número:</strong> <?= e($numeroFactura) ?></p>
    <p><strong>Fecha:</strong> <?= e($fecha) ?></p>

    <?php if (count($items) === 0): ?>
      <p>No hay productos en el carrito para facturar.</p>
      <a class="btn" href="catalogo_accesorios.php">Ir al catálogo</a>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= e((string) $item['nombre']) ?></td>
              <td>$ <?= e(number_format($item['precio'], 0, ',', '.')) ?></td>
              <td><?= e((string) $item['cantidad']) ?></td>
              <td>$ <?= e(number_format($item['subtotal'], 0, ',', '.')) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <p class="total">Total factura: $ <?= e(number_format($total, 0, ',', '.')) ?></p>
      <p class="nota">Gracias por tu compra.</p>
      <?php $_SESSION['cart'] = []; $_SESSION['cart_snapshot'] = []; ?>

      <div class="acciones">
        <a class="btn" href="catalogo_accesorios.php">Volver al catálogo</a>
        <a class="btn" href="index.php">Ir al inicio</a>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
