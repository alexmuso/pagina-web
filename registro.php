<?php
session_start();
require_once "conexion.php";

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = 'cliente';

    try {
        $sql = "INSERT INTO usuarios (nombre, usuario, correo, clave, rol) 
                VALUES (:nombre, :usuario, :correo, :clave, :rol)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':clave', $clave);
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();

        $_SESSION['usuario'] = $usuario;
        header("Location: index.php");
        exit;
        
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "El usuario o correo ya existen.";
        } else {
            $error = "Error: " . $e->getMessage();
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

    /* ===== HEADER ===== */
    header {
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      background: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000;
    }

    header img {
      height: 60px;
      margin-right: 15px;
    }

    header h1 {
      font-family: "Brush Script MT", cursive;
      font-size: 28px;
      font-weight: normal;
      color: #444;
      flex-grow: 1;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 25px;
      margin: 0;
      padding: 0;
    }

    nav a {
      text-decoration: none;
      color: #444;
      font-size: 16px;
      transition: 0.3s;
    }

    nav a:hover {
      color: #e91e63;
    }

    /* ===== BODY ===== */
    body {
      font-family: Arial, sans-serif;
      background: url("img/patron.jfif") no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      padding-top: 150px; 
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    /* FORMULARIO */
    form {
      background: white;
      padding: 25px;
      width: 380px;
      border-radius: 8px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.25);
      text-align: center;
    }

    form input {
      width: 90%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    form button {
      background: #222;
      color: white;
      padding: 10px 18px;
      border: none;
      cursor: pointer;
      border-radius: 6px;
      font-size: 15px;
      margin-top: 10px;
      width: 95%;
    }

    form button:hover {
      background: #444;
    }

    /* ===== FOOTER ===== */
    footer {
      background: #222;
      color: white;
      text-align: center;
      padding: 25px;
      margin-top: 60px;
      position: fixed;
      bottom: 0;
      width: 100%;
    }

    footer i {
      color: #e91e63;
      margin-right: 6px;
    }

  </style>
</head>
<body>

  <!-- HEADER -->
  <header>
    <img src="img/logo.jpeg" alt="Logo Boutique">
    <h1>Arreglos y Confecciones Felicias</h1>

    <nav>
      <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="productos.php">Productos</a></li>
        <li><a href="contacto.php">Contacto</a></li>
        <li><a href="login.php">Iniciar Sesión</a></li>
      </ul>
    </nav>
  </header>

  <!-- FORMULARIO -->
  <form method="POST" action="">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <input type="text" name="nombre" placeholder="Nombre completo" required>
    <input type="text" name="usuario" placeholder="Nombre de usuario" required>
    <input type="email" name="correo" placeholder="Correo electrónico" required>
    <input type="password" name="clave" placeholder="Contraseña" required>

    <button type="submit">Registrarse</button>

    <br><br>
    <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a><br>
    <a href="index.php">Volver al inicio</a>
  </form>

  <!-- FOOTER -->
  <footer>
    <p><i class="fa fa-map-marker"></i> Calle 123, Bogotá</p>
    <p><i class="fa fa-phone"></i> +57 300 123 4567</p>
    <p><i class="fa fa-envelope"></i> contacto@mi-boutique.com</p>
  </footer>

</body>
</html>
