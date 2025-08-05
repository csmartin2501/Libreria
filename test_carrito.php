<?php
// Test simple para verificar la funcionalidad del carrito
session_start();
require_once 'config/database.php';

echo "<h2>Test de Funcionalidad del Carrito</h2>";

try {
    // Verificar conexión PDO
    if (!isset($pdo)) {
        echo "<p style='color: red;'>❌ PDO no está definido</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✓ PDO está definido</p>";
    
    // Verificar tablas
    $tables = ['usuarios', 'libros', 'carrito'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Tabla '$table' existe</p>";
        } else {
            echo "<p style='color: red;'>❌ Tabla '$table' no existe</p>";
        }
    }
    
    // Test básico de consulta al carrito
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM carrito");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Items totales en carrito: {$result['total']}</p>";
    
    // Test de consulta de libros
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM libros");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Libros totales en catálogo: {$result['total']}</p>";
    
    // Test de consulta de usuarios
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Usuarios registrados: {$result['total']}</p>";
    
    echo "<p style='color: green;'>✓ Todas las consultas funcionan correctamente</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='carrito.php'>Probar Carrito</a> | <a href='catalogo.php'>Probar Catálogo</a> | <a href='install.php'>Reinstalar BD</a></p>";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Carrito</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
    <!-- El contenido PHP se renderiza arriba -->
</body>
</html>
