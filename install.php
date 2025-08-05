<?php
// Script de instalación de la base de datos
echo "<h1>Instalación de la Base de Datos - Librería Online</h1>";

// Configuración de la base de datos
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'libreria';

try {
    // Conectar a MySQL sin especificar base de datos
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Conexión a MySQL establecida correctamente</p>";
    
    // Crear la base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Base de datos '$database' creada o verificada</p>";
    
    // Conectar a la base de datos específica
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Leer y ejecutar el script SQL
    $sql_script = file_get_contents(__DIR__ . '/database/create_database.sql');
    
    if ($sql_script === false) {
        throw new Exception('No se pudo leer el archivo create_database.sql');
    }
    
    // Dividir el script en consultas individuales
    $queries = explode(';', $sql_script);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }
    
    echo "<p>✓ Tablas creadas correctamente</p>";
    echo "<p>✓ Datos de ejemplo insertados</p>";
    
    // Verificar las tablas creadas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tablas creadas:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Verificar algunos datos
    $stmt = $pdo->query("SELECT COUNT(*) FROM libros");
    $libro_count = $stmt->fetchColumn();
    
    echo "<h3>Datos iniciales:</h3>";
    echo "<p>Libros en el catálogo: $libro_count</p>";
    
    echo "<div style='background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724;'>¡Instalación Completada Exitosamente!</h3>";
    echo "<p style='color: #155724;'>La base de datos ha sido configurada correctamente. Puedes proceder a usar la aplicación.</p>";
    echo "<p><a href='index.php' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir a la Aplicación</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #721c24;'>Error en la Instalación</h3>";
    echo "<p style='color: #721c24;'>Error: " . $e->getMessage() . "</p>";
    echo "<h4>Posibles soluciones:</h4>";
    echo "<ul style='color: #721c24;'>";
    echo "<li>Verificar que MySQL esté ejecutándose</li>";
    echo "<li>Comprobar las credenciales de la base de datos</li>";
    echo "<li>Asegurar que el usuario tenga permisos para crear bases de datos</li>";
    echo "<li>Verificar que el archivo create_database.sql exista en la carpeta database/</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Configuración del Proyecto</h3>";
echo "<p>Recuerda configurar los siguientes archivos si es necesario:</p>";
echo "<ul>";
echo "<li><strong>config/database.php</strong> - Configuración de conexión a la base de datos</li>";
echo "<li>Permisos de carpetas para escritura si es necesario</li>";
echo "</ul>";

echo "<h3>Credenciales de Prueba</h3>";
echo "<p>Puedes crear un usuario de prueba o usar estos datos para probar la aplicación:</p>";
echo "<ul>";
echo "<li><strong>Email:</strong> test@example.com</li>";
echo "<li><strong>Contraseña:</strong> Test123@</li>";
echo "</ul>";
echo "<p><em>Nota: Tendrás que registrar este usuario manualmente a través del formulario de registro.</em></p>";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación Completada - Librería Online</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h3 {
            color: #555;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }
        ul {
            margin-left: 20px;
        }
        li {
            margin-bottom: 5px;
        }
        hr {
            margin: 30px 0;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>
    <!-- El contenido PHP se renderiza arriba -->
</body>
</html>
