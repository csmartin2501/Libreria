<?php
require_once __DIR__ . '/../config/database.php';

// Función para agregar un libro al carrito
function agregarAlCarrito($usuario_id, $libro_id, $cantidad = 1) {
    global $pdo;
    
    try {
        // Verificar que el libro existe y tiene stock
        $stmt = $pdo->prepare("SELECT precio, cantidad_inventario FROM libros WHERE id = ?");
        $stmt->execute([$libro_id]);
        $libro = $stmt->fetch();
        
        if (!$libro) {
            return ['success' => false, 'message' => 'El libro no existe'];
        }
        
        if ($libro['cantidad_inventario'] < $cantidad) {
            return ['success' => false, 'message' => 'No hay suficiente stock disponible'];
        }
        
        $monto_total = $libro['precio'] * $cantidad;
        
        // Verificar si ya existe en el carrito
        $stmt = $pdo->prepare("SELECT cantidad FROM carrito WHERE usuario_id = ? AND libro_id = ?");
        $stmt->execute([$usuario_id, $libro_id]);
        $item_existente = $stmt->fetch();
        
        if ($item_existente) {
            // Actualizar cantidad existente
            $nueva_cantidad = $item_existente['cantidad'] + $cantidad;
            $nuevo_monto = $libro['precio'] * $nueva_cantidad;
            
            $stmt = $pdo->prepare("UPDATE carrito SET cantidad = ?, monto_total = ? WHERE usuario_id = ? AND libro_id = ?");
            $stmt->execute([$nueva_cantidad, $nuevo_monto, $usuario_id, $libro_id]);
        } else {
            // Insertar nuevo item
            $stmt = $pdo->prepare("INSERT INTO carrito (usuario_id, libro_id, cantidad, monto_total) VALUES (?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $libro_id, $cantidad, $monto_total]);
        }
        
        return ['success' => true, 'message' => 'Libro agregado al carrito'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al agregar al carrito: ' . $e->getMessage()];
    }
}

// Función para obtener items del carrito
function obtenerCarrito($usuario_id) {
    global $pdo;
    
    // Verificar que $pdo esté disponible
    if (!isset($pdo) || !$pdo) {
        error_log("Error: PDO no está disponible en obtenerCarrito()");
        return [];
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT c.id, c.cantidad, c.monto_total, 
                   l.titulo, l.autor, l.precio, l.imagen_url 
            FROM carrito c 
            JOIN libros l ON c.libro_id = l.id 
            WHERE c.usuario_id = ? 
            ORDER BY c.fecha_agregado DESC
        ");
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetchAll();
        
        // Asegurar que siempre retorne un array
        return is_array($result) ? $result : [];
    } catch (PDOException $e) {
        error_log("Error en obtenerCarrito(): " . $e->getMessage());
        return [];
    }
}

// Función para actualizar cantidad en el carrito
function actualizarCantidadCarrito($carrito_id, $nueva_cantidad, $usuario_id) {
    global $pdo;
    
    try {
        if ($nueva_cantidad <= 0) {
            return eliminarDelCarrito($carrito_id, $usuario_id);
        }
        
        // Obtener precio del libro
        $stmt = $pdo->prepare("
            SELECT l.precio, l.cantidad_inventario 
            FROM carrito c 
            JOIN libros l ON c.libro_id = l.id 
            WHERE c.id = ? AND c.usuario_id = ?
        ");
        $stmt->execute([$carrito_id, $usuario_id]);
        $item = $stmt->fetch();
        
        if (!$item) {
            return ['success' => false, 'message' => 'Item no encontrado'];
        }
        
        if ($item['cantidad_inventario'] < $nueva_cantidad) {
            return ['success' => false, 'message' => 'No hay suficiente stock disponible'];
        }
        
        $nuevo_monto = $item['precio'] * $nueva_cantidad;
        
        $stmt = $pdo->prepare("UPDATE carrito SET cantidad = ?, monto_total = ? WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$nueva_cantidad, $nuevo_monto, $carrito_id, $usuario_id]);
        
        return ['success' => true, 'message' => 'Cantidad actualizada'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
    }
}

// Función para eliminar un item del carrito
function eliminarDelCarrito($carrito_id, $usuario_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$carrito_id, $usuario_id]);
        
        return ['success' => true, 'message' => 'Libro eliminado del carrito'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()];
    }
}

// Función para obtener total del carrito
function obtenerTotalCarrito($usuario_id) {
    global $pdo;
    
    // Verificar que $pdo esté disponible
    if (!isset($pdo) || !$pdo) {
        error_log("Error: PDO no está disponible en obtenerTotalCarrito()");
        return 0;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT SUM(monto_total) as total FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch();
        
        $total = $result['total'] ?? 0;
        return is_numeric($total) ? floatval($total) : 0;
    } catch (PDOException $e) {
        error_log("Error en obtenerTotalCarrito(): " . $e->getMessage());
        return 0;
    }
}

// Función para contar items en el carrito
function contarItemsCarrito($usuario_id) {
    global $pdo;
    
    // Verificar que $pdo esté disponible
    if (!isset($pdo) || !$pdo) {
        error_log("Error: PDO no está disponible en contarItemsCarrito()");
        return 0;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT SUM(cantidad) as total_items FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch();
        
        $total = $result['total_items'] ?? 0;
        return is_numeric($total) ? intval($total) : 0;
    } catch (PDOException $e) {
        error_log("Error en contarItemsCarrito(): " . $e->getMessage());
        return 0;
    }
}

// Función para vaciar el carrito
function vaciarCarrito($usuario_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        
        return ['success' => true, 'message' => 'Carrito vaciado'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al vaciar carrito: ' . $e->getMessage()];
    }
}

// Función para realizar un pedido
function realizarPedido($usuario_id, $direccion_envio) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Obtener items del carrito
        $items_carrito = obtenerCarrito($usuario_id);
        
        if (empty($items_carrito)) {
            throw new Exception('El carrito está vacío');
        }
        
        // Calcular total
        $total = obtenerTotalCarrito($usuario_id);
        
        // Crear el pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, direccion_envio) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $total, $direccion_envio]);
        $pedido_id = $pdo->lastInsertId();
        
        // Insertar detalles del pedido y actualizar inventario
        foreach ($items_carrito as $item) {
            // Insertar detalle del pedido
            $stmt = $pdo->prepare("INSERT INTO detalles_pedido (pedido_id, libro_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $libro_id = $pdo->query("SELECT libro_id FROM carrito WHERE id = " . $item['id'])->fetchColumn();
            $stmt->execute([$pedido_id, $libro_id, $item['cantidad'], $item['precio'], $item['monto_total']]);
            
            // Actualizar inventario
            $stmt = $pdo->prepare("UPDATE libros SET cantidad_inventario = cantidad_inventario - ? WHERE id = ?");
            $stmt->execute([$item['cantidad'], $libro_id]);
        }
        
        // Vaciar el carrito
        vaciarCarrito($usuario_id);
        
        $pdo->commit();
        
        return ['success' => true, 'message' => 'Pedido realizado exitosamente', 'pedido_id' => $pedido_id];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al realizar pedido: ' . $e->getMessage()];
    }
}
?>
