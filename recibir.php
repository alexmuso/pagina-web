<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $lugar = $_POST['lugar'];
    $descripcion = $_POST['descripcion'];

    try {
        $sql = "INSERT INTO citas (documento, nombre, apellido, correo, telefono, lugar, descripcion)
                VALUES (:documento, :nombre, :apellido, :correo, :telefono, :lugar, :descripcion)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':descripcion', $descripcion);

        $stmt->execute();

        echo "
        <html lang='es'>
        <head><meta charset='UTF-8'><title>Cita registrada</title>
        <style>
          body{font-family:Arial;background:#f5f5f5;text-align:center;padding:50px;}
          .ok{background:white;padding:30px;border-radius:15px;display:inline-block;}
          a{display:inline-block;margin-top:20px;color:#e91e63;text-decoration:none;}
        </style></head>
        <body>
        <div class='ok'>
          <h2>🎉 Cita registrada exitosamente</h2>
          <p>Gracias <b>$nombre $apellido</b>, te contactaremos pronto.</p>
          <a href='index.php'>Volver al inicio</a>
        </div>
        </body></html>
        ";
    } catch (PDOException $e) {
        echo "❌ Error al guardar la cita: " . $e->getMessage();
    }
} else {
    echo "⚠️ No se enviaron datos.";
}
?>
