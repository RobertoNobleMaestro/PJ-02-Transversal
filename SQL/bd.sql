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
    tipo_sala VARCHAR(50),
    stock_sillas INT, 
    imagen_sala VARCHAR(255) DEFAULT NULL     -- Imagen asociada a la sala
);

-- Crear la tabla de mesas
CREATE TABLE tbl_mesas (
    id_mesa INT PRIMARY KEY AUTO_INCREMENT,
    numero_mesa INT,
    id_sala INT,
    numero_sillas INT
);

-- Crear la nueva tabla de reservas (reemplazo de tbl_reservas_recursos)
CREATE TABLE tbl_reservas (
    id_reserva INT PRIMARY KEY AUTO_INCREMENT,  -- Cambié el nombre de la columna a id_reserva
    fecha_reserva DATE NOT NULL,
    fecha_inicio TIME NOT NULL,                   -- Hora de inicio de la reserva
    fecha_fin TIME NOT NULL,                      -- Hora de finalización de la reserva
    id_mesa INT NOT NULL,                        -- ID de la mesa
    id_usuario INT NOT NULL,                  -- ID del usuario que hace la reserva
    id_turno INT NOT NULL                        -- Agregar la columna id_turno                  -- ID del usuario que hace la reserva
);

CREATE TABLE tbl_turnos (
    id_turno INT AUTO_INCREMENT PRIMARY KEY,
    nombre_turno VARCHAR(50) NOT NULL,        -- Ejemplo: "Mediodía", "Noche"
    hora_inicio TIME NOT NULL,                -- Hora de inicio del turno
    hora_fin TIME NOT NULL                    -- Hora de fin del turno
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
ADD CONSTRAINT fk_reservas_usuarios FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario),
ADD CONSTRAINT fk_reservas_turnos FOREIGN KEY (id_turno) REFERENCES tbl_turnos(id_turno);


-- Insertar roles
INSERT INTO tbl_rol (nombre_rol) VALUES
    ('Camarero'),
    ('Administrador');

-- Insertar usuarios (camareros) adaptados (sin id_usuario porque es AUTO_INCREMENT)
INSERT INTO tbl_usuarios (nombre_user, nombre_real, ape_usuario, contrasena, rol_user) VALUES
    ('Jorge', 'Jorge', 'López', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 2),
    ('Olga', 'Olga', 'Gómez','$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1),
    ('Miguel', 'Miguel', 'Pérez', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1),
    ('Ana', 'Ana', 'Martínez','$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1),
    ('Luis', 'Luis', 'Ramírez', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1);

-- Insertar salas
-- Insertar salas con el stock máximo de sillas (60) por sala
INSERT INTO tbl_salas (nombre_sala, tipo_sala, imagen_sala, stock_sillas) VALUES
    ('Terraza 1', 'Terraza', 'terraza 1.jpg', 60),
    ('Terraza 2', 'Terraza', 'terraza 2.jpg', 60),
    ('Terraza 3', 'Terraza', 'terraza 3.jpg', 60),
    ('Comedor 1', 'Comedor', 'comedor 1.jpg', 60),
    ('Comedor 2', 'Comedor', 'comedor 2.jpg', 60),
    ('Sala Privada 1', 'Privada', 'sala privada 1.jpg', 60),
    ('Sala Privada 2', 'Privada', 'sala privada 2.jpg', 60),
    ('Sala Privada 3', 'Privada', 'sala privada 3.jpg', 60),
    ('Sala Privada 4', 'Privada', 'sala privada 4.jpg', 60);


-- Insertar mesas (relacionadas con salas existentes)
INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas) VALUES
-- Mesas Terraza 1
    (101, 1, 4),
    (102, 1, 6),
    (103, 1, 4),
    (104, 1, 9),
-- Mesas Terraza 2
    (201, 2, 4),
    (202, 2, 6),
    (203, 2, 12),
    (204, 2, 4),
-- Mesas Terraza 3
    (301, 3, 4),
    (302, 3, 4),
    (303, 3, 7),
    (304, 3, 2),
-- Mesas Comedor 1
    (401, 4, 2),
    (402, 4, 9),
    (403, 4, 2),
    (404, 4, 7),
    (405, 4, 5),
    (406, 4, 6),
-- Mesas Comedor 2
    (501, 5, 12),
    (502, 5, 9),
    (503, 5, 16),
    (504, 5, 2),
    (505, 5, 4),
    (506, 5, 4),
-- Mesas Sala Privada
    -- Mesas Sala Privada 1
    (602, 6, 10),
    (603, 6, 8),
    -- Mesas Sala Privada 2
    (702, 7, 12),
    (703, 7, 14),
    -- Mesas Sala Privada 3
    (802, 8, 16),
    (803, 8, 20),
    -- Mesas Sala Privada 4
    (902, 9, 18),
    (903, 9, 14);

    -- Insertar turnos
INSERT INTO tbl_turnos (nombre_turno, hora_inicio, hora_fin) VALUES
    ('Mediodía', '12:00:00', '16:00:00'),
    ('Noche', '19:00:00', '24:00:00');
