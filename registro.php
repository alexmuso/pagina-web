
<?php
session_start();
require_once "conexion.php";

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT); // Usa hashing seguro
    $rol = 'cliente';

    try {
        $sql = "INSERT INTO usuarios (nombre, usuario, correo, clave, rol) VALUES (:nombre, :usuario, :correo, :clave, :rol)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':clave', $clave);
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();
        
        $_SESSION['usuario'] = $usuario; // Inicia sesión automáticamente
        header("Location: index.php");
        exit;
        
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "El usuario o correo ya existen. Intenta con otros datos.";
        } else {
            $error = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Boutique</title>
  <style> 
   body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
        background: url("img/patron.jfif") no-repeat center center fixed;
      background-size: cover;
    }
    form {
      background: white;
      padding: 20px;
      width: 350px;
      border-radius: 8px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
      text-align: center;
    }
    form h2 {
      margin-bottom: 15px;
    }
  .logo {
      width: 100px;
      margin-bottom: 15px;
    }
    form input, form select, form textarea {
      width: 90%;
      padding: 8px;
      margin: 8px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 0.9em;
    }
    form button {
      background: #222;
      color: white;
      padding: 8px 14px;
      border: none;
      cursor: pointer;
      border-radius: 5px;
      font-size: 0.9em;
      margin-top: 10px;
    }
    form button:hover {
      background: #444;
    }
  </style>
</head>
<body>
  <form method="POST" action="">
    <h2>Crear Cuenta</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <input type="text" name="nombre" placeholder="Nombre completo" required>
    <input type="text" name="usuario" placeholder="Nombre de usuario" required>
    <input type="email" name="correo" placeholder="Correo electrónico" required>
    <input type="password" name="clave" placeholder="Contraseña" required>
    <button type="submit">Registrarse</button>
    <br><br>
    <a href='login.php'>¿Ya tienes cuenta? Inicia sesión</a><br>
     <a href='index.php'>Volver al inicio</a>
  </form>
</body>
</html>