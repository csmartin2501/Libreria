<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/carrito.php';

// Asegurar que PDO esté disponible globalmente
global $pdo;

// Verificar que el usuario esté logueado
requireLogin();

// Obtener usuario actual
$usuario = getCurrentUser();

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['actualizar_cantidad'])) {
        $carrito_id = (int)$_POST['carrito_id'];
        $nueva_cantidad = (int)$_POST['cantidad'];
        
        $resultado = actualizarCantidadCarrito($carrito_id, $nueva_cantidad, $usuario['id']);
        
        if ($resultado['success']) {
            header('Location: carrito.php?success=' . urlencode($resultado['message']));
        } else {
            header('Location: carrito.php?error=' . urlencode($resultado['message']));
        }
        exit();
    }
    
    if (isset($_POST['eliminar_item'])) {
        $carrito_id = (int)$_POST['carrito_id'];
        
        $resultado = eliminarDelCarrito($carrito_id, $usuario['id']);
        
        if ($resultado['success']) {
            header('Location: carrito.php?success=' . urlencode($resultado['message']));
        } else {
            header('Location: carrito.php?error=' . urlencode($resultado['message']));
        }
        exit();
    }
    
    if (isset($_POST['vaciar_carrito'])) {
        $resultado = vaciarCarrito($usuario['id']);
        
        if ($resultado['success']) {
            header('Location: carrito.php?success=' . urlencode($resultado['message']));
        } else {
            header('Location: carrito.php?error=' . urlencode($resultado['message']));
        }
        exit();
    }
}

// Obtener items del carrito directamente
try {
    global $pdo;
    
    // Verificar que PDO esté inicializado
    if (!isset($pdo)) {
        throw new Exception("Error: Conexión a base de datos no disponible");
    }
    
    // Verificar que la tabla carrito existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'carrito'");
    if ($stmt->rowCount() == 0) {
        throw new Exception("La tabla 'carrito' no existe. <a href='install.php'>Ejecutar instalación</a>");
    }
    
    // Obtener items del carrito
    $stmt = $pdo->prepare("
        SELECT c.id, c.cantidad, c.monto_total, 
               l.titulo, l.autor, l.precio 
        FROM carrito c 
        JOIN libros l ON c.libro_id = l.id 
        WHERE c.usuario_id = ? 
        ORDER BY c.fecha_agregado DESC
    ");
    $stmt->execute([$usuario['id']]);
    $items_carrito_array = $stmt->fetchAll();
    
    // Obtener total del carrito
    $stmt = $pdo->prepare("SELECT SUM(monto_total) as total FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$usuario['id']]);
    $result = $stmt->fetch();
    $total_carrito = $result['total'] ?? 0;

    
} catch (Exception $e) {
    // En caso de error, mostrar mensaje y establecer valores por defecto
    $error_db = $e->getMessage();
    $items_carrito_array = [];
    $total_carrito = 0;
}

// Asegurar que los valores sean del tipo correcto
if (!is_array($items_carrito_array)) {
    $items_carrito_array = [];
}
if (!is_numeric($total_carrito)) {
    $total_carrito = 0;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Carrito - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5">
        <h2 class="text-center mb-4">Mi Carrito de Compras</h2>

        <?php if (isset($error_db)): ?>
            <div class="alert alert-danger">
                <h5>Error de Base de Datos</h5>
                <p><?php echo $error_db; ?></p>
            </div>
        <?php endif; ?>

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
        
        <?php if (empty($items_carrito_array)): ?>
            <div class="text-center">
                <h4>Tu carrito está vacío</h4>
                <p class="text-muted">¡Explora nuestro catálogo y agrega algunos libros!</p>
                <a href="catalogo.php" class="btn btn-primary">Ver Catálogo</a>
            </div>
        <?php else: ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Productos en tu carrito</h5>
                            <form method="POST" class="d-inline">
                                <button type="submit" name="vaciar_carrito" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('¿Estás seguro de que quieres vaciar el carrito?')">
                                    Vaciar Carrito
                                </button>
                            </form>
                        </div>
                        <div class="card-body">
                            <?php foreach ($items_carrito_array as $item): ?>
                                <div class="row border-bottom py-3">
                                    <div class="col-md-8">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['titulo']); ?></h6>
                                        <p class="text-muted mb-1">
                                            <strong>Autor:</strong> <?php echo htmlspecialchars($item['autor']); ?>
                                        </p>
                                        <p class="text-muted mb-0">
                                            <strong>Precio unitario:</strong> $<?php echo number_format($item['precio'], 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <form method="POST" class="d-flex align-items-center gap-2 flex-grow-1">
                                                <input type="hidden" name="carrito_id" value="<?php echo $item['id']; ?>">
                                                <label class="form-label mb-0 small">Cantidad:</label>
                                                <input type="number" name="cantidad" class="form-control form-control-sm" 
                                                       value="<?php echo $item['cantidad']; ?>" min="0" max="50" 
                                                       style="width: 80px;">
                                                <button type="submit" name="actualizar_cantidad" class="btn btn-outline-primary btn-sm">
                                                    Actualizar
                                                </button>
                                            </form>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>$<?php echo number_format($item['monto_total'], 0, ',', '.'); ?></strong>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="carrito_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" name="eliminar_item" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('¿Eliminar este producto del carrito?')">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Resumen del Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Total de productos:</span>
                                <span><?php echo count($items_carrito_array); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($total_carrito, 0, ',', '.'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Envío:</span>
                                <span class="text-success">Gratis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>$<?php echo number_format($total_carrito, 0, ',', '.'); ?></strong>
                            </div>
                            <a href="checkout.php" class="btn btn-success w-100">
                                Proceder al Pago
                            </a>
                            <a href="catalogo.php" class="btn btn-outline-primary w-100 mt-2">
                                Seguir Comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>

</html>
