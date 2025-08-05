<?php
session_start();
require_once 'includes/auth.php';

// procesar_registro.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: registro_usuario.php');
  exit;
}

// Recoger y sanear
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email = trim($_POST['email'] ?? '');
$pwd = $_POST['password'] ?? '';
$cpwd = $_POST['confirm_password'] ?? '';
$direccion = trim($_POST['direccion'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

// Validaciones servidor-side
$errors = [];
if ($nombre === '') $errors[] = 'Nombre requerido.';
if ($apellido === '') $errors[] = 'Apellido requerido.';
if (!validarEmail($email)) $errors[] = 'Email inválido.';
if (!validarPassword($pwd)) $errors[] = 'La contraseña debe tener mínimo 8 caracteres, con al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo.';
if ($pwd !== $cpwd) $errors[] = 'Contraseñas no coinciden.';
if ($direccion === '') $errors[] = 'Dirección requerida.';
if ($telefono === '') $errors[] = 'Teléfono requerido.';

if ($errors) {
  $error_msg = implode('<br>', $errors);
  header("Location: registro_usuario.php?error=" . urlencode($error_msg));
  exit;
}

// Registrar usuario
$nombre_completo = $nombre . ' ' . $apellido;
$resultado = registrarUsuario($nombre_completo, $email, $pwd, $direccion, $telefono);

if ($resultado['success']) {
  header('Location: index.php?success=' . urlencode($resultado['message']));
} else {
  header('Location: registro_usuario.php?error=' . urlencode($resultado['message']));
}
exit;
?>
