<?php
session_start();
require_once 'includes/auth.php';

// Verificar que el usuario esté logueado
requireLogin();

// procesar_libro.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: registro_libro.php');
  exit;
}

// Recuperación segura de los campos
$data = filter_input_array(INPUT_POST, [
  'titulo'   => FILTER_SANITIZE_SPECIAL_CHARS,
  'autor'    => FILTER_SANITIZE_SPECIAL_CHARS,
  'precio'   => ['filter' => FILTER_VALIDATE_FLOAT],
  'cantidad' => ['filter' => FILTER_VALIDATE_INT],
  'descripcion' => FILTER_SANITIZE_SPECIAL_CHARS
]);

// Errores de validación
$errors = [];
if (empty($data['titulo']))   $errors[] = 'Falta el título.';
if (empty($data['autor']))    $errors[] = 'Falta el autor.';
if ($data['precio'] === false || $data['precio'] <= 0)   $errors[] = 'Precio inválido.';
if ($data['cantidad'] === false || $data['cantidad'] < 0) $errors[] = 'Cantidad inválida.';

if ($errors) {
  $error_msg = implode('<br>', $errors);
  header("Location: registro_libro.php?error=" . urlencode($error_msg));
  exit;
}

// Insertar en la base de datos usando PDO
try {
  $stmt = $pdo->prepare("INSERT INTO libros (titulo, autor, precio, cantidad_inventario, descripcion) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([
    $data['titulo'],
    $data['autor'],
    $data['precio'],
    $data['cantidad'],
    $data['descripcion'] ?? ''
  ]);
  
  header('Location: registro_libro.php?success=' . urlencode('Libro registrado exitosamente'));
} catch (PDOException $e) {
  header('Location: registro_libro.php?error=' . urlencode('Error al registrar el libro: ' . $e->getMessage()));
}
exit;
?>
