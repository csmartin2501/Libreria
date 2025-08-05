<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Web SPA - Proyecto Final</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style_index.css">
    <script src="js/validacion_index.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'partials/nav.php'; ?>

    <main class="container my-5" id="home">
        <section class="text-center mb-5">
            <h2>Bienvenidos a la Librería Online</h2>
            <p>Descubre nuestros últimos títulos y promociones exclusivas.</p>
        </section>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger mx-auto" style="max-width:400px;">
                <?php 
                switch($_GET['error']) {
                    case 'login_required':
                        echo 'Debes iniciar sesión para acceder a esa página.';
                        break;
                    case 'campos_vacios':
                        echo 'Por favor completa todos los campos.';
                        break;
                    case 'email_invalido':
                        echo 'El formato del email no es válido.';
                        break;
                    case 'credenciales_invalidas':
                        echo 'Email o contraseña incorrectos.';
                        break;
                    default:
                        echo htmlspecialchars($_GET['error']);
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success mx-auto" style="max-width:400px;">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (!isLoggedIn()): ?>
        <section id="login" class="mx-auto">
            <h3 class="text-center mb-3">Iniciar Sesión</h3>
            <form id="loginForm" action="login.php" method="POST" novalidate class="needs-validation">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ejemplo.com"
                        required pattern="\S+@\S+\.\S+">
                    <div class="invalid-feedback">
                        Por favor ingresa un correo válido.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="********"
                        required minlength="8"
                        title="Mínimo 8 caracteres, con al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo"/>
                    <div class="invalid-feedback">
                        Mínimo 8 caracteres, con al menos 1 mayúscula, 1 minúscula, 1 número y 1 símbolo.
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Ingresar</button>
            </form>
            <hr class="my-4">
            <button type="button" class="btn btn-secondary w-100" onclick="location.href='registro_usuario.php'">Registrarse</button>
        </section>
        <?php else: ?>
        <section class="text-center">
            <h3>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h3>
            <p>Ya tienes una sesión activa.</p>
            <a href="catalogo.php" class="btn btn-primary">Ver Catálogo</a>
            <a href="carrito.php" class="btn btn-outline-primary">Mi Carrito</a>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>

</html>