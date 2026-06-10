-- ═══════════════════════════════════════════════════════════════
-- DML — Datos de prueba
-- Requisitos: ≥15 mesas, 10 clientes, 20 reservas, 10 órdenes con detalle
-- Ejecutar después de database.sql (esquema ya creado)
-- ═══════════════════════════════════════════════════════════════
USE `reservas_restaurante`;

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `detalle_orden`;
DELETE FROM `orden`;
DELETE FROM `reserva`;
DELETE FROM `plato`;
DELETE FROM `usuario`;
DELETE FROM `mesero`;
DELETE FROM `mesa`;
DELETE FROM `cliente`;
DELETE FROM `categoria`;
ALTER TABLE `detalle_orden` AUTO_INCREMENT = 1;
ALTER TABLE `orden`         AUTO_INCREMENT = 1;
ALTER TABLE `reserva`       AUTO_INCREMENT = 1;
ALTER TABLE `plato`         AUTO_INCREMENT = 1;
ALTER TABLE `usuario`       AUTO_INCREMENT = 1;
ALTER TABLE `mesero`        AUTO_INCREMENT = 1;
ALTER TABLE `mesa`          AUTO_INCREMENT = 1;
ALTER TABLE `cliente`       AUTO_INCREMENT = 1;
ALTER TABLE `categoria`     AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- ── Catálogo base ──────────────────────────────────────────────
INSERT INTO `categoria` (`nombre`, `descripcion`) VALUES
('Entradas', 'Aperitivos y entradas'),
('Platos Fuertes', 'Platos principales del menú'),
('Postres', 'Dulces y postres caseros'),
('Bebidas', 'Bebidas frías y calientes'),
('Cócteles', 'Bebidas alcohólicas');

INSERT INTO `plato` (`nombre`, `descripcion`, `precio`, `disponible`, `id_categoria`) VALUES
('Ensalada César', 'Lechuga romana, crutones, parmesano y aderezo César', 18500.00, 1, 1),
('Sopa del día', 'Sopa casera preparada diariamente', 12000.00, 1, 1),
('Nachos con guacamole', 'Totopos con guacamole y queso fundido', 16000.00, 1, 1),
('Filete de res', 'Filete angus 300g con papas y vegetales', 45000.00, 1, 2),
('Salmón a la plancha', 'Salmón fresco con arroz y ensalada', 42000.00, 1, 2),
('Pasta Alfredo', 'Fettuccine con salsa cremosa y pollo', 28000.00, 1, 2),
('Pollo al curry', 'Pechuga en salsa curry con arroz basmati', 32000.00, 1, 2),
('Tiramisú', 'Postre italiano clásico con café y mascarpone', 14000.00, 1, 3),
('Cheesecake', 'Tarta de queso con frutos rojos', 13000.00, 1, 3),
('Brownie con helado', 'Brownie caliente con helado de vainilla', 15000.00, 1, 3),
('Agua mineral', 'Botella 500ml', 5000.00, 1, 4),
('Jugo natural', 'Mango, naranja o maracuyá', 8000.00, 1, 4),
('Café americano', 'Café colombiano recién preparado', 6000.00, 1, 4),
('Limonada natural', 'Limonada casera 400ml', 7000.00, 1, 4),
('Mojito', 'Ron blanco, menta, lima y soda', 22000.00, 1, 5),
('Margarita', 'Tequila, triple sec y lima', 24000.00, 1, 5);

-- ── 10 clientes ────────────────────────────────────────────────
INSERT INTO `cliente` (`nombre`, `apellido`, `telefono`, `email`) VALUES
('María', 'García', '3001112233', 'maria.garcia@email.com'),
('Carlos', 'Rodríguez', '3102223344', 'carlos.rodriguez@email.com'),
('Ana', 'Martínez', '3203334455', 'ana.martinez@email.com'),
('Luis', 'Hernández', '3004445566', 'luis.hernandez@email.com'),
('Sofía', 'López', '3105556677', 'sofia.lopez@email.com'),
('Jorge', 'Muñoz', '3206667788', 'jorge.munoz@email.com'),
('Valentina', 'Ríos', '3007778899', 'valentina.rios@email.com'),
('Andrés', 'Pérez', '3108889900', 'andres.perez@email.com'),
('Camila', 'Torres', '3209990011', 'camila.torres@email.com'),
('Diego', 'Salazar', '3010001122', 'diego.salazar@email.com');

