-- ============================================================
--  DDL — Sistema de Reservas de Restaurante
--  Motor: MariaDB
--  Normalización: 3FN
--  Compatible con DBeaver
--
--  INSTRUCCIONES:
--  1. En DBeaver selecciona la base de datos reservas_restaurante
--  2. Abre este archivo
--  3. Ejecuta TODO el script con Alt+X
-- ============================================================

-- ============================================================
--  MESA
-- ============================================================
CREATE DATABASE reservas_restaurante
CREATE TABLE reservas_restaurante.mesa (
    id_mesa       INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    numero_mesa   SMALLINT     NOT NULL UNIQUE,
    capacidad     SMALLINT     NOT NULL CHECK (capacidad BETWEEN 1 AND 20),
    ubicacion     VARCHAR(80)  NOT NULL,
    estado        ENUM('disponible','ocupada','reservada','mantenimiento') NOT NULL DEFAULT 'disponible'
);

-- ============================================================
--  CLIENTE
-- ============================================================

CREATE TABLE reservas_restaurante.cliente (
    id_cliente  INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80)  NOT NULL,
    apellido    VARCHAR(80)  NOT NULL,
    telefono    VARCHAR(20),
    email       VARCHAR(120) UNIQUE
);

-- ============================================================
--  MESERO
-- ============================================================

CREATE TABLE reservas_restaurante.mesero (
    id_mesero   INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80) NOT NULL,
    apellido    VARCHAR(80) NOT NULL,
    telefono    VARCHAR(20)
);

-- ============================================================
--  CATEGORIA
-- ============================================================

CREATE TABLE reservas_restaurante.categoria (
    id_categoria  INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(60) NOT NULL UNIQUE,
    descripcion   VARCHAR(200)
);

-- ============================================================
--  PLATO
-- ============================================================

