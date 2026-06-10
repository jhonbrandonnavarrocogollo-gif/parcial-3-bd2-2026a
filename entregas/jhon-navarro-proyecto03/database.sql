CREATE DATABASE IF NOT EXISTS `reservas_restaurante` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `reservas_restaurante`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `detalle_orden`;
DROP TABLE IF EXISTS `orden`;
DROP TABLE IF EXISTS `reserva`;
DROP TABLE IF EXISTS `plato`;
DROP TABLE IF EXISTS `usuario`;
DROP TABLE IF EXISTS `mesero`;
DROP TABLE IF EXISTS `mesa`;
DROP TABLE IF EXISTS `cliente`;
DROP TABLE IF EXISTS `categoria`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Tablas sin dependencias
CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mesa` (
  `id_mesa` int(11) NOT NULL AUTO_INCREMENT,
  `numero_mesa` smallint(6) NOT NULL,
  `capacidad` smallint(6) NOT NULL CHECK (`capacidad` between 1 and 20),
  `ubicacion` varchar(80) NOT NULL,
  `estado` enum('disponible','ocupada','reservada','mantenimiento') NOT NULL DEFAULT 'disponible',
  PRIMARY KEY (`id_mesa`),
  UNIQUE KEY `numero_mesa` (`numero_mesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mesero` (
  `id_mesero` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_mesero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','mesero') NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `id_cliente` int(11) DEFAULT NULL,
  `id_mesero` int(11) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uk_usuario_cliente` (`id_cliente`),
  UNIQUE KEY `uk_usuario_mesero` (`id_mesero`),
  CONSTRAINT `fk_usuario_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_usuario_mesero` FOREIGN KEY (`id_mesero`) REFERENCES `mesero` (`id_mesero`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tablas con una dependencia
CREATE TABLE `plato` (
  `id_plato` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` >= 0),
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id_plato`),
  KEY `fk_plato_categoria` (`id_categoria`),
  CONSTRAINT `fk_plato_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reserva` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_hora_inicio` datetime NOT NULL,
  `fecha_hora_fin` datetime NOT NULL CHECK (`fecha_hora_fin` > `fecha_hora_inicio`),
  `num_personas` smallint(6) NOT NULL CHECK (`num_personas` between 1 and 50),
  `estado` enum('pendiente','confirmada','cancelada','completada') NOT NULL DEFAULT 'pendiente',
  `notas` varchar(300) DEFAULT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  PRIMARY KEY (`id_reserva`),
  KEY `fk_reserva_cliente` (`id_cliente`),
  KEY `idx_reserva_mesa_horario` (`id_mesa`,`fecha_hora_inicio`,`fecha_hora_fin`),
  CONSTRAINT `fk_reserva_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reserva_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `mesa` (`id_mesa`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tablas con múltiples dependencias
CREATE TABLE `orden` (
  `id_orden` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('recibida','en_cocina','servida','pagada','cancelada') NOT NULL DEFAULT 'recibida',
  `total` decimal(12,2) NOT NULL DEFAULT 0.00 CHECK (`total` >= 0),
  `id_mesa` int(11) NOT NULL,
  `id_reserva` int(11) DEFAULT NULL,
  `id_mesero` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_orden`),
  KEY `fk_orden_mesa` (`id_mesa`),
  KEY `fk_orden_reserva` (`id_reserva`),
  KEY `fk_orden_mesero` (`id_mesero`),
  CONSTRAINT `fk_orden_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `mesa` (`id_mesa`) ON UPDATE CASCADE,
  CONSTRAINT `fk_orden_mesero` FOREIGN KEY (`id_mesero`) REFERENCES `mesero` (`id_mesero`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_orden_reserva` FOREIGN KEY (`id_reserva`) REFERENCES `reserva` (`id_reserva`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `detalle_orden` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad` smallint(6) NOT NULL CHECK (`cantidad` > 0),
  `precio_unitario` decimal(10,2) NOT NULL CHECK (`precio_unitario` >= 0),
  `notas` varchar(200) DEFAULT NULL,
  `id_orden` int(11) NOT NULL,
  `id_plato` int(11) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_orden` (`id_orden`),
  KEY `fk_detalle_plato` (`id_plato`),
  CONSTRAINT `fk_detalle_orden` FOREIGN KEY (`id_orden`) REFERENCES `orden` (`id_orden`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_plato` FOREIGN KEY (`id_plato`) REFERENCES `plato` (`id_plato`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════════
-- DATOS DE EJEMPLO
-- ═══════════════════════════════════════════════════════════════

INSERT INTO `categoria` (`nombre`, `descripcion`) VALUES
('Entradas', 'Aperitivos y entradas'),
('Platos Fuertes', 'Platos principales del menú'),
('Postres', 'Dulces y postres caseros'),
('Bebidas', 'Bebidas frías y calientes'),
('Cócteles', 'Bebidas alcohólicas');

INSERT INTO `cliente` (`nombre`, `apellido`, `telefono`, `email`) VALUES
('María', 'García', '3001112233', 'maria.garcia@email.com'),
('Carlos', 'Rodríguez', '3102223344', 'carlos.rodriguez@email.com'),
('Ana', 'Martínez', '3203334455', 'ana.martinez@email.com'),
('Luis', 'Hernández', '3004445566', 'luis.hernandez@email.com'),
('Sofía', 'López', '3105556677', 'sofia.lopez@email.com');

INSERT INTO `mesa` (`numero_mesa`, `capacidad`, `ubicacion`, `estado`) VALUES
(1, 2, 'Terraza', 'disponible'),
(2, 4, 'Terraza', 'disponible'),
(3, 4, 'Salón principal', 'disponible'),
(4, 6, 'Salón principal', 'reservada'),
(5, 8, 'Salón VIP', 'disponible'),
(6, 2, 'Barra', 'ocupada'),
(7, 4, 'Jardín', 'disponible'),
(8, 10, 'Salón VIP', 'mantenimiento');

INSERT INTO `mesero` (`nombre`, `apellido`, `telefono`) VALUES
('Pedro', 'Ramírez', '3006667788'),
('Laura', 'Castro', '3107778899'),
('Diego', 'Vargas', '3208889900');

-- Contraseña demo para meseros: 1234
INSERT INTO `usuario` (`email`, `password`, `rol`, `estado`, `id_mesero`) VALUES
('pedro.ramirez@restaurante.com', '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 1),
('laura.castro@restaurante.com', '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 2),
('diego.vargas@restaurante.com', '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 3);

INSERT INTO `plato` (`nombre`, `descripcion`, `precio`, `disponible`, `id_categoria`) VALUES
('Ensalada César', 'Lechuga romana, crutones, parmesano y aderezo César', 18500.00, 1, 1),
('Sopa del día', 'Sopa casera preparada diariamente', 12000.00, 1, 1),
('Filete de res', 'Filete angus 300g con papas y vegetales', 45000.00, 1, 2),
('Salmón a la plancha', 'Salmón fresco con arroz y ensalada', 42000.00, 1, 2),
('Pasta Alfredo', 'Fettuccine con salsa cremosa y pollo', 28000.00, 1, 2),
('Tiramisú', 'Postre italiano clásico con café y mascarpone', 14000.00, 1, 3),
('Cheesecake', 'Tarta de queso con frutos rojos', 13000.00, 1, 3),
('Agua mineral', 'Botella 500ml', 5000.00, 1, 4),
('Jugo natural', 'Mango, naranja o maracuyá', 8000.00, 1, 4),
('Café americano', 'Café colombiano recién preparado', 6000.00, 1, 4),
('Mojito', 'Ron blanco, menta, lima y soda', 22000.00, 1, 5),
('Margarita', 'Tequila, triple sec y lima', 24000.00, 1, 5);

INSERT INTO `reserva` (`fecha_hora_inicio`, `fecha_hora_fin`, `num_personas`, `estado`, `notas`, `id_cliente`, `id_mesa`) VALUES
(CONCAT(CURDATE(), ' 12:00:00'), CONCAT(CURDATE(), ' 14:00:00'), 4, 'confirmada', 'Cumpleaños', 1, 3),
(CONCAT(CURDATE(), ' 19:00:00'), CONCAT(CURDATE(), ' 21:00:00'), 2, 'pendiente', NULL, 2, 1),
(DATE_ADD(CONCAT(CURDATE(), ' 13:00:00'), INTERVAL 1 DAY), DATE_ADD(CONCAT(CURDATE(), ' 15:00:00'), INTERVAL 1 DAY), 6, 'confirmada', 'Reunión de negocios', 3, 5),
(DATE_ADD(CONCAT(CURDATE(), ' 20:00:00'), INTERVAL 2 DAY), DATE_ADD(CONCAT(CURDATE(), ' 22:00:00'), INTERVAL 2 DAY), 4, 'pendiente', NULL, 4, 2),
(DATE_ADD(CONCAT(CURDATE(), ' 18:30:00'), INTERVAL 3 DAY), DATE_ADD(CONCAT(CURDATE(), ' 20:30:00'), INTERVAL 3 DAY), 2, 'confirmada', 'Aniversario', 5, 7);

INSERT INTO `orden` (`fecha_hora`, `estado`, `total`, `id_mesa`, `id_reserva`, `id_mesero`) VALUES
(CONCAT(CURDATE(), ' 12:30:00'), 'pagada', 0, 3, 1, 1),
(CONCAT(CURDATE(), ' 13:15:00'), 'en_cocina', 0, 6, NULL, 2),
(DATE_SUB(CONCAT(CURDATE(), ' 20:00:00'), INTERVAL 1 DAY), 'pagada', 0, 2, NULL, 1);

INSERT INTO `detalle_orden` (`cantidad`, `precio_unitario`, `notas`, `id_orden`, `id_plato`) VALUES
(2, 18500.00, NULL, 1, 1),
(2, 45000.00, 'Término medio', 1, 3),
(2, 8000.00, NULL, 1, 9),
(1, 14000.00, NULL, 1, 6),
(1, 28000.00, 'Sin cebolla', 2, 5),
(1, 6000.00, NULL, 2, 10),
(2, 42000.00, NULL, 3, 4),
(2, 22000.00, NULL, 3, 11);

UPDATE `orden` SET `total` = (
    SELECT COALESCE(SUM(cantidad * precio_unitario), 0) FROM `detalle_orden` WHERE `id_orden` = `orden`.`id_orden`
);
