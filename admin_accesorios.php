<?php
session_start();

if (!isset($_SESSION['rol'])) {
    echo "Acceso denegado";
    exit;
}


if ($_SESSION['rol'] !== 'admin') {
    echo "Acceso denegado";
    exit;
}
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];

    $imagen = "uploads/" . basename($_FILES["imagen"]["name"]);
    move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen);

    $sql = "INSERT INTO accesorios(nombre, precio, stock, imagen) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nombre, $precio, $stock, $imagen]);

    $msg = "Accesorio agregado con éxito";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Panel Admin - Accesorios</title>
<style>
form { width: 400px; margin:auto; background:white; padding:20px;
       border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.3); }
input { width:100%; padding:10px; margin-bottom:10px;}
button { background:#e91e63; color:white; padding:10px; border:none; width:100%; }
</style>
</head>
<body>

<h2 style="text-align:center;">Administrador - Agregar Accesorio</h2>

<?php if(isset($msg)) echo "<p style='text-align:center; color:green;'>$msg</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="number" name="precio" placeholder="Precio" required>
    <input type="number" name="stock" placeholder="Stock" required>
    <input type="file" name="imagen" required>
    <button type="submit">Guardar</button>
</form>

</body>
</html>

