<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión – Boutique Elegante</title>
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
      font-weight: normal;
      color: #444;
      flex-grow: 1;
    }

    /* NAV */
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

    /* CONTENEDOR GENERAL */
    .contenedor {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }

    /* FORMULARIO */
    form {
      background: white;
      padding: 25px;
      width: 350px;
      border-radius: 8px;
      margin: 40px auto 120px auto; /* margen abajo para evitar que choque con el footer */
      text-align: center;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    }

    form h2 {
      margin-bottom: 15px;
    }

    .logo {
      width: 100px;
      margin-bottom: 15px;
    }

    form input {
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
      background: #d81758;
    }

    /* ===== FOOTER ===== */
    footer {
      width: 100%;
      background: #222;
      color: white;
      text-align: center;
      padding: 25px 15px;
      margin-top: auto; /* Hace que el footer se quede abajo */
      border-top: 3px solid #e91e63;
      font-size: 15px;
    }

    footer i {
      margin-right: 8px;
      color: #e91e63;
      font-size: 18px;
    }

    footer p {
      margin: 5px 0;
    }
  </style>
</head>

<body>

<header>
  <div style="display:flex; align-items:center;">
    <img src="img/logo.jpeg" alt="Logo">
    <h1>Arreglos y Confeciones Felicias</h1>
  </div>

  <nav>
    <ul>
      <li><a href="index.php">Inicio</a></li>
      <li><a href="productos.php">Productos</a></li>
      <li><a href="contacto.php">Contacto</a></li>
    </ul>
  </nav>
</header>

<div class="contenedor">

  <form action="validar.php" method="POST">
    <img src="img/logo.jpeg" class="logo">
    <h2>Iniciar Sesión</h2>

    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="clave" placeholder="Contraseña" required>

    <button type="submit">Entrar</button>
  </form>

    <footer id="contacto">
    <p><i class="fa-solid fa-location-dot"></i> Calle 123, Bogotá</p>
    <p><i class="fa-solid fa-phone"></i> +57 300 123 4567</p>
    <p><i class="fa-solid fa-envelope"></i> contacto@mi-boutique.com</p>
  </footer>

</div>

</body>
</html>

