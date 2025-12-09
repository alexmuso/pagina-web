<?php
session_start();
require_once "conexion.php";

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $lugar = $_POST['lugar'];
    $descripcion = $_POST['descripcion'];
    $servicio = $_POST['servicio'];

    try {
        $sql = "INSERT INTO citas (usuario_id, documento, nombre, apellido, correo, telefono, lugar, descripcion, servicio) 
                VALUES (:usuario_id, :documento, :nombre, :apellido, :correo, :telefono, :lugar, :descripcion, :servicio)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':servicio', $servicio);
        $stmt->execute();

        $mensaje_exito = "Cita agendada con éxito. Pronto te contactaremos.";
        
    } catch(PDOException $e) {
        $error = "Error al agendar cita: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<!-- ICONOS FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
  color: #444;
  margin: 0;
  flex-grow: 1;
  font-weight: normal;
}

/* ===== NAV ===== */
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
  padding-top: 120px;
}

/* ===== FORM ===== */
form {
  background: white;
  padding: 20px;
  width: 350px;
  border-radius: 8px;
  box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
  margin: 40px auto;
  text-align: center;
}

.logo {
  width: 100px;
  margin-bottom: 15px;
}

form input, 
form select, 
form textarea {
  width: 90%;
  padding: 8px;
  margin: 8px 0;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 0.9em;
}

form button {
  background: #e91e63;
  color: white;
  padding: 8px 14px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  font-size: 0.9em;
  margin-top: 10px;
}

form button:hover {
  background: #c2185b;
}

/* Enlaces dentro del formulario */
form a {
  color: #e91e63;
  text-decoration: none;
}

/* ===== FOOTER ===== */
footer {
  width: 100%;
  background: #222;
  color: white;
  text-align: center;
  padding: 20px;
  margin-top: 40px;
}

footer i {
  margin-right: 8px;
  color: #e91e63;
}

</style>
</head>
<body>

<header>
  <img src="img/logo.jpeg" alt="Logo Boutique">
  <h1>Arreglos y Confecciones Felicias</h1>

  <nav>
    <ul>
      <li><a href="index.php#inicio">Inicio</a></li>
      <li><a href="index.php#servicios">Servicios</a></li>
      <li><a href="#contacto">Contacto</a></li>
      <li><a href="login.php">Inicio de Sesión</a></li>
    </ul>
  </nav>
</header>

<form method="post" action="formulario.php">
  <img src="img/logo.jpeg" alt="Logo Boutique" class="logo">
  <h2>Agendar Cita</h2>

  <input type="text" name="documento" placeholder="Número de documento" required>
  <input type="text" name="nombre" placeholder="Nombre" required>
  <input type="text" name="apellido" placeholder="Apellido" required>
  <input type="email" name="correo" placeholder="Correo electrónico" required>
  <input type="tel" name="telefono" placeholder="Teléfono" required>

  <label>¿Dónde quieres tu cita?</label>
  <select name="lugar">
    <option value="local">En el local</option>
    <option value="domicilio">A domicilio</option>
  </select>

  <label>Servicio</label>
  <select name="servicio">
    <option value="arreglo">Arreglo</option>
    <option value="confeccion">Confección</option>
  </select>

  <textarea name="descripcion" placeholder="Descripción de lo que necesitas"></textarea>

  <button type="submit">Enviar</button>

  <br><br>
  <a href="login.php">Iniciar Sesión</a> |
  <a href="index.php">Volver al inicio</a>

  <?php if (!empty($mensaje_exito)) echo "<p style='color: green;'>$mensaje_exito</p>"; ?>
  <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
</form>

<footer id="contacto">
  <p><i class="fa-solid fa-location-dot"></i> Calle 123, Bogotá</p>
  <p><i class="fa-solid fa-phone"></i> +57 300 123 4567</p>
  <p><i class="fa-solid fa-envelope"></i> contacto@mi-boutique.com</p>
</footer>

</body>
</html>
