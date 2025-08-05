<?php 
session_start(); 
require_once 'includes/auth.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener usuario actual
$usuario = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrar Libro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="js/validacion_registro_libro.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
  <?php include 'partials/nav.php'; ?>

  <main class="container flex-grow-1 py-5">
    <h2 class="mb-4 text-center">Registro de Libros</h2>
    
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger" style="max-width: 500px; margin: 0 auto;">
        <?php echo htmlspecialchars($_GET['error']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success" style="max-width: 500px; margin: 0 auto;">
        <?php echo htmlspecialchars($_GET['success']); ?>
      </div>
    <?php endif; ?>
    
    <form id="bookForm" action="procesar_libro.php" method="POST" class="needs-validation mx-auto" novalidate style="max-width: 500px;">
      <div class="mb-3">
        <label for="titulo" class="form-label">Título</label>
        <input type="text" class="form-control" id="titulo" name="titulo" required>
        <div class="invalid-feedback">Ingresa el título del libro.</div>
      </div>

      <div class="mb-3">
        <label for="autor" class="form-label">Autor</label>
        <input type="text" class="form-control" id="autor" name="autor" required>
        <div class="invalid-feedback">Ingresa el autor del libro.</div>
      </div>

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                  placeholder="Breve descripción del libro (opcional)"></textarea>
      </div>

      <div class="mb-3">
        <label for="precio" class="form-label">Precio (CLP)</label>
        <input 
        type="number" 
        step="0.01" 
        class="form-control" 
        id="precio" 
        name="precio" 
        min="0.01" 
        required>
        <div class="invalid-feedback">Ingresa un precio válido.</div>
      </div>

      <div class="mb-3">
        <label for="cantidad" class="form-label">Cantidad en Inventario</label>
        <input type="number" class="form-control" id="cantidad" name="cantidad" min="0" required>
        <div class="invalid-feedback">Ingresa la cantidad disponible.</div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Registrar Libro</button>
    </form>
  </main>

  <?php include 'partials/footer.php'; ?>

</body>
</html>
