-- Crear la base de datos LIBRERIA
CREATE DATABASE IF NOT EXISTS libreria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE libreria;

-- Tabla USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla LIBROS
CREATE TABLE IF NOT EXISTS libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    cantidad_inventario INT NOT NULL DEFAULT 0,
    descripcion TEXT,
    imagen_url VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla CARRITO
CREATE TABLE IF NOT EXISTS carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    libro_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    monto_total DECIMAL(10, 2) NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (usuario_id, libro_id)
);

-- Tabla PEDIDOS
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'procesando', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    direccion_envio TEXT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla DETALLES_PEDIDO
CREATE TABLE IF NOT EXISTS detalles_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    libro_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
);

-- Insertar algunos libros de ejemplo
INSERT INTO libros (titulo, autor, precio, cantidad_inventario, descripcion) VALUES
('Cien años de soledad', 'Gabriel García Márquez', 15000, 10, 'Una obra maestra de la literatura latinoamericana'),
('Don Quijote de la Mancha', 'Miguel de Cervantes', 18000, 8, 'La novela más famosa de la literatura española'),
('1984', 'George Orwell', 12000, 15, 'Una distopía clásica sobre el control totalitario'),
('El Principito', 'Antoine de Saint-Exupéry', 10000, 20, 'Una historia filosófica para todas las edades'),
('Orgullo y prejuicio', 'Jane Austen', 14000, 12, 'Romance clásico de la literatura inglesa'),
('Crónica de una muerte anunciada', 'Gabriel García Márquez', 13000, 7, 'Una novela corta llena de realismo mágico'),
('La casa de los espíritus', 'Isabel Allende', 16000, 9, 'Saga familiar llena de elementos fantásticos'),
('Rayuela', 'Julio Cortázar', 17000, 6, 'Novela experimental de la literatura argentina');
