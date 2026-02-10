<?php
session_start();

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

try {
    $conn = new PDO("mysql:host=localhost;dbname=boutique;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($clave, $data['clave'])) {

            $_SESSION['usuario'] = $data['usuario'];
            $_SESSION['rol'] = $data['rol'];

            // Redirecciones por rol
            if ($data['rol'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else if ($data['rol'] == 'cliente') {
                header("Location: mis_citas_pedidos.php");
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location='login.php';</script>";
        exit();
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
