<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendar Cita - Boutique</title>
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

  <form method="post" action="recibir.php">
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

  <textarea name="descripcion" placeholder="Descripción de lo que necesitas"></textarea>

  <button type="submit">Enviar</button>
  <br> <br>
  <li><a href="index.php">Volver</a></li>

</form>


</body>
</html>
