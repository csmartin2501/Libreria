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
    <title>Mi Perfil - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5">
        <h2 class="text-center mb-4">Mi Perfil</h2>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Información Personal</h5>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Nombre:</strong></label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Email:</strong></label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['email']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Dirección:</strong></label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['direccion'] ?? 'No especificada'); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Teléfono:</strong></label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($usuario['telefono'] ?? 'No especificado'); ?></p>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="catalogo.php" class="btn btn-primary">Ver Catálogo</a>
                            <a href="mis_pedidos.php" class="btn btn-outline-primary">Mis Pedidos</a>
                            <a href="carrito.php" class="btn btn-outline-secondary">Mi Carrito</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>

</html>
