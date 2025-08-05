<?php
require_once __DIR__ . '/../config/database.php';

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Función para requerir login (redirecciona si no está logueado)
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?error=login_required');
        exit();
    }
}

// Función para obtener datos del usuario actual
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, email, direccion, telefono FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Función para verificar credenciales de login
function login($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

// Función para registrar un nuevo usuario
function registrarUsuario($nombre, $email, $password, $direccion, $telefono) {
    global $pdo;
    
    try {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }
        
        // Encriptar contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, direccion, telefono) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $passwordHash, $direccion, $telefono]);
        
        return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al registrar usuario: ' . $e->getMessage()];
    }
}

// Función para cerrar sesión
function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Función para validar contraseña segura
function validarPassword($password) {
    // Mínimo 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo
    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    return preg_match($patron, $password);
}

// Función para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>
