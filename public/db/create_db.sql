CREATE DATABASE IF NOT EXISTS pasteleria;

USE pasteleria;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    tipo VARCHAR(20) NOT NULL
);

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Insertar usuarios iniciales
INSERT INTO clientes (nombre, usuario, password) VALUES
('Administrador', 'admin', MD5('admin')), -- Encripta la contraseña
('Usuario Genérico', 'usuario', MD5('usuario')); -- Encripta la contraseña


INSERT INTO productos (id, nombre, precio, categoria, tipo) VALUES
(1, 'Croissant', 2.50, 'Bollo', 'Bollo'),
(2, 'Chocolate Negro', 3.00, 'Chocolate', 'Chocolate'),
(3, 'Tarta de Queso', 15.00, 'Tarta', 'Tarta');
