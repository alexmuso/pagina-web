<?php
session_start();
require_once "conexion.php";

// Verificar si el usuario ha iniciado sesión, si no, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    // ... (recuperar los demás campos del formulario) ...
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $lugar = $_POST['lugar'];
    $descripcion = $_POST['descripcion'];
    $servicio = $_POST['servicio'];
    // El estado inicial es 'pendiente' por defecto en la BD

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
      background: #e91e63;
    }
  </style>
</head>
<body>
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
    <option value="confeccion">Confeccion</option>
  </select>

  <textarea name="descripcion" placeholder="Descripción de lo que necesitas"></textarea>

  <button type="submit">Enviar</button>
  <br> <br>
  <a href="login.php">Iniciar Sesión</a><br><br>
  <li><a href="index.php">Volver</a></li> <!-- ... (Mantén tus campos del formulario HTML originales aquí) ... -->
  <?php if (!empty($mensaje_exito)) echo "<p style='color: green;'>$mensaje_exito</p>"; ?>
  <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
  </form>
</body>
</html>