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

$accesorios = [];
$errorCatalogo = '';

try {
    $sql = 'SELECT id, nombre, descripcion, precio, imagen, stock FROM accesorios ORDER BY id ASC';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $accesorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorCatalogo = 'No se pudo cargar la base de datos. Mostrando catálogo de ejemplo.';
}

if (count($accesorios) === 0) {
    $accesorios = [
        ['id' => 1, 'nombre' => 'Bolso elegante', 'descripcion' => 'Bolso de mano para eventos especiales.', 'precio' => 85000, 'imagen' => 'img/maquina.jfif', 'stock' => 5],
        ['id' => 2, 'nombre' => 'Monedero artesanal', 'descripcion' => 'Monedero compacto con acabado artesanal.', 'precio' => 25000, 'imagen' => 'img/patron.jfif', 'stock' => 8],
        ['id' => 3, 'nombre' => 'Aretes de perlas', 'descripcion' => 'Aretes clásicos para complementar tu estilo.', 'precio' => 18000, 'imagen' => 'img/medidas.jfif', 'stock' => 12],
    ];
}

$itemsEnCarrito = array_sum($_SESSION['cart']);

$imagenesPorDefecto = [
    1 => 'img/maquina.jfif',
    2 => 'img/patron.jfif',
    3 => 'img/medidas.jfif',
];

function resolverImagenAccesorio(array $item, array $imagenesPorDefecto): string
{
    $raw = trim((string) ($item['imagen'] ?? ''));

    if ($raw !== '') {
        $normalizada = str_replace('\\', '/', $raw);
        $basename = basename($normalizada);
        if ($basename !== '' && is_file(__DIR__ . '/img/' . $basename)) {
            return 'img/' . $basename;
        }

        if (preg_match('#^img/#i', $normalizada) && is_file(__DIR__ . '/' . $normalizada)) {
            return $normalizada;
        }
    }

    $id = (int) ($item['id'] ?? 0);
    if (isset($imagenesPorDefecto[$id]) && is_file(__DIR__ . '/' . $imagenesPorDefecto[$id])) {
        return $imagenesPorDefecto[$id];
    }

    return 'img/mujer.jfif';
}

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

  <?php if ($errorCatalogo !== ''): ?>
    <p class="mensaje aviso"><?= e($errorCatalogo) ?></p>
  <?php endif; ?>

  <main class="grid">
    <?php foreach ($accesorios as $item): ?>
      <?php
        $imagen = resolverImagenAccesorio($item, $imagenesPorDefecto);
        $stock = (int) ($item['stock'] ?? 0);
      ?>
      <article class="card producto-card">
        <div class="img-wrap">
          <img src="<?= e($imagen) ?>" alt="<?= e((string) $item['nombre']) ?>" class="producto-img">
          <span class="etiqueta-stock <?= $stock > 0 ? 'ok' : 'agotado' ?>">
            <?= $stock > 0 ? 'Disponible' : 'Agotado' ?>
          </span>
        </div>

        <h2><?= e((string) $item['nombre']) ?></h2>
        <p class="descripcion"><?= e((string) ($item['descripcion'] ?? 'Sin descripción')) ?></p>
        <p class="precio">$ <?= e(number_format((float) $item['precio'], 0, ',', '.')) ?></p>

        <form method="POST" action="">
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
          <label>Cantidad</label>
          <input type="number" name="cantidad" min="1" max="10" value="1" required <?= $stock <= 0 ? 'disabled' : '' ?>>
          <button type="submit" <?= $stock <= 0 ? 'disabled' : '' ?>>Agregar al carrito</button>
        </form>
      </article>
    <?php endforeach; ?>
  </main>
</body>
</html>
