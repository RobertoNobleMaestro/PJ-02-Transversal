CREATE DATABASE bd_restaurante;

USE bd_restaurante;

-- Tabla de usuarios para los camareros
-- Tabla de usuarios (sin cambios, ya está adaptada)
CREATE DATABASE bd_restaurante;

USE bd_restaurante;

-- Crear la tabla de usuarios
CREATE TABLE tbl_usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_user VARCHAR(100),
    nombre_real VARCHAR(30) NOT NULL,
    ape_usuario VARCHAR(30) NOT NULL,
    telefono_usuario CHAR(9) NOT NULL,
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

-- Crear la nueva tabla: Recursos (sillas, mesas adicionales, etc.)
CREATE TABLE tbl_recursos (
    id_recurso INT PRIMARY KEY AUTO_INCREMENT,
    nombre_recurso VARCHAR(100) NOT NULL,     -- Nombre del recurso
    cantidad INT NOT NULL,                    -- Cantidad total disponible del recurso
    descripcion TEXT DEFAULT NULL             -- Descripción del recurso
);

-- Crear la relación entre salas y recursos
CREATE TABLE tbl_sala_recursos (
    id_sala INT NOT NULL,                     -- ID de la sala
    id_recurso INT NOT NULL,                  -- ID del recurso
    cantidad_disponible INT NOT NULL,         -- Cantidad disponible en la sala
    PRIMARY KEY (id_sala, id_recurso)        -- Clave primaria compuesta
);

-- Crear la nueva tabla: Reservas de recursos (por ejemplo, camareros en un horario específico)
CREATE TABLE tbl_reservas_recursos (
    id_reserva INT PRIMARY KEY AUTO_INCREMENT,  -- Cambié el nombre de la columna a id_reserva
    id_usuario INT NOT NULL,                    -- Usuario (camarero, gerente, etc.)
    fecha_reserva DATE NOT NULL,                -- Fecha de la reserva
    hora_inicio TIME NOT NULL,                  -- Hora de inicio de la reserva
    hora_fin TIME NOT NULL,                     -- Hora de finalización de la reserva
    id_recurso INT NOT NULL                     -- ID del recurso reservado
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

-- Agregar las claves foráneas en la tabla tbl_sala_recursos
ALTER TABLE tbl_sala_recursos
ADD CONSTRAINT fk_sala_recursos_sala FOREIGN KEY (id_sala) REFERENCES tbl_salas(id_sala),
ADD CONSTRAINT fk_sala_recursos_recurso FOREIGN KEY (id_recurso) REFERENCES tbl_recursos(id_recurso);

-- Agregar las claves foráneas en la tabla tbl_reservas_recursos
ALTER TABLE tbl_reservas_recursos
ADD CONSTRAINT fk_reservas_recursos_usuario FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario),
ADD CONSTRAINT fk_reservas_recursos_recurso FOREIGN KEY (id_recurso) REFERENCES tbl_recursos(id_recurso);

-- Insertar roles
INSERT INTO tbl_rol (nombre_rol) VALUES
    ('Camarero'),
    ('Administrador'),
    ('Gerente'),
    ('Personal de Mantenimiento');

-- Insertar usuarios (camareros) adaptados (sin id_usuario porque es AUTO_INCREMENT)
INSERT INTO tbl_usuarios (nombre_user, nombre_real, ape_usuario, telefono_usuario, contrasena, rol_user, foto_usuario) VALUES
    ('Jorge', 'Jorge', 'López', '123456789', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1, NULL),  -- Contraseña: asdASD123
    ('Olga', 'Olga', 'Gómez', '987654321', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1, NULL),   -- Contraseña: asdASD123
    ('Miguel', 'Miguel', 'Pérez', '456123789', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 1, NULL),   -- Contraseña: asdASD123
    ('Ana', 'Ana', 'Martínez', '654987321', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 3, NULL),   -- Contraseña: asdASD123
    ('Luis', 'Luis', 'Ramírez', '789123456', '$2y$10$wORRwXyRsJRc9ua8okkNuO6m/GbqBuZouNb4LZbwFPDG6HwNUhOVa', 4, NULL);  -- Contraseña: asdASD123

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
    (601, 6, 12, 'libre'),
    (701, 7, 12, 'libre'),
    (801, 8, 16, 'libre'),
    (901, 9, 18, 'libre');

-- Insertar recursos
INSERT INTO tbl_recursos (nombre_recurso, cantidad, descripcion) VALUES
    ('Sillas adicionales', 50, 'Sillas disponibles para eventos o necesidades extra'),
    ('Mesas adicionales', 10, 'Mesas adicionales para eventos'),
    ('Proyector', 1, 'Proyector para presentaciones o eventos en salas privadas'),
    ('Altavoces', 5, 'Altavoces para música ambiental o presentaciones');

-- Relación entre salas y recursos
INSERT INTO tbl_sala_recursos (id_sala, id_recurso, cantidad_disponible) VALUES
    (6, 1, 10),   -- Sala Privada 1 tiene 10 sillas adicionales
    (6, 3, 1),    -- Sala Privada 1 tiene 1 proyector
    (7, 1, 5),    -- Sala Privada 2 tiene 5 sillas adicionales
    (8, 2, 2),    -- Sala Privada 3 tiene 2 mesas adicionales
    (9, 4, 1);    -- Sala Privada 4 tiene 1 altavoz

-- Insertar reservas de recursos (eliminada la columna cantidad en la tabla de reservas)
INSERT INTO tbl_reservas_recursos (id_usuario, fecha_reserva, hora_inicio, hora_fin, id_recurso) VALUES
(1, '2024-12-01', '10:00:00', '12:00:00', 1), 
(2, '2024-12-01', '14:00:00', '16:00:00', 3), 
(3, '2024-12-01', '18:00:00', '20:00:00', 4)
-- Insertar más ocupaciones
INSERT INTO tbl_ocupaciones (id_usuario, id_mesa, fecha_inicio, fecha_fin) VALUES
(1, 1, '2024-12-02 12:30:00', '2024-12-02 14:30:00'),
(2, 4, '2024-12-02 18:00:00', '2024-12-02 19:30:00'),
(3, 6, '2024-12-02 20:00:00', '2024-12-02 22:00:00'),
(4, 7, '2024-12-03 12:00:00', '2024-12-03 14:00:00')

-- Insertar más mesas en terrazas y salas privadas
INSERT INTO tbl_mesas (numero_mesa, id_sala, numero_sillas, estado) VALUES
(902, 9, 20, 'libre'), 
(303, 3, 4, 'ocupada')