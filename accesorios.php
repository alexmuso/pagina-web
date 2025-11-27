<?php
session_start();

// Inicializar carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Productos de ejemplo
$productos = [
    1 => ["nombre" => "Bolso artesanal", "precio" => 45000, "img" => "img/bolso1.jpg"],
    2 => ["nombre" => "Monedero tejido", "precio" => 15000, "img" => "img/monedero.jpg"],
    3 => ["nombre" => "Cartera elegante", "precio" => 55000, "img" => "img/cartera.jpg"],
];

// Agregar al carrito
if (isset($_GET["add"])) {
    $id = intval($_GET["add"]);

    if (!isset($_SESSION["usuario_id"])) {
        // Si no ha iniciado sesión → enviarlo al login
        header("Location: login.php?redir=accesorios");
        exit;
    }

    if (!isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] = 1;
    } else {
        $_SESSION['carrito'][$id]++;
    }

    header("Location: accesorios.php?ok");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Accesorios - Boutique</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; margin: 0; }
        .container { width: 90%; margin: auto; padding: 20px; }
        h1 { text-align: center; }
        .productos { display: flex; gap: 20px; justify-content: center; }
        .card {
            background: white;
            width: 250px;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .card img { width: 100%; border-radius: 10px; }
        .card h3 { margin: 10px 0; }
        .btn {
            background: #e91e63;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn:hover { background: #c2185b; }
        .carrito { text-align: right; margin-bottom: 15px; }
        .carrito a { color: #e91e63; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">

    <div class="carrito">
        🛒 <a href="carrito.php">Ver Carrito</a>
    </div>

    <h1>Accesorios Disponibles</h1>

    <div class="productos">
        <?php foreach ($productos as $id => $p): ?>
        <div class="card">
            <img src="<?= $p['img'] ?>" alt="">
            <h3><?= $p['nombre'] ?></h3>
            <p><strong>$<?= number_format($p['precio']) ?></strong></p>

            <button class="btn" onclick="window.location.href='accesorios.php?add=<?= $id ?>'">
                Agregar al Carrito
            </button>
        </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>
