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

CREATE TABLE IF NOT EXISTS detalles_chocolate (
    producto_id INT PRIMARY KEY,
    porcentajeCacao DECIMAL(5, 2) NOT NULL,
    peso DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE IF NOT EXISTS detalles_tarta (
    producto_id INT PRIMARY KEY,
    rellenos TEXT NOT NULL,
    numPisos INT NOT NULL,
    minComensales INT NOT NULL,
    maxComensales INT NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE IF NOT EXISTS valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    cliente_id INT NOT NULL,
    valoracion TEXT NOT NULL,
    puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 5),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Insertar usuarios iniciales
INSERT INTO clientes (nombre, usuario, password, rol) VALUES
('Administrador', 'admin', '$2y$10$1UoqgiuqXdH10K1jjEzLjOWAFr9JYVs9WMPUTo0hZKB0XdiHDOZk2', 'admin'), -- CONTRASEÑA: admin
('Usuario', 'usuario', '$2y$10$OrOFyAqm2bv98ikLZ7XpGOcv5EO67p28qsLA7a4qAKwlQ4lxIpxEy', 'cliente'), -- CONTRASEÑA usuario
('Cristian Rodriguez Moreno', 'cristian', '$2y$10$9YDcryHtSENMSm9dW3obeuH7w/h7Lo/bSk.3E7hMUUY3JHyYyrhkW', 'cliente'), -- CONTRASEÑA cristian123
('Daniel Rodriguez', 'daniel', '$2y$10$WukFofwK.InHWBPthNwTheu9Mut7VfFWU8N5zbwjY.hJlBm.PoYwm', 'cliente'), --  CONTRASEÑA daniel123
('Giovanni Barroso', 'giovanni', '$2y$10$Id3kuV9A6a1cXpYByRAMAO5mKuhNH7awypfW5nVxd9c.SyJvn/Xpu', 'cliente'); -- CONTRASEÑA giovanni123
; 

-- Insertar productos iniciales
INSERT INTO productos (nombre, precio, categoria, tipo, descripcion, porcentajeCacao, peso, rellenos, numPisos, minComensales, maxComensales) VALUES
-- Bollos
('Croissant', 2.50, 'Bollo', 'Bollo', 'Un bollo clásico de mantequilla', NULL, NULL, NULL, NULL, NULL, NULL),
('Napolitana de Chocolate', 2.80, 'Bollo', 'Bollo', 'Un bollo relleno de chocolate', NULL, NULL, NULL, NULL, NULL, NULL),
('Brioche', 3.00, 'Bollo', 'Bollo', 'Un bollo suave y esponjoso con mantequilla', NULL, NULL, NULL, NULL, NULL, NULL),

-- Chocolates
('Chocolate Negro', 3.00, 'Chocolate', 'Chocolate', 'Chocolate con un alto porcentaje de cacao', 75.00, 200.00, NULL, NULL, NULL, NULL),
('Chocolate con Leche', 2.50, 'Chocolate', 'Chocolate', 'Chocolate suave con leche', 45.00, 150.00, NULL, NULL, NULL, NULL),
('Chocolate Blanco', 2.80, 'Chocolate', 'Chocolate', 'Chocolate blanco cremoso', 30.00, 120.00, NULL, NULL, NULL, NULL),

-- Tartas
('Tarta de Queso', 15.00, 'Tarta', 'Tarta', 'Tarta cremosa con base de galleta', NULL, NULL, 'queso, crema', 1, 4, 8),
('Tarta de Fresa', 18.00, 'Tarta', 'Tarta', 'Tarta con relleno y cobertura de fresa', NULL, NULL, 'fresa, nata', 2, 6, 10),
('Tarta Selva Negra', 20.00, 'Tarta', 'Tarta', 'Tarta de chocolate con cerezas y nata', NULL, NULL, 'chocolate, cereza, nata', 3, 8, 12);


-- Valoraciones por los 5 usuarios
INSERT INTO valoraciones (producto_id, cliente_id, valoracion, puntuacion) VALUES
-- Usuario: Cristian Rodriguez Moreno
(1, 3, 'El croissant estaba delicioso, fresco y bien hecho.', 5),
(2, 3, 'El chocolate negro tiene un sabor intenso, pero un poco amargo para mi gusto.', 3),

-- Usuario: Daniel Rodriguez
(3, 4, 'El brioche es muy esponjoso y delicioso. Ideal para desayunos.', 5),
(8, 4, 'La tarta de fresa estaba increíblemente fresca y bien decorada.', 4),

-- Usuario: Giovanni Barroso
(7, 5, 'La tarta de queso es excelente, pero un poco pesada para mi gusto.', 3),
(9, 5, 'La selva negra fue una experiencia deliciosa, perfecta para compartir.', 5),

-- Usuario: Usuario (ya existente)
(8, 2, 'La tarta de fresa es perfecta para celebraciones, me encantó.', 5),
(2, 2, 'El chocolate negro es de excelente calidad, con un sabor muy puro.', 4),

-- Usuario: Administrador (ya existente)
(3, 1, 'El brioche esponjoso y con un gran sabor, muy recomendado.', 4),
(9, 1, 'La selva negra tiene una combinación de sabores excelente.', 5);


-- Consultar datos para verificación
SELECT * FROM clientes;
SELECT * FROM productos;
SELECT * FROM pedidos;