<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'libreria');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Asegurar que $pdo esté disponible globalmente
global $pdo;

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log del error para debug
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
}
?>
