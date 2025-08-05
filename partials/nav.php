<?php
// Incluir funciones de autenticación
if (!isset($auth_included)) {
    require_once __DIR__ . '/../includes/auth.php';
    require_once __DIR__ . '/../includes/carrito.php';
    $auth_included = true;
}

// Obtener usuario actual si está logueado
$usuario_actual = getCurrentUser();
$items_carrito = 0;
if ($usuario_actual) {
    $items_carrito = contarItemsCarrito($usuario_actual['id']);
}
?>
<!-- nav.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Librería Online</a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navMenu"
      aria-controls="navMenu"
      aria-expanded="false"
      aria-label="Mostrar navegación"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='index.php') echo 'active';?>" href="index.php">
            Inicio
          </a>
        </li>
        
        <?php if (!$usuario_actual): ?>
        <!-- Opciones para usuarios NO logueados -->
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='registro_usuario.php') echo 'active';?>" href="registro_usuario.php">
            Registrarse
          </a>
        </li>
        <?php else: ?>
        <!-- Opciones para usuarios logueados -->
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='catalogo.php') echo 'active';?>" href="catalogo.php">
            Catálogo
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='registro_libro.php') echo 'active';?>" href="registro_libro.php">
            Registrar Libro
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='carrito.php') echo 'active';?>" href="carrito.php">
            Carrito <span class="badge bg-primary"><?php echo $items_carrito; ?></span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo htmlspecialchars($usuario_actual['nombre']); ?>
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
            <li><a class="dropdown-item" href="mis_pedidos.php">Mis Pedidos</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">Cerrar Sesión</a></li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
