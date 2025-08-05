<?php
session_start();
require_once 'includes/auth.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener usuario actual
$usuario = getCurrentUser();

// Obtener pedidos del usuario
try {
    $stmt = $pdo->prepare("
        SELECT p.*, COUNT(dp.id) as total_items 
        FROM pedidos p 
        LEFT JOIN detalles_pedido dp ON p.id = dp.pedido_id 
        WHERE p.usuario_id = ? 
        GROUP BY p.id 
        ORDER BY p.fecha_pedido DESC
    ");
    $stmt->execute([$usuario['id']]);
    $pedidos = $stmt->fetchAll();
} catch (PDOException $e) {
    $pedidos = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Pedidos - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5">
        <h2 class="text-center mb-4">Mis Pedidos</h2>

        <?php if (empty($pedidos)): ?>
            <div class="text-center">
                <h4>No tienes pedidos aún</h4>
                <p class="text-muted">¡Explora nuestro catálogo y realiza tu primera compra!</p>
                <a href="catalogo.php" class="btn btn-primary">Ver Catálogo</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?>
                                </h5>
                                <p class="card-text">
                                    <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?><br>
                                    <strong>Estado:</strong> 
                                    <?php
                                    $estado_class = '';
                                    switch($pedido['estado']) {
                                        case 'pendiente':
                                            $estado_class = 'bg-warning';
                                            break;
                                        case 'procesando':
                                            $estado_class = 'bg-info';
                                            break;
                                        case 'enviado':
                                            $estado_class = 'bg-primary';
                                            break;
                                        case 'entregado':
                                            $estado_class = 'bg-success';
                                            break;
                                        case 'cancelado':
                                            $estado_class = 'bg-danger';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $estado_class; ?>">
                                        <?php echo ucfirst($pedido['estado']); ?>
                                    </span>
                                </p>
                                <p class="card-text">
                                    <strong>Items:</strong> <?php echo $pedido['total_items']; ?><br>
                                    <strong>Total:</strong> $<?php echo number_format($pedido['total'], 0, ',', '.'); ?>
                                </p>
                                <a href="detalle_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>

</html>
