CREATE DATABASE bd_restaurante;

USE bd_restaurante;

-- Crear la tabla de usuarios
CREATE TABLE tbl_usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_user VARCHAR(100),
    nombre_real VARCHAR(30) NOT NULL,
    ape_usuario VARCHAR(30) NOT NULL,
    contrasena VARCHAR(100),
    rol_user INT NOT NULL
);

-- Crear la tabla de roles
CREATE TABLE tbl_rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    nombre_rol VARCHAR(40)
);

-- Crear la tabla de salas (con imágenes)
CREATE TABLE tbl_salas (
    id_sala INT PRIMARY KEY AUTO_INCREMENT,
    nombre_sala VARCHAR(100),
    tipo_sala VARCHAR(50),                    -- Tipo de sala (Terraza, Comedor, Sala Privada...)
    imagen_sala VARCHAR(255) DEFAULT NULL     -- Imagen asociada a la sala
);

-- Crear la tabla de mesas
CREATE TABLE tbl_mesas (
    id_mesa INT PRIMARY KEY AUTO_INCREMENT,
    numero_mesa INT,
    id_sala INT,
    numero_sillas INT,
    estado ENUM('libre', 'ocupada') DEFAULT 'libre'
);

-- Crear la nueva tabla de reservas (reemplazo de tbl_reservas_recursos)
CREATE TABLE tbl_reservas (
    id_reserva INT PRIMARY KEY AUTO_INCREMENT,  -- Cambié el nombre de la columna a id_reserva
    fecha_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,                   -- Hora de inicio de la reserva
    hora_fin TIME NOT NULL,                      -- Hora de finalización de la reserva
    id_mesa INT NOT NULL,                        -- ID de la mesa
    id_usuario INT NOT NULL                      -- ID del usuario que hace la reserva
);

-- Agregar las claves foráneas después de crear las tablas

-- Agregar la clave foránea en la tabla tbl_usuarios
ALTER TABLE tbl_usuarios
ADD CONSTRAINT fk_rol_usuario FOREIGN KEY (rol_user) REFERENCES tbl_rol(id_rol);

-- Agregar la clave foránea en la tabla tbl_mesas
ALTER TABLE tbl_mesas
ADD CONSTRAINT fk_mesas_salas FOREIGN KEY (id_sala) REFERENCES tbl_salas(id_sala);

-- Agregar las claves foráneas en la tabla tbl_reservas
ALTER TABLE tbl_reservas
ADD CONSTRAINT fk_reservas_mesas FOREIGN KEY (id_mesa) REFERENCES tbl_mesas(id_mesa),
ADD CONSTRAINT fk_reservas_usuarios FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario);

-- Insertar roles
INSERT INTO tbl_rol (nombre_rol) VALUES
    ('Camarero'),
    ('Administrador'),
    ('Gerente'),
    ('Personal de Mantenimiento');

-- Insertar usuarios (camareros) adaptados (sin id_usuario porque es AUTO_INCREMENT)
INSERT INTO tbl_usuarios (nombre_user, nombre_real, ape_usuario, contrasena, rol_user) VALUES
    ('Jorge', 'Jorge', 'López', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 2),
    ('Olga', 'Olga', 'Gómez','$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1),
    ('Miguel', 'Miguel', 'Pérez', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1),
    ('Ana', 'Ana', 'Martínez','$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 3),
    ('Luis', 'Luis', 'Ramírez', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 4);

-- Insertar salas
INSERT INTO tbl_salas (nombre_sala, tipo_sala, imagen_sala) VALUES
    ('Terraza 1', 'Terraza', 'terraza 1.jpg'),
    ('Terraza 2', 'Terraza', 'terraza 2.jpg'),
    ('Terraza 3', 'Terraza', 'terraza 3.jpg'),
    ('Comedor 1', 'Comedor', 'comedor 1.jpg'),
    ('Comedor 2', 'Comedor', 'comedor 2.jpg'),
    ('Sala Privada 1', 'Privada', 'sala privada 1.jpg'),
    ('Sala Privada 2', 'Privada', 'sala privada 2.jpg'),
    ('Sala Privada 3', 'Privada', 'sala privada 3.jpg'),
    ('Sala Privada 4', 'Privada', 'sala privada 4.jpg');

-- Insertar mesas (relacionadas con salas existentes)
INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas, estado) VALUES
-- Mesas Terraza 1
    (101, 1, 4, 'libre'),
    (102, 1, 6, 'libre'),
    (103, 1, 4, 'libre'),
    (104, 1, 9, 'libre'),
-- Mesas Terraza 2
    (201, 2, 4, 'libre'),
    (202, 2, 6, 'libre'),
    (203, 2, 12, 'libre'),
    (204, 2, 4, 'libre'),
-- Mesas Terraza 3
    (301, 3, 4, 'libre'),
    (302, 3, 4, 'libre'),
    (303, 3, 7, 'libre'),
    (304, 3, 2, 'libre'),
-- Mesas Comedor 1
    (401, 4, 2, 'libre'),
    (402, 4, 9, 'libre'),
    (403, 4, 2, 'libre'),
    (404, 4, 7, 'libre'),
    (405, 4, 5, 'libre'),
    (406, 4, 6, 'libre'),
-- Mesas Comedor 2
    (501, 5, 12, 'libre'),
    (502, 5, 9, 'libre'),
    (503, 5, 16, 'libre'),
    (504, 5, 2, 'libre'),
    (505, 5, 4, 'libre'),
    (506, 5, 4, 'libre'),
-- Mesas Sala Privada
    -- Mesas Sala Privada 1
    (602, 6, 10, 'libre'),
    (603, 6, 8, 'libre'),
    -- Mesas Sala Privada 2
    (702, 7, 12, 'libre'),
    (703, 7, 14, 'libre'),
    -- Mesas Sala Privada 3
    (802, 8, 16, 'libre'),
    (803, 8, 20, 'libre'),
    -- Mesas Sala Privada 4
    (902, 9, 18, 'libre'),
    (903, 9, 14, 'libre');