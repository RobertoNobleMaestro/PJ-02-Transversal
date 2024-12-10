CREATE DATABASE bd_restaurante;

USE bd_restaurante;


-- Crear la tabla de usuarios
CREATE TABLE tbl_usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_user VARCHAR(100),
    nombre_real VARCHAR(30) NOT NULL,
    ape_usuario VARCHAR(30) NOT NULL,
    contrasena VARCHAR(100),
    rol_user INT NOT NULL,
    foto_usuario VARCHAR(255) NULL
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

-- Crear la tabla para los registros de ocupación de las mesas
CREATE TABLE tbl_ocupaciones (
    id_ocupacion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    id_mesa INT,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,    -- Fecha y hora del inicio de la ocupación
    fecha_fin DATETIME                                  -- Fecha y hora del final de la ocupación
);

-- Crear la nueva tabla: Reservas de recursos (por ejemplo, camareros en un horario específico)
CREATE TABLE tbl_reservas_recursos (
    id_reserva INT PRIMARY KEY AUTO_INCREMENT,  -- Cambié el nombre de la columna a id_reserva
    fecha_reserva DATE NOT NULL,                -- Fecha de la reserva
    hora_inicio TIME NOT NULL,                  -- Hora de inicio de la reserva
    hora_fin TIME NOT NULL,                     -- Hora de finalización de la reserva
    id_mesa INT NOT NULL                     -- ID del recurso(mesa)
);

-- Agregar las claves foráneas después de crear las tablas

-- Agregar la clave foránea en la tabla tbl_usuarios
ALTER TABLE tbl_usuarios
ADD CONSTRAINT fk_rol_usuario FOREIGN KEY (rol_user) REFERENCES tbl_rol(id_rol);

-- Agregar la clave foránea en la tabla tbl_mesas
ALTER TABLE tbl_mesas
ADD CONSTRAINT fk_mesas_salas FOREIGN KEY (id_sala) REFERENCES tbl_salas(id_sala);
-- Agregar las claves foráneas en la tabla tbl_ocupaciones
ALTER TABLE tbl_ocupaciones
ADD CONSTRAINT fk_ocupaciones_usuarios FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario),
ADD CONSTRAINT fk_ocupaciones_mesas FOREIGN KEY (id_mesa) REFERENCES tbl_mesas(id_mesa);

ALTER TABLE tbl_reservas_recursos
ADD CONSTRAINT fk_reservas_mesas FOREIGN KEY (id_mesa) REFERENCES tbl_mesas(id_mesa);

-- Insertar roles
-- Insertar roles
INSERT INTO tbl_rol (nombre_rol) VALUES
    ('Camarero'),
    ('Administrador'),
    ('Gerente'),
    ('Personal de Mantenimiento');

-- Insertar usuarios (camareros) adaptados (sin id_usuario porque es AUTO_INCREMENT)
INSERT INTO tbl_usuarios (nombre_user, nombre_real, ape_usuario, contrasena, rol_user, foto_usuario) VALUES

    ('Jorge', 'Jorge', 'López', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa',2, NULL),
    ('Olga', 'Olga', 'Gómez','$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1, NULL),
    ('Miguel', 'Miguel', 'Pérez', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1, NULL),
    ('Ana', 'Ana', 'Martínez','$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 3, NULL),
    ('Luis', 'Luis', 'Ramírez', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 4, NULL);

-- Insertar salas
INSERT INTO tbl_salas (nombre_sala, tipo_sala) VALUES
    ('Terraza 1', 'Terraza'),
    ('Terraza 2', 'Terraza'),
    ('Terraza 3', 'Terraza'),
    ('Comedor 1', 'Comedor'),
    ('Comedor 2', 'Comedor'),
    ('Sala Privada 1', 'Privada'),
    ('Sala Privada 2', 'Privada'),
    ('Sala Privada 3', 'Privada'),
    ('Sala Privada 4', 'Privada');

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

-- Insertar ocupaciones
INSERT INTO tbl_ocupaciones (id_usuario, id_mesa, fecha_inicio, fecha_fin) VALUES
    (1, 1, '2024-12-02 12:30:00', '2024-12-02 14:30:00'),
    (2, 4, '2024-12-02 18:00:00', '2024-12-02 19:30:00'),
    (3, 6, '2024-12-02 20:00:00', '2024-12-02 22:00:00'),
    (4, 7, '2024-12-03 12:00:00', '2024-12-03 14:00:00');


-- Insertar más mesas en terrazas y salas privadas
INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas, estado) VALUES
    (902, 9, 20, 'libre'), 
    (303, 3, 4, 'ocupada');

