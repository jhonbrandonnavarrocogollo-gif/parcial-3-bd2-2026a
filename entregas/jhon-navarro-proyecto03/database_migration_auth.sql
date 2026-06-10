-- ═══════════════════════════════════════════════════════════════
-- Migración: Autenticación y roles (ejecutar sobre BD existente)
-- No elimina tablas existentes. Solo agrega lo necesario.
-- ═══════════════════════════════════════════════════════════════
USE `reservas_restaurante`;

CREATE TABLE IF NOT EXISTS `usuario` (
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

-- Meseros de ejemplo (contraseña: 1234)
INSERT INTO `usuario` (`email`, `password`, `rol`, `estado`, `id_mesero`) VALUES
('pedro.ramirez@restaurante.com', '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 1),
('laura.castro@restaurante.com', '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 2),
('diego.vargas@restaurante.com', '$2y$10$3c5EopfkCvSe3NfAjXZBVe9AjUR50jg5yYzrBW17e6cPNbnwK9RHu', 'mesero', 'activo', 3)
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`);
