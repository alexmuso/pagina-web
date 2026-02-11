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
    <input type="tel" name="telefono" placeholder="Teléfono" value="<?= e((string)($old['telefono'] ?? '')) ?>" required>

    <label for="lugar">¿Dónde quieres tu cita?</label>
    <select name="lugar" id="lugar">
      <option value="local" <?= (($old['lugar'] ?? '') === 'local') ? 'selected' : '' ?>>En el local</option>
      <option value="domicilio" <?= (($old['lugar'] ?? '') === 'domicilio') ? 'selected' : '' ?>>A domicilio</option>
    </select>

    <textarea name="descripcion" placeholder="Descripción de lo que necesitas"><?= e((string)($old['descripcion'] ?? '')) ?></textarea>

    <button type="submit">Enviar</button>
    <a class="volver" href="index.php">Volver</a>
  </form>
</body>
</html>
