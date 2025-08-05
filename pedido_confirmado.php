<?php
session_start();
require_once 'includes/auth.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener usuario actual
$usuario = getCurrentUser();

// Verificar que se haya proporcionado un ID de pedido
if (!isset($_GET['pedido_id']) || empty($_GET['pedido_id'])) {
    header('Location: carrito.php');
    exit();
}

$pedido_id = (int)$_GET['pedido_id'];

// Obtener información del pedido
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.nombre as usuario_nombre, u.email 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        WHERE p.id = ? AND p.usuario_id = ?
    ");
    $stmt->execute([$pedido_id, $usuario['id']]);
    $pedido = $stmt->fetch();
    
    if (!$pedido) {
        header('Location: carrito.php?error=' . urlencode('Pedido no encontrado'));
        exit();
    }
    
    // Obtener detalles del pedido
    $stmt = $pdo->prepare("
        SELECT dp.*, l.titulo, l.autor 
        FROM detalles_pedido dp 
        JOIN libros l ON dp.libro_id = l.id 
        WHERE dp.pedido_id = ?
    ");
    $stmt->execute([$pedido_id]);
    $detalles = $stmt->fetchAll();
    
} catch (PDOException $e) {
    header('Location: carrito.php?error=' . urlencode('Error al obtener información del pedido'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido Confirmado - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="text-success mb-3">
                                <svg width="64" height="64" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </div>
                            <h2 class="text-success">¡Pedido Confirmado!</h2>
                            <p class="lead">Tu pedido ha sido registrado exitosamente</p>
                        </div>

                        <div class="row text-start">
                            <div class="col-md-6">
                                <h5>Información del Pedido</h5>
                                <p><strong>Número de Pedido:</strong> #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                <p><strong>Estado:</strong> <span class="badge bg-warning">Pendiente</span></p>
                                <p><strong>Total:</strong> $<?php echo number_format($pedido['total'], 0, ',', '.'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Información de Envío</h5>
                                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['usuario_nombre']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['email']); ?></p>
                                <p><strong>Dirección:</strong><br><?php echo nl2br(htmlspecialchars($pedido['direccion_envio'])); ?></p>
                            </div>
                        </div>

                        <hr>

                        <h5 class="text-start">Productos Ordenados</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detalles as $detalle): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($detalle['titulo']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($detalle['autor']); ?></small>
                                            </td>
                                            <td><?php echo $detalle['cantidad']; ?></td>
                                            <td>$<?php echo number_format($detalle['precio_unitario'], 0, ',', '.'); ?></td>
                                            <td>$<?php echo number_format($detalle['subtotal'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th>$<?php echo number_format($pedido['total'], 0, ',', '.'); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <hr>

                        <div class="alert alert-info">
                            <h6>¿Qué sigue?</h6>
                            <ul class="mb-0 text-start">
                                <li>Recibirás un email de confirmación con los detalles de tu pedido</li>
                                <li>Procesaremos tu pedido en las próximas 24 horas</li>
                                <li>Te contactaremos para coordinar la entrega</li>
                                <li>El pago se realizará contra entrega</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2 justify-content-center">
                            <a href="catalogo.php" class="btn btn-primary">Seguir Comprando</a>
                            <a href="mis_pedidos.php" class="btn btn-outline-primary">Ver Mis Pedidos</a>
                            <button onclick="window.print()" class="btn btn-outline-secondary">Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>

</html>
