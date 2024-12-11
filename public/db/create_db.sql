CREATE DATABASE IF NOT EXISTS pasteleria;

USE pasteleria;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    tipo VARCHAR(20) NOT NULL,
    descripcion TEXT NULL,
    porcentajeCacao DECIMAL(5, 2) NULL,
    peso DECIMAL(10, 2) NULL,
    rellenos TEXT NULL,
    numPisos INT NULL,
    minComensales INT NULL,
    maxComensales INT NULL
);


CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente'
);

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);


CREATE TABLE IF NOT EXISTS detalle_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE detalles_chocolate (
    producto_id INT PRIMARY KEY,
    porcentajeCacao DECIMAL(5, 2) NOT NULL,
    peso DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);


CREATE TABLE detalles_tarta (
    producto_id INT PRIMARY KEY,
    rellenos TEXT NOT NULL,
    numPisos INT NOT NULL,
    minComensales INT NOT NULL,
    maxComensales INT NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);


INSERT INTO productos (nombre, precio, categoria, tipo, descripcion, porcentajeCacao, peso, rellenos, numPisos, minComensales, maxComensales) VALUES
('Croissant', 2.50, 'Bollo', 'Bollo', 'Un bollo clásico de mantequilla', NULL, NULL, NULL, NULL, NULL, NULL),
('Chocolate Negro', 3.00, 'Chocolate', 'Chocolate', 'Chocolate con un alto porcentaje de cacao', 75.00, 200.00, NULL, NULL, NULL, NULL),
('Tarta de Queso', 15.00, 'Tarta', 'Tarta', 'Tarta cremosa con base de galleta', NULL, NULL, 'queso, crema', 1, 4, 8),
('Tarta de Cebolla', 12.95, 'Tarta', 'Tarta', 'Tarta salada de cebolla caramelizada', NULL, NULL, 'cebolla, queso', 1, 4, 6),
('Chocolate Azul', 12.00, 'Chocolate', 'Chocolate', 'Chocolate con colorante azul', 60.00, 150.00, NULL, NULL, NULL, NULL);



SELECT * FROM clientes;
SELECT * FROM productos;
SELECT * FROM pedidos;