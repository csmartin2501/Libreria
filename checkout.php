<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/carrito.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener usuario actual
$usuario = getCurrentUser();

// Obtener items del carrito
$items_carrito_checkout = obtenerCarrito($usuario['id']);
$total_carrito = obtenerTotalCarrito($usuario['id']);

// Si el carrito está vacío, redirigir
if (empty($items_carrito_checkout)) {
    header('Location: carrito.php?error=' . urlencode('El carrito está vacío'));
    exit();
}

// Procesar el pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_pedido'])) {
    $direccion_envio = trim($_POST['direccion_envio'] ?? '');
    
    if (empty($direccion_envio)) {
        $error = 'La dirección de envío es requerida';
    } else {
        $resultado = realizarPedido($usuario['id'], $direccion_envio);
        
        if ($resultado['success']) {
            header('Location: pedido_confirmado.php?pedido_id=' . $resultado['pedido_id']);
            exit();
        } else {
            $error = $resultado['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finalizar Compra - Librería Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5">
        <h2 class="text-center mb-4">Finalizar Compra</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información de Envío</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="checkoutForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control" id="nombre" 
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" 
                                           value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" 
                                       value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="direccion_envio" class="form-label">Dirección de Envío *</label>
                                <textarea class="form-control" id="direccion_envio" name="direccion_envio" 
                                          rows="3" required placeholder="Ingresa la dirección completa de envío"><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
                                <div class="form-text">Si es diferente a tu dirección registrada, actualízala aquí.</div>
                            </div>
                            
                            <div class="mb-3">
                                <h6>Método de Pago</h6>
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="metodo_pago" id="contraentrega" value="contraentrega" checked>
                                        <label class="form-check-label" for="contraentrega">
                                            <strong>Pago Contra Entrega</strong><br>
                                            <small class="text-muted">Paga en efectivo cuando recibas tu pedido</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="carrito.php" class="btn btn-outline-secondary">Volver al Carrito</a>
                                <button type="submit" name="finalizar_pedido" class="btn btn-success flex-grow-1">
                                    Confirmar Pedido
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($items_carrito_checkout as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['titulo']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($item['autor']); ?> 
                                        (x<?php echo $item['cantidad']; ?>)
                                    </small>
                                </div>
                                <span>$<?php echo number_format($item['monto_total'], 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total_carrito, 0, ',', '.'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Impuestos:</span>
                            <span>$0</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong>$<?php echo number_format($total_carrito, 0, ',', '.'); ?></strong>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                Al confirmar tu pedido, aceptas nuestros términos y condiciones de venta.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

    <script>
        // Validación adicional del formulario
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const direccion = document.getElementById('direccion_envio').value.trim();
            
            if (!direccion) {
                e.preventDefault();
                alert('Por favor ingresa una dirección de envío válida.');
                document.getElementById('direccion_envio').focus();
                return false;
            }
            
            // Confirmar el pedido
            if (!confirm('¿Estás seguro de que quieres confirmar este pedido?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>

</body>

</html>
