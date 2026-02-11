<?php
require_once 'auth.php';
ensure_session_started();

$errores = $_SESSION['form_errors'] ?? [];
$success = $_SESSION['success_message'] ?? '';
$old = $_SESSION['old_form'] ?? [];
$csrf = csrf_token();
unset($_SESSION['form_errors'], $_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendar cita - Boutique</title>
  <link rel="stylesheet" href="css/formulario.css">
</head>
<body>
  <header class="site-header">
    <img src="img/logo.jpeg" alt="Logo Boutique">
    <h1>Arreglos y confecciones Felicias</h1>
    <nav><ul><li><a href="index.php">Inicio</a></li><li><a href="login.php">Iniciar sesión</a></li></ul></nav>
  </header>
  <main class="form-wrap">
  <form method="post" action="recibir.php">
    <img src="img/logo.jpeg" alt="Logo Boutique" class="logo">
    <h2>Agendar cita</h2>

    <?php if (!empty($success)): ?>
      <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

    <?php if (!empty($errores)): ?>
      <div class="errors">
        <?php foreach ($errores as $error): ?>
          <p><?= e($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <input type="text" name="documento" placeholder="Número de documento" value="<?= e((string)($old['documento'] ?? '')) ?>" required>
    <input type="text" name="nombre" placeholder="Nombre" value="<?= e((string)($old['nombre'] ?? '')) ?>" required>
    <input type="text" name="apellido" placeholder="Apellido" value="<?= e((string)($old['apellido'] ?? '')) ?>" required>
    <input type="email" name="correo" placeholder="Correo electrónico" value="<?= e((string)($old['correo'] ?? '')) ?>" required>
    <input type="password" name="clave" placeholder="Crea una contraseña para tu cuenta" minlength="6" value="<?= e((string)($old['clave'] ?? '')) ?>" required>
    <input type="tel" name="telefono" placeholder="Teléfono" value="<?= e((string)($old['telefono'] ?? '')) ?>" required>

    <label for="lugar">¿Dónde quieres tu cita?</label>
    <select name="lugar" id="lugar">
      <option value="local" <?= (($old['lugar'] ?? '') === 'local') ? 'selected' : '' ?>>En el local</option>
      <option value="domicilio" <?= (($old['lugar'] ?? '') === 'domicilio') ? 'selected' : '' ?>>A domicilio</option>
    </select>


    <label for="servicio">Servicio</label>
    <select name="servicio" id="servicio" required>
      <option value="arreglo" <?= (($old['servicio'] ?? '') === 'arreglo') ? 'selected' : '' ?>>Arreglo</option>
      <option value="confeccion" <?= (($old['servicio'] ?? '') === 'confeccion') ? 'selected' : '' ?>>Confección</option>
      <option value="accesorios" <?= (($old['servicio'] ?? '') === 'accesorios') ? 'selected' : '' ?>>Accesorios</option>
    </select>

    <textarea name="descripcion" placeholder="Descripción de lo que necesitas"><?= e((string)($old['descripcion'] ?? '')) ?></textarea>


    <div class="cuenta-box"> 
      <label class="crear-cuenta"> 
        <input type="checkbox" name="crear_usuario" value="1" <?= (($old['crear_usuario'] ?? '1') === '1') ? 'checked' : '' ?>>
        Crear usuario automáticamente con estos datos
      </label>
      <p class="cuenta-ayuda">Si ya tienes cuenta, puedes iniciar sesión y continuar.</p>
      <a class="ir-login" href="login.php?tipo=usuario">Iniciar sesión</a>
    </div>
    <button type="submit">Enviar</button>
    <a class="volver" href="index.php">Volver</a>
  </form>
  </main>
  <footer class="site-footer" id="contacto">
    <p> <i>📍</i> Calle 123, Bogotá</p>
    <p> <i>📞</i> +57 300 123 4567</p>
    <p> <i>✉️</i> contacto@mi-boutique.com</p>
  </footer>
</body>
</html>