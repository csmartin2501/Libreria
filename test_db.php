<?php
// Archivo de prueba para verificar la conexión a la base de datos
session_start();

// Verificar si existe la base de datos y las tablas
require_once 'config/database.php';

echo "<h2>Prueba de Conexión a la Base de Datos</h2>";

try {
    // Verificar conexión
    echo "<p>✓ Conexión a la base de datos exitosa</p>";
    
    // Verificar tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tablas en la base de datos:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Verificar estructura de la tabla carrito
    if (in_array('carrito', $tables)) {
        echo "<h3>Estructura de la tabla 'carrito':</h3>";
        $stmt = $pdo->query("DESCRIBE carrito");
        $columns = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Clave</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Verificar datos en carrito
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM carrito");
        $count = $stmt->fetch();
        echo "<p>Total de items en carrito: {$count['total']}</p>";
    } else {
        echo "<p style='color: red;'>❌ La tabla 'carrito' no existe</p>";
    }
    
    // Verificar usuarios
    if (in_array('usuarios', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $count = $stmt->fetch();
        echo "<p>Total de usuarios registrados: {$count['total']}</p>";
    }
    
    // Verificar libros
    if (in_array('libros', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM libros");
        $count = $stmt->fetch();
        echo "<p>Total de libros en catálogo: {$count['total']}</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='install.php'>Ejecutar instalación</a> | <a href='index.php'>Ir al inicio</a></p>";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Base de Datos</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        table { width: 100%; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <!-- El contenido PHP se renderiza arriba -->
</body>
</html>
