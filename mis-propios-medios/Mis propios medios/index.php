<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boutique Elegante</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" href="img/logo.jpeg" type="image/x-icon">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <header>
    <img src="img/logo.jpeg" alt="Logo Boutique">
    <h1>Arreglos y confecciones Felicias</h1>
    <nav>
      <ul>
        <li><a href="login.php">Administrador</a></li>
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#servicios">Servicios</a></li>
        <li><a href="#contacto">Contacto</a></li>
      </ul>
    </nav>
  </header>

  <section class="banner" id="inicio">
    <h2>Bienvenidos</h2>
    <p><em>Tu espacio de confianza para transformar, confeccionar y dar vida a tus prendas con un servicio hecho a tu medida.</em></p>
  </section>

  <section class="servicios" id="servicios">
    <div class="card">
      <i class="fa-solid fa-scissors"></i>
      <h3>Arreglos</h3>
      <p>Ajustamos tus prendas para que luzcan perfectas.</p>
      <button onclick="window.location.href='formulario.php'">Agendar</button>
    </div>
    <div class="card">
      <i class="fa-solid fa-shirt"></i>
      <h3>Confección</h3>
      <p>Diseños personalizados hechos a tu medida.</p>
      <button onclick="window.location.href='formulario.php'">Agendar</button>
    </div>
    <div class="card">
      <i class="fa-solid fa-gem"></i>
      <h3>Accesorios</h3>
      <p>Bolsos y detalles únicos que acompañan tu estilo.</p>
      <button onclick="window.location.href='catalogo_accesorios.php'">Ver catálogo</button>
    </div>
  </section>

  <footer id="contacto">
    <p><i class="fa-solid fa-location-dot"></i> Calle 123, Bogotá</p>
    <p><i class="fa-solid fa-phone"></i> +57 300 123 4567</p>
    <p><i class="fa-solid fa-envelope"></i> contacto@mi-boutique.com</p>
  </footer>
</body>
</html>
