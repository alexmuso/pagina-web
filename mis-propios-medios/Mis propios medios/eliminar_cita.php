<?php
require_once 'auth.php';
require_admin_session();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo 'Token CSRF inválido.';
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id !== false && $id !== null) {
    $sql = 'DELETE FROM citas WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: admin.php');
exit;
?>
