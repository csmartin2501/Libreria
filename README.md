# Librería Online - Sistema de Carrito de Compras

## Descripción

Sistema web completo para una librería online que incluye:
- Registro e inicio de sesión de usuarios
- Catálogo de libros
- Carrito de compras
- Gestión de pedidos
- Control de sesiones y seguridad

## Características Principales

### Sistema de Usuarios
- Registro de usuarios con validación
- Inicio de sesión seguro
- Control de sesiones PHP
- Validación de contraseñas seguras

### Carrito de Compras
- Agregar libros al carrito
- Modificar cantidades
- Eliminar productos
- Cálculo automático de totales
- Persistencia por sesión

### Gestión de Pedidos
- Finalizar compra
- Confirmación de pedidos
- Historial de pedidos
- Estados de pedido

### Seguridad
- Acceso restringido por sesiones
- Validación de datos en cliente y servidor
- Preparadas statements para SQL
- Sanitización de datos

## Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frameworks**: Bootstrap para UI responsiva

## Estructura del Proyecto

```
libreria/
├── config/
│   └── database.php          # Configuración de base de datos
├── includes/
│   ├── auth.php             # Funciones de autenticación
│   └── carrito.php          # Funciones del carrito
├── database/
│   └── create_database.sql  # Script de creación de BD
├── css/
│   ├── style_index.css
│   ├── style_carrito.css
│   └── style_registro_usuario.css
├── js/
│   ├── validacion_index.js
│   ├── validacion_registro_libro.js
│   ├── validacion_registro_usuario.js
│   └── validaciones_carrito.js
├── partials/
│   ├── nav.php              # Navegación
│   └── footer.php           # Pie de página
├── index.php                # Página principal
├── catalogo.php             # Catálogo de libros
├── carrito.php              # Carrito de compras
├── checkout.php             # Finalizar compra
├── registro_usuario.php     # Registro de usuarios
├── registro_libro.php       # Registro de libros (solo logueados)
├── login.php                # Procesamiento login
├── logout.php               # Cerrar sesión
├── install.php              # Instalador de BD
└── README.md               # Este archivo
```

## Instalación

### Prerrequisitos
- XAMPP, WAMP, LAMP o servidor web con PHP y MySQL
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensión PDO de PHP habilitada

### Pasos de Instalación

1. **Clonar/Descargar el proyecto**
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   # o descargar y extraer el ZIP
   ```

2. **Configurar el servidor web**
   - Colocar los archivos en la carpeta del servidor web (htdocs, www, etc.)
   - Asegurar que Apache y MySQL estén ejecutándose

3. **Configurar la base de datos**
   - Editar `config/database.php` si es necesario
   - Ejecutar el instalador: `http://localhost/libreria/install.php`
   - O importar manualmente `database/create_database.sql`

4. **Verificar permisos**
   - Asegurar que PHP tenga permisos de lectura en todos los archivos

### Configuración de Base de Datos

El archivo `config/database.php` contiene la configuración por defecto:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'libreria');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Modificar según tu configuración local.

## Uso del Sistema

### Para Usuarios No Registrados
- Ver página principal
- Registrarse como nuevo usuario

### Para Usuarios Registrados
- Iniciar sesión
- Ver catálogo de libros
- Agregar libros al carrito
- Gestionar carrito (modificar cantidades, eliminar items)
- Finalizar compras
- Ver historial de pedidos
- Acceder al perfil

### Para Administración
- Registrar nuevos libros (requiere login)
- Gestionar inventario

## Validaciones Implementadas

### Cliente (JavaScript)
- Validación de formularios en tiempo real
- Validación de contraseñas seguras
- Validación de cantidades en carrito

### Servidor (PHP)
- Sanitización de datos de entrada
- Validación de tipos de datos
- Verificación de stock disponible
- Control de sesiones

## Base de Datos

### Tablas Principales

**usuarios**
- id, nombre, email, password, direccion, telefono

**libros**
- id, titulo, autor, precio, cantidad_inventario, descripcion

**carrito**
- id, usuario_id, libro_id, cantidad, monto_total

**pedidos**
- id, usuario_id, total, estado, fecha_pedido, direccion_envio

**detalles_pedido**
- id, pedido_id, libro_id, cantidad, precio_unitario, subtotal

## Funcionalidades de Seguridad

1. **Autenticación**
   - Passwords hasheados con `password_hash()`
   - Verificación con `password_verify()`

2. **Control de Sesiones**
   - Verificación de login para páginas protegidas
   - Timeout de sesión automático

3. **Validación de Datos**
   - Prepared statements para prevenir SQL injection
   - Sanitización de inputs
   - Validación tanto en cliente como servidor

4. **Autorización**
   - Acceso restringido a funcionalidades según estado de login
   - Verificación de permisos por usuario

## Responsive Design

- Diseño adaptable usando Bootstrap 5
- Funciona en desktop, tablet y móvil
- Navegación móvil optimizada

## APIs y Endpoints

### Principales archivos de procesamiento:
- `login.php` - Procesamiento de login
- `logout.php` - Cerrar sesión
- `procesar_registro.php` - Registro de usuarios
- `procesar_libro.php` - Registro de libros

## Solución de Problemas

### Error de Conexión a BD
- Verificar que MySQL esté ejecutándose
- Comprobar credenciales en `config/database.php`
- Verificar que la base de datos existe

### Problemas de Sesión
- Verificar que `session_start()` se ejecute antes de cualquier output
- Comprobar configuración de sesiones en PHP

### Errores de Permisos
- Verificar permisos de lectura en archivos
- Asegurar que el servidor web pueda acceder a los archivos

## Contribución

Para contribuir al proyecto:
1. Fork del repositorio
2. Crear rama para nueva feature
3. Implementar cambios
4. Crear pull request

## Licencia

[Especificar licencia]

## Contacto

[Información de contacto del desarrollador]

## Changelog

### v1.0.0
- Sistema completo de autenticación
- Carrito de compras funcional
- Gestión de pedidos
- Interfaz responsive
- Sistema de validaciones

---

**Nota**: Este es un proyecto educativo que demuestra las mejores prácticas en desarrollo web con PHP, MySQL y JavaScript.
