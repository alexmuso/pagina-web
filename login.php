<?php
session_start();
require_once "conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_input = $_POST['usuario'];
    $clave_input = $_POST['clave'];
    $error = "Usuario o contraseña incorrectos";

    
    $sql = "SELECT id, usuario, clave, rol FROM usuarios WHERE usuario = :usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario', $usuario_input);
    $stmt->execute();
    
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Verificar la contraseña usando password_verify
        if (password_verify($clave_input, $user['clave'])) {
            // Contraseña correcta
            if ($user['rol'] == 'admin') {
                $_SESSION['admin'] = $user['usuario'];  
                header("Location: admin_dashboard.php"); // Necesitarás crear este archivo
            } else {
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['usuario_id'] = $user['id'];
                header("Location: mis_citas_pedidos.php"); // Redirige al inicio del cliente
            }
            exit;
        }
    }
    // Si la verificación falla o el usuario no existe, se muestra el error.
    $error = "Usuario o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión - Boutique</title>
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
  <form method="POST" action="login.php">
    <h2>🔐 Iniciar Sesión</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <input type="text" name="usuario" placeholder="Usuario" required><br>
    <input type="password" name="clave" placeholder="Contraseña" required><br>
    <button type="submit">Entrar</button>
    <br>
    <br>
    <a href='registro.php'>¿No tienes cuenta? Regístrate aquí</a>
    <br>
    <a href='index.php'>Volver al inicio</a>
</form>
</body>
</html>