-- ── 15 mesas ─────────────────────────────────────────────────
INSERT INTO `mesa` (`numero_mesa`, `capacidad`, `ubicacion`, `estado`) VALUES
(1,  2,  'Terraza',         'disponible'),
(2,  2,  'Terraza',         'disponible'),
(3,  4,  'Salón principal', 'disponible'),
(4,  4,  'Salón principal', 'reservada'),
(5,  4,  'Salón principal', 'disponible'),
(6,  6,  'Salón principal', 'ocupada'),
(7,  6,  'Salón principal', 'disponible'),
(8,  8,  'Salón VIP',       'disponible'),
(9,  8,  'Salón VIP',       'reservada'),
(10, 2,  'Barra',           'ocupada'),
(11, 2,  'Barra',           'disponible'),
(12, 4,  'Jardín',          'disponible'),
(13, 4,  'Jardín',          'disponible'),
(14, 6,  'Jardín',          'disponible'),
(15, 10, 'Salón VIP',       'mantenimiento');

-- ── Meseros y usuarios (contraseña demo: 1234) ─────────────────
INSERT INTO `mesero` (`nombre`, `apellido`, `telefono`) VALUES
('Pedro', 'Ramírez', '3006667788'),
('Laura', 'Castro', '3107778899'),
('Diego', 'Vargas', '3208889900');

