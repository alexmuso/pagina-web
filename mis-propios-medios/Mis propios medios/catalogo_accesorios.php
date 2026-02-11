<?php
require_once 'conexion.php';
require_once 'auth.php';

ensure_session_started();
$csrf = csrf_token();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$mensaje = '';

$imagenesPorDefecto = [
    1 => 'img/maquina.jfif',
    2 => 'img/patron.jfif',
    3 => 'img/medidas.jfif',
];

/**
 * Si la tabla accesorios está vacía, inserta un catálogo base usando imágenes locales.
 */
function poblarCatalogoInicial(PDO $conn): void
{
    $semilla = [
        ['Bolso elegante', 'Bolso elegante para uso diario.', 85000, 'img/maquina.jfif'],
        ['Monedero artesanal', 'Monedero compacto hecho a mano.', 25000, 'img/patron.jfif'],
        ['Aretes de perlas', 'Aretes clásicos para ocasiones especiales.', 18000, 'img/medidas.jfif'],
    ];

    $insert = $conn->prepare('INSERT INTO accesorios (nombre, descripcion, precio, stock, imagen) VALUES (:nombre, :descripcion, :precio, :stock, :imagen)');
    foreach ($semilla as $item) {
        $insert->execute([
            ':nombre' => $item[0],
            ':descripcion' => $item[1],
            ':precio' => $item[2],
            ':stock' => 10,
            ':imagen' => $item[3],
        ]);
    }
}

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

try {
    $sql = 'SELECT id, nombre, descripcion, precio, imagen FROM accesorios ORDER BY id ASC';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $accesorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($accesorios) === 0) {
        poblarCatalogoInicial($conn);
        $stmt->execute();
        $accesorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $mensaje = 'Se cargó un catálogo base de accesorios.';
    }
} catch (PDOException $e) {
    $accesorios = [];
    $mensaje = 'No fue posible cargar los accesorios. Verifica la base de datos.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        $mensaje = 'Sesión inválida. Recarga la página.';
    } else {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

        $idsValidos = array_map(static fn(array $item): int => (int) $item['id'], $accesorios);

        if ($id && $cantidad && $cantidad > 0 && in_array($id, $idsValidos, true)) {
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
        <?php $imagen = resolverImagenAccesorio($item, $imagenesPorDefecto); ?>
        <img src="<?= e($imagen) ?>" alt="<?= e((string) $item['nombre']) ?>" class="producto-img">
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
