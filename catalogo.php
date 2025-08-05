<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/carrito.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener usuario actual
$usuario = getCurrentUser();

// Obtener libros disponibles
try {
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE cantidad_inventario > 0 ORDER BY titulo ASC");
    $stmt->execute();
    $libros = $stmt->fetchAll();
} catch (PDOException $e) {
    $libros = [];
}

// Procesar agregar al carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_carrito'])) {
    $libro_id = (int)$_POST['libro_id'];
    $cantidad = (int)$_POST['cantidad'];
    
    $resultado = agregarAlCarrito($usuario['id'], $libro_id, $cantidad);
    
    if ($resultado['success']) {
        header('Location: catalogo.php?success=' . urlencode($resultado['message']));
    } else {
        header('Location: catalogo.php?error=' . urlencode($resultado['message']));
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5">
        <h2 class="text-center mb-4">Catálogo de Libros</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($libros)): ?>
                <div class="col-12 text-center">
                    <p class="lead">No hay libros disponibles en este momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($libros as $libro): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($libro['titulo']); ?></h5>
                                <p class="card-text text-muted">
                                    <strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?>
                                </p>
                                <?php if (!empty($libro['descripcion'])): ?>
                                    <p class="card-text"><?php echo htmlspecialchars($libro['descripcion']); ?></p>
                                <?php endif; ?>
                                <div class="mt-auto">
                                    <p class="card-text">
                                        <strong>Precio:</strong> $<?php echo number_format($libro['precio'], 0, ',', '.'); ?>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Stock disponible: <?php echo $libro['cantidad_inventario']; ?>
                                        </small>
                                    </p>
                                    
                                    <form method="POST" class="d-flex gap-2">
                                        <input type="hidden" name="libro_id" value="<?php echo $libro['id']; ?>">
                                        <input type="number" name="cantidad" class="form-control form-control-sm" 
                                               value="1" min="1" max="<?php echo $libro['cantidad_inventario']; ?>" 
                                               style="width: 80px;">
                                        <button type="submit" name="agregar_carrito" class="btn btn-primary btn-sm flex-grow-1">
                                            Agregar al Carrito
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>

</html>