CREATE TABLE reservas_restaurante.plato (
    id_plato      INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(120)  NOT NULL,
    descripcion   VARCHAR(300),
    precio        DECIMAL(10,2) NOT NULL CHECK (precio >= 0),
    disponible    TINYINT(1)    NOT NULL DEFAULT 1,
    id_categoria  INT           NOT NULL,
    CONSTRAINT fk_plato_categoria FOREIGN KEY (id_categoria)
        REFERENCES reservas_restaurante.categoria (id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- ============================================================
--  RESERVA
-- ============================================================

CREATE TABLE reservas_restaurante.reserva (
    id_reserva        INT      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    fecha_hora_inicio DATETIME NOT NULL,
    fecha_hora_fin    DATETIME NOT NULL CHECK (fecha_hora_fin > fecha_hora_inicio),
    num_personas      SMALLINT NOT NULL CHECK (num_personas BETWEEN 1 AND 50),
    estado            ENUM('pendiente','confirmada','cancelada','completada') NOT NULL DEFAULT 'pendiente',
    notas             VARCHAR(300),
    id_cliente        INT      NOT NULL,
    id_mesa           INT      NOT NULL,
    CONSTRAINT fk_reserva_cliente FOREIGN KEY (id_cliente)
        REFERENCES reservas_restaurante.cliente (id_cliente)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reserva_mesa FOREIGN KEY (id_mesa)
        REFERENCES reservas_restaurante.mesa (id_mesa)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_reserva_mesa_horario
    ON reservas_restaurante.reserva (id_mesa, fecha_hora_inicio, fecha_hora_fin);

-- ============================================================
--  ORDEN (cabecera)
-- ============================================================

CREATE TABLE reservas_restaurante.orden (
    id_orden    INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
    fecha_hora  DATETIME      NOT NULL DEFAULT NOW(),
    estado      ENUM('recibida','en_cocina','servida','pagada','cancelada') NOT NULL DEFAULT 'recibida',
    total       DECIMAL(12,2) NOT NULL DEFAULT 0 CHECK (total >= 0),
    id_mesa     INT           NOT NULL,
    id_reserva  INT,
    id_mesero   INT,
    CONSTRAINT fk_orden_mesa    FOREIGN KEY (id_mesa)
        REFERENCES reservas_restaurante.mesa (id_mesa)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_orden_reserva FOREIGN KEY (id_reserva)
        REFERENCES reservas_restaurante.reserva (id_reserva)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_orden_mesero  FOREIGN KEY (id_mesero)
        REFERENCES reservas_restaurante.mesero (id_mesero)
        ON UPDATE CASCADE ON DELETE SET NULL
);

-- ============================================================
--  DETALLE_ORDEN
-- ============================================================

CREATE TABLE reservas_restaurante.detalle_orden (
    id_detalle      INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cantidad        SMALLINT      NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    notas           VARCHAR(200),
    id_orden        INT           NOT NULL,
    id_plato        INT           NOT NULL,
    CONSTRAINT fk_detalle_orden FOREIGN KEY (id_orden)
        REFERENCES reservas_restaurante.orden (id_orden)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_plato FOREIGN KEY (id_plato)
        REFERENCES reservas_restaurante.plato (id_plato)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- ============================================================
--  TRIGGERS: Validación de conflicto de horario (RF4)
-- ============================================================

DELIMITER $$

CREATE TRIGGER reservas_restaurante.trg_conflicto_reserva_insert
BEFORE INSERT ON reservas_restaurante.reserva
FOR EACH ROW
BEGIN
    DECLARE conflictos INT DEFAULT 0;
    SELECT COUNT(*) INTO conflictos
    FROM   reservas_restaurante.reserva
    WHERE  id_mesa = NEW.id_mesa
      AND  estado  NOT IN ('cancelada', 'completada')
      AND  fecha_hora_inicio < NEW.fecha_hora_fin
      AND  fecha_hora_fin    > NEW.fecha_hora_inicio;
    IF conflictos > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La mesa ya tiene una reserva activa que se superpone con el horario solicitado.';
    END IF;
END$$

CREATE TRIGGER reservas_restaurante.trg_conflicto_reserva_update
BEFORE UPDATE ON reservas_restaurante.reserva
FOR EACH ROW
BEGIN
    DECLARE conflictos INT DEFAULT 0;
    SELECT COUNT(*) INTO conflictos
    FROM   reservas_restaurante.reserva
    WHERE  id_mesa     = NEW.id_mesa
      AND  estado      NOT IN ('cancelada', 'completada')
      AND  id_reserva  <> OLD.id_reserva
      AND  fecha_hora_inicio < NEW.fecha_hora_fin
      AND  fecha_hora_fin    > NEW.fecha_hora_inicio;
    IF conflictos > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La mesa ya tiene una reserva activa que se superpone con el horario solicitado.';
    END IF;
END$$

-- ============================================================
--  TRIGGERS: Recálculo automático del total de la orden
-- ============================================================

CREATE TRIGGER reservas_restaurante.trg_total_orden_insert
AFTER INSERT ON reservas_restaurante.detalle_orden
FOR EACH ROW
BEGIN
    UPDATE reservas_restaurante.orden
    SET total = (
        SELECT COALESCE(SUM(cantidad * precio_unitario), 0)
        FROM   reservas_restaurante.detalle_orden
        WHERE  id_orden = NEW.id_orden
    )
    WHERE id_orden = NEW.id_orden;
END$$

CREATE TRIGGER reservas_restaurante.trg_total_orden_update
AFTER UPDATE ON reservas_restaurante.detalle_orden
FOR EACH ROW
BEGIN
    UPDATE reservas_restaurante.orden
    SET total = (
        SELECT COALESCE(SUM(cantidad * precio_unitario), 0)
        FROM   reservas_restaurante.detalle_orden
        WHERE  id_orden = NEW.id_orden
    )
    WHERE id_orden = NEW.id_orden;
END$$

CREATE TRIGGER reservas_restaurante.trg_total_orden_delete
AFTER DELETE ON reservas_restaurante.detalle_orden
FOR EACH ROW
BEGIN
    UPDATE reservas_restaurante.orden
    SET total = (
        SELECT COALESCE(SUM(cantidad * precio_unitario), 0)
        FROM   reservas_restaurante.detalle_orden
        WHERE  id_orden = OLD.id_orden
    )
    WHERE id_orden = OLD.id_orden;
END$$

DELIMITER ;

-- ============================================================
--  VISTAS (RF7)
-- ============================================================

CREATE OR REPLACE VIEW reservas_restaurante.vw_ocupacion_por_dia AS
SELECT
    DATE(fecha_hora_inicio)                                        AS dia,
    COUNT(*)                                                       AS total_reservas,
    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END)        AS completadas,
    SUM(CASE WHEN estado = 'cancelada'  THEN 1 ELSE 0 END)        AS canceladas,
    SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END)        AS confirmadas
FROM reservas_restaurante.reserva
GROUP BY DATE(fecha_hora_inicio)
ORDER BY dia DESC;

CREATE OR REPLACE VIEW reservas_restaurante.vw_mesas_mas_reservadas AS
SELECT
    m.id_mesa,
    m.numero_mesa,
    m.ubicacion,
    m.capacidad,
    COUNT(r.id_reserva)                                            AS total_reservas,
    SUM(CASE WHEN r.estado = 'completada' THEN 1 ELSE 0 END)      AS reservas_completadas
FROM reservas_restaurante.mesa m
LEFT JOIN reservas_restaurante.reserva r ON r.id_mesa = m.id_mesa
GROUP BY m.id_mesa, m.numero_mesa, m.ubicacion, m.capacidad
ORDER BY total_reservas DESC;

