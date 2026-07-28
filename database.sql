-- =========================================================
-- BASE DE DATOS: hotel_db
-- Sistema de gestion hotelera
-- Motor: MySQL / MariaDB - Uso con PDO PHP
-- =========================================================

DROP DATABASE IF EXISTS hotel_db;
CREATE DATABASE hotel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel_db;

-- ---------------------------------------------------------
-- TABLA: cliente
-- ---------------------------------------------------------
CREATE TABLE cliente (
    id_cliente      INT AUTO_INCREMENT PRIMARY KEY,
    cedula          VARCHAR(15) NOT NULL UNIQUE,
    nombres         VARCHAR(80) NOT NULL,
    apellidos       VARCHAR(80) NOT NULL,
    telefono        VARCHAR(20) NOT NULL,
    email           VARCHAR(100),
    direccion       VARCHAR(150),
    fecha_registro  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLA: tipo_habitacion
-- ---------------------------------------------------------
CREATE TABLE tipo_habitacion (
    id_tipo         INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(50) NOT NULL,
    descripcion     VARCHAR(200),
    precio_noche    DECIMAL(10,2) NOT NULL,
    capacidad       INT NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLA: habitacion
-- ---------------------------------------------------------
CREATE TABLE habitacion (
    id_habitacion   INT AUTO_INCREMENT PRIMARY KEY,
    numero          VARCHAR(10) NOT NULL UNIQUE,
    piso            INT NOT NULL,
    id_tipo         INT NOT NULL,
    estado          ENUM('Disponible','Ocupada','Mantenimiento') NOT NULL DEFAULT 'Disponible',
    CONSTRAINT fk_habitacion_tipo FOREIGN KEY (id_tipo)
        REFERENCES tipo_habitacion(id_tipo)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLA: reserva
-- ---------------------------------------------------------
CREATE TABLE reserva (
    id_reserva      INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente      INT NOT NULL,
    id_habitacion   INT NOT NULL,
    fecha_entrada   DATE NOT NULL,
    fecha_salida    DATE NOT NULL,
    fecha_reserva   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado          ENUM('Pendiente','Confirmada','Cancelada','Finalizada') NOT NULL DEFAULT 'Pendiente',
    total           DECIMAL(10,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_reserva_cliente FOREIGN KEY (id_cliente)
        REFERENCES cliente(id_cliente)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reserva_habitacion FOREIGN KEY (id_habitacion)
        REFERENCES habitacion(id_habitacion)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_fechas CHECK (fecha_salida > fecha_entrada)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLA: servicio
-- ---------------------------------------------------------
CREATE TABLE servicio (
    id_servicio     INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(80) NOT NULL,
    descripcion     VARCHAR(200),
    precio          DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- TABLA: gasto  (consumo de servicios asociados a una reserva)
-- ---------------------------------------------------------
CREATE TABLE gasto (
    id_gasto        INT AUTO_INCREMENT PRIMARY KEY,
    id_reserva      INT NOT NULL,
    id_servicio     INT NOT NULL,
    cantidad        INT NOT NULL DEFAULT 1,
    fecha           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal        DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_gasto_reserva FOREIGN KEY (id_reserva)
        REFERENCES reserva(id_reserva)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_gasto_servicio FOREIGN KEY (id_servicio)
        REFERENCES servicio(id_servicio)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================================================
-- DATOS DE PRUEBA
-- =========================================================
INSERT INTO tipo_habitacion (nombre, descripcion, precio_noche, capacidad) VALUES
('Sencilla', 'Habitacion individual con cama de 1 plaza', 25.00, 1),
('Doble', 'Habitacion con dos camas', 40.00, 2),
('Suite', 'Habitacion amplia con sala y jacuzzi', 90.00, 4);

INSERT INTO habitacion (numero, piso, id_tipo, estado) VALUES
('101', 1, 1, 'Disponible'),
('102', 1, 1, 'Disponible'),
('201', 2, 2, 'Disponible'),
('202', 2, 2, 'Ocupada'),
('301', 3, 3, 'Disponible');

INSERT INTO cliente (cedula, nombres, apellidos, telefono, email, direccion) VALUES
('1712345678', 'Juan', 'Perez Lopez', '0991234567', 'juan.perez@mail.com', 'Av. Amazonas 123'),
('1723456789', 'Maria', 'Gomez Ruiz', '0987654321', 'maria.gomez@mail.com', 'Calle Sucre 456');

INSERT INTO servicio (nombre, descripcion, precio) VALUES
('Desayuno', 'Desayuno buffet por persona', 5.50),
('Lavanderia', 'Servicio de lavado de ropa', 8.00),
('Spa', 'Sesion de spa de 1 hora', 30.00),
('Transporte', 'Traslado aeropuerto - hotel', 15.00);

INSERT INTO reserva (id_cliente, id_habitacion, fecha_entrada, fecha_salida, estado, total) VALUES
(1, 4, '2026-07-20', '2026-07-25', 'Confirmada', 200.00),
(2, 3, '2026-08-01', '2026-08-03', 'Pendiente', 80.00);

INSERT INTO gasto (id_reserva, id_servicio, cantidad, subtotal) VALUES
(1, 1, 2, 11.00),
(1, 3, 1, 30.00);
