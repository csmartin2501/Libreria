<?php
session_start();
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Validaciones
    if (empty($email) || empty($password)) {
        header('Location: index.php?error=campos_vacios');
        exit();
    }
    
    if (!validarEmail($email)) {
        header('Location: index.php?error=email_invalido');
        exit();
    }
    
    // Intentar login
    if (login($email, $password)) {
        header('Location: catalogo.php');
        exit();
    } else {
        header('Location: index.php?error=credenciales_invalidas');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>
