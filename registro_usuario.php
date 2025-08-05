<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style_registro_usuario.css">
    <script src="js/validacion_registro_usuario.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>
    
    <main class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger w-100" style="max-width:400px;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        
        <form id="registerForm" action="procesar_registro.php" method="POST" class="needs-validation w-100"
            style="max-width:400px;" novalidate>
            <h2 class="mb-4 text-center">Crear Cuenta</h2>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
                <div class="invalid-feedback">Ingresa tu nombre.</div>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
                <div class="invalid-feedback">Ingresa tu apellido.</div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="tu@ejemplo.com"
                    required pattern="\S+@\S+\.\S+">
                <div class="invalid-feedback">Correo no válido.</div>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <textarea class="form-control" id="direccion" name="direccion" rows="2" required></textarea>
                <div class="invalid-feedback">Ingresa tu dirección.</div>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" required>
                <div class="invalid-feedback">Ingresa tu teléfono.</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="********"
                        required minlength="8" title="Mínimo 8 caracteres, con al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo"/>
                <div class="invalid-feedback">Mínimo 8 caracteres, con al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo.</div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <div class="invalid-feedback">Las contraseñas no coinciden.</div>
            </div>

            <button type="submit" class="btn btn-success w-100">Registrarse</button>
        </form>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>

</html>