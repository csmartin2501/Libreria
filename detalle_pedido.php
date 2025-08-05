<?php
session_start();
require_once 'includes/auth.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener usuario actual
$usuario = getCurrentUser();

// Obtener ID del pedido
$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$pedido_id) {
    header('Location: mis_pedidos.php?error=' . urlencode('ID de pedido no válido'));
    exit();
}

// Obtener detalles del pedido
try {
    global $pdo;
    
    // Verificar que el pedido pertenece al usuario actual
    $stmt = $pdo->prepare("
        SELECT p.*, COUNT(dp.id) as total_items 
        FROM pedidos p 
        LEFT JOIN detalles_pedido dp ON p.id = dp.pedido_id 
        WHERE p.id = ? AND p.usuario_id = ?
        GROUP BY p.id
    ");
    $stmt->execute([$pedido_id, $usuario['id']]);
    $pedido = $stmt->fetch();
    
    if (!$pedido) {
        header('Location: mis_pedidos.php?error=' . urlencode('Pedido no encontrado'));
        exit();
    }
    
    // Obtener items del pedido
    $stmt = $pdo->prepare("
        SELECT dp.*, l.titulo, l.autor 
        FROM detalles_pedido dp 
        JOIN libros l ON dp.libro_id = l.id 
        WHERE dp.pedido_id = ?
        ORDER BY dp.id
    ");
    $stmt->execute([$pedido_id]);
    $items_pedido = $stmt->fetchAll();
    
} catch (PDOException $e) {
    header('Location: mis_pedidos.php?error=' . urlencode('Error al obtener detalles del pedido'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle del Pedido - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detalle del Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></h2>
            <a href="mis_pedidos.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Mis Pedidos
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Información del Pedido -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Número de Pedido:</strong> #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                <p><strong>Estado:</strong> 
                                    <?php
                                    $estado_class = '';
                                    $estado_text = '';
                                    switch($pedido['estado']) {
                                        case 'pendiente':
                                            $estado_class = 'bg-warning text-dark';
                                            $estado_text = 'Pendiente';
                                            break;
                                        case 'procesando':
                                            $estado_class = 'bg-info text-white';
                                            $estado_text = 'Procesando';
                                            break;
                                        case 'enviado':
                                            $estado_class = 'bg-primary text-white';
                                            $estado_text = 'Enviado';
                                            break;
                                        case 'entregado':
                                            $estado_class = 'bg-success text-white';
                                            $estado_text = 'Entregado';
                                            break;
                                        case 'cancelado':
                                            $estado_class = 'bg-danger text-white';
                                            $estado_text = 'Cancelado';
                                            break;
                                        default:
                                            $estado_class = 'bg-secondary text-white';
                                            $estado_text = ucfirst($pedido['estado']);
                                    }
                                    ?>
                                    <span class="badge <?php echo $estado_class; ?> p-2">
                                        <?php echo $estado_text; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total de Items:</strong> <?php echo $pedido['total_items']; ?></p>
                                <p><strong>Total:</strong> <span class="h5 text-success">$<?php echo number_format($pedido['total'], 0, ',', '.'); ?></span></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6>Dirección de Envío:</h6>
                                <p class="text-muted"><?php echo nl2br(htmlspecialchars($pedido['direccion_envio'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items del Pedido -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Productos Ordenados</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($items_pedido)): ?>
                            <p class="text-muted">No se encontraron productos en este pedido.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Unit.</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items_pedido as $item): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['titulo']); ?></h6>
                                                        <small class="text-muted">por <?php echo htmlspecialchars($item['autor']); ?></small>
                                                    </div>
                                                </td>
                                                <td>$<?php echo number_format($item['precio_unitario'], 0, ',', '.'); ?></td>
                                                <td><?php echo $item['cantidad']; ?></td>
                                                <td><strong>$<?php echo number_format($item['subtotal'], 0, ',', '.'); ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th colspan="3">Total del Pedido:</th>
                                            <th>$<?php echo number_format($pedido['total'], 0, ',', '.'); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Estado del Pedido -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Estado del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item <?php echo in_array($pedido['estado'], ['pendiente', 'procesando', 'enviado', 'entregado']) ? 'active' : ''; ?>">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6>Pedido Recibido</h6>
                                    <small class="text-muted">Tu pedido ha sido recibido y está siendo procesado</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item <?php echo in_array($pedido['estado'], ['procesando', 'enviado', 'entregado']) ? 'active' : ''; ?>">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6>En Preparación</h6>
                                    <small class="text-muted">Estamos preparando tus productos</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item <?php echo in_array($pedido['estado'], ['enviado', 'entregado']) ? 'active' : ''; ?>">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>Enviado</h6>
                                    <small class="text-muted">Tu pedido está en camino</small>
                                </div>
                            </div>
                            
                            <div class="timeline-item <?php echo $pedido['estado'] == 'entregado' ? 'active' : ''; ?>">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Entregado</h6>
                                    <small class="text-muted">¡Tu pedido ha sido entregado!</small>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($pedido['estado'] == 'pendiente'): ?>
                            <div class="alert alert-info mt-3">
                                <small>Tu pedido será procesado dentro de las próximas 24 horas.</small>
                            </div>
                        <?php elseif ($pedido['estado'] == 'enviado'): ?>
                            <div class="alert alert-primary mt-3">
                                <small>Tu pedido está en camino. Tiempo estimado de entrega: 2-3 días hábiles.</small>
                            </div>
                        <?php elseif ($pedido['estado'] == 'entregado'): ?>
                            <div class="alert alert-success mt-3">
                                <small>¡Gracias por tu compra! Esperamos que disfrutes tus libros.</small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="catalogo.php" class="btn btn-primary">Seguir Comprando</a>
                            <?php if ($pedido['estado'] == 'entregado'): ?>
                                <button class="btn btn-outline-secondary" onclick="window.print()">Imprimir Factura</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -19px;
            top: 20px;
            height: calc(100% - 10px);
            width: 2px;
            background-color: #dee2e6;
        }
        
        .timeline-item.active:not(:last-child)::before {
            background-color: #198754;
        }
        
        .timeline-marker {
            position: absolute;
            left: -25px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }
        
        .timeline-item.active .timeline-marker {
            box-shadow: 0 0 0 2px #198754;
        }
        
        .timeline-content h6 {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .timeline-content small {
            font-size: 0.8rem;
        }
    </style>

</body>

</html>
