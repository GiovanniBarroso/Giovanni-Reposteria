CREATE DATABASE IF NOT EXISTS pasteleria;

USE pasteleria;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    tipo VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
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




-- Insertar usuarios iniciales
INSERT INTO clientes (nombre, usuario, password) VALUES
('Administrador', 'admin', MD5('admin')), -- Encripta la contraseña
('Usuario Genérico', 'usuario', MD5('usuario')); -- Encripta la contraseña


INSERT INTO productos (nombre, precio, categoria, tipo) VALUES
('Croissant', 2.50, 'Bollo', 'Bollo'),
('Chocolate Negro', 3.00, 'Chocolate', 'Chocolate'),
('Tarta de Queso', 15.00, 'Tarta', 'Tarta');


SELECT * FROM productos;
SELECT * FROM pedidos;