INSERT INTO `usuario` (`email`, `password`, `rol`, `estado`, `id_mesero`) VALUES
('pedro.ramirez@restaurante.com', '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 1),
('laura.castro@restaurante.com',  '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 2),
('diego.vargas@restaurante.com',  '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 3);

INSERT INTO `usuario` (`email`, `password`, `rol`, `estado`, `id_cliente`) VALUES
('maria.garcia@email.com',      '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'cliente', 'activo', 1),
('carlos.rodriguez@email.com',  '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'cliente', 'activo', 2),
('ana.martinez@email.com',      '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'cliente', 'activo', 3);

-- ── 20 reservas ──────────────────────────────────────────────
INSERT INTO `reserva` (`fecha_hora_inicio`, `fecha_hora_fin`, `num_personas`, `estado`, `notas`, `id_cliente`, `id_mesa`) VALUES
(CONCAT(CURDATE(), ' 12:00:00'), CONCAT(CURDATE(), ' 14:00:00'), 4, 'confirmada',  'Cumpleaños',                    1,  3),
(CONCAT(CURDATE(), ' 19:00:00'), CONCAT(CURDATE(), ' 21:00:00'), 2, 'pendiente',   NULL,                            2,  1),
(CONCAT(CURDATE(), ' 13:00:00'), CONCAT(CURDATE(), ' 15:00:00'), 6, 'confirmada',  'Reunión de negocios',           3,  8),
(CONCAT(CURDATE(), ' 20:00:00'), CONCAT(CURDATE(), ' 22:00:00'), 4, 'pendiente',   NULL,                            4,  5),
(CONCAT(CURDATE(), ' 18:30:00'), CONCAT(CURDATE(), ' 20:30:00'), 2, 'confirmada',  'Aniversario',                   5,  12),
(DATE_ADD(CONCAT(CURDATE(), ' 12:30:00'), INTERVAL 1 DAY), DATE_ADD(CONCAT(CURDATE(), ' 14:30:00'), INTERVAL 1 DAY), 3, 'confirmada',  'Mesa junto a ventana',          6,  2),
(DATE_ADD(CONCAT(CURDATE(), ' 19:30:00'), INTERVAL 1 DAY), DATE_ADD(CONCAT(CURDATE(), ' 21:30:00'), INTERVAL 1 DAY), 5, 'pendiente',   'Sin gluten',                    7,  7),
(DATE_ADD(CONCAT(CURDATE(), ' 20:00:00'), INTERVAL 2 DAY), DATE_ADD(CONCAT(CURDATE(), ' 22:00:00'), INTERVAL 2 DAY), 4, 'confirmada',  NULL,                            8,  4),
(DATE_ADD(CONCAT(CURDATE(), ' 13:00:00'), INTERVAL 2 DAY), DATE_ADD(CONCAT(CURDATE(), ' 15:00:00'), INTERVAL 2 DAY), 8, 'confirmada',  'Evento familiar',               9,  9),
(DATE_ADD(CONCAT(CURDATE(), ' 18:00:00'), INTERVAL 3 DAY), DATE_ADD(CONCAT(CURDATE(), ' 20:00:00'), INTERVAL 3 DAY), 2, 'pendiente',   NULL,                            10, 11),
(DATE_ADD(CONCAT(CURDATE(), ' 12:00:00'), INTERVAL 4 DAY), DATE_ADD(CONCAT(CURDATE(), ' 14:00:00'), INTERVAL 4 DAY), 4, 'confirmada',  'Celebración laboral',           1,  13),
(DATE_ADD(CONCAT(CURDATE(), ' 19:00:00'), INTERVAL 4 DAY), DATE_ADD(CONCAT(CURDATE(), ' 21:00:00'), INTERVAL 4 DAY), 3, 'cancelada',   'Cliente canceló por viaje',       2,  3),
(DATE_ADD(CONCAT(CURDATE(), ' 20:30:00'), INTERVAL 5 DAY), DATE_ADD(CONCAT(CURDATE(), ' 22:30:00'), INTERVAL 5 DAY), 6, 'pendiente',   NULL,                            3,  14),
(DATE_SUB(CONCAT(CURDATE(), ' 13:00:00'), INTERVAL 1 DAY), DATE_SUB(CONCAT(CURDATE(), ' 15:00:00'), INTERVAL 1 DAY), 4, 'completada',  NULL,                            4,  6),
(DATE_SUB(CONCAT(CURDATE(), ' 19:00:00'), INTERVAL 1 DAY), DATE_SUB(CONCAT(CURDATE(), ' 21:00:00'), INTERVAL 1 DAY), 2, 'completada',  'Cena romántica',                5,  1),
(DATE_SUB(CONCAT(CURDATE(), ' 12:00:00'), INTERVAL 2 DAY), DATE_SUB(CONCAT(CURDATE(), ' 14:00:00'), INTERVAL 2 DAY), 5, 'completada',  NULL,                            6,  8),
(DATE_SUB(CONCAT(CURDATE(), ' 20:00:00'), INTERVAL 2 DAY), DATE_SUB(CONCAT(CURDATE(), ' 22:00:00'), INTERVAL 2 DAY), 4, 'cancelada',   'No se presentó',                7,  5),
(DATE_SUB(CONCAT(CURDATE(), ' 18:00:00'), INTERVAL 3 DAY), DATE_SUB(CONCAT(CURDATE(), ' 20:00:00'), INTERVAL 3 DAY), 3, 'completada',  NULL,                            8,  12),
(DATE_SUB(CONCAT(CURDATE(), ' 13:30:00'), INTERVAL 4 DAY), DATE_SUB(CONCAT(CURDATE(), ' 15:30:00'), INTERVAL 4 DAY), 2, 'completada',  'Almuerzo ejecutivo',            9,  10),
(DATE_SUB(CONCAT(CURDATE(), ' 21:00:00'), INTERVAL 5 DAY), DATE_SUB(CONCAT(CURDATE(), ' 23:00:00'), INTERVAL 5 DAY), 7, 'completada',  'Grupo de amigos',               10, 7);

-- ── 10 órdenes ───────────────────────────────────────────────
INSERT INTO `orden` (`fecha_hora`, `estado`, `total`, `id_mesa`, `id_reserva`, `id_mesero`) VALUES
(CONCAT(CURDATE(), ' 12:30:00'),                    'pagada',     0,  3,  1,  1),
(CONCAT(CURDATE(), ' 13:15:00'),                    'en_cocina',  0,  6,  NULL, 2),
(CONCAT(CURDATE(), ' 19:45:00'),                    'servida',    0,  1,  2,  1),
(DATE_SUB(CONCAT(CURDATE(), ' 20:10:00'), INTERVAL 1 DAY), 'pagada',     0,  6,  14, 3),
(DATE_SUB(CONCAT(CURDATE(), ' 19:30:00'), INTERVAL 1 DAY), 'pagada',     0,  1,  15, 1),
(DATE_SUB(CONCAT(CURDATE(), ' 12:45:00'), INTERVAL 2 DAY), 'pagada',     0,  8,  16, 2),
(DATE_SUB(CONCAT(CURDATE(), ' 20:30:00'), INTERVAL 2 DAY), 'cancelada',  0,  5,  NULL, 3),
(DATE_SUB(CONCAT(CURDATE(), ' 18:50:00'), INTERVAL 3 DAY), 'pagada',     0,  12, 18, 1),
(DATE_SUB(CONCAT(CURDATE(), ' 13:40:00'), INTERVAL 4 DAY), 'pagada',     0,  10, 19, 2),
(DATE_SUB(CONCAT(CURDATE(), ' 21:15:00'), INTERVAL 5 DAY), 'pagada',     0,  7,  20, 3);

-- ── Detalle de las 10 órdenes ─────────────────────────────────
INSERT INTO `detalle_orden` (`cantidad`, `precio_unitario`, `notas`, `id_orden`, `id_plato`) VALUES
-- Orden 1
(2, 18500.00, NULL,           1,  1),
(2, 45000.00, 'Término medio', 1,  4),
(2,  8000.00, NULL,           1,  12),
(1, 14000.00, NULL,           1,  8),
-- Orden 2
(1, 28000.00, 'Sin cebolla',  2,  6),
(1,  6000.00, NULL,           2,  13),
(2, 16000.00, NULL,           2,  3),
-- Orden 3
(2, 12000.00, NULL,           3,  2),
(1, 32000.00, 'Poco picante', 3,  7),
(2,  7000.00, NULL,           3,  14),
-- Orden 4
(2, 42000.00, NULL,           4,  5),
(2, 22000.00, NULL,           4,  15),
(1, 13000.00, NULL,           4,  9),
-- Orden 5
(1, 45000.00, 'Término tres cuartos', 5, 4),
(2,  5000.00, NULL,           5,  11),
(1, 15000.00, NULL,           5,  10),
-- Orden 6
(3, 28000.00, NULL,           6,  6),
(3,  8000.00, NULL,           6,  12),
(2, 14000.00, NULL,           6,  8),
-- Orden 7 (cancelada)
(1, 24000.00, NULL,           7,  16),
(2, 18500.00, NULL,           7,  1),
-- Orden 8
(2, 32000.00, NULL,           8,  7),
(2, 13000.00, NULL,           8,  9),
(2,  6000.00, NULL,           8,  13),
-- Orden 9
(1, 16000.00, NULL,           9,  3),
(1, 12000.00, NULL,           9,  2),
(2,  7000.00, NULL,           9,  14),
-- Orden 10
(4, 45000.00, NULL,           10, 4),
(4, 22000.00, NULL,           10, 15),
(4,  8000.00, NULL,           10, 12);

-- Recalcular totales de cada orden
UPDATE `orden` SET `total` = (
    SELECT COALESCE(SUM(`cantidad` * `precio_unitario`), 0)
    FROM `detalle_orden`
    WHERE `id_orden` = `orden`.`id_orden`
);
