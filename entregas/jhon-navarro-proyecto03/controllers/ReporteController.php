<?php
require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../models/Orden.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../config/database.php';

class ReporteController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function index(): void {
        // Reporte 1: Ocupación diaria (últimos 30 días)
        $ocupacion = $this->db->query(
            "SELECT DATE(r.fecha_hora_inicio) AS dia,
                    COUNT(*) AS total_reservas,
                    COUNT(DISTINCT r.id_mesa) AS mesas_usadas
             FROM reserva r
             WHERE r.fecha_hora_inicio >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY dia ORDER BY dia DESC"
        )->fetchAll();

        // Reporte 2: Mesas más reservadas
        $mesasTop = $this->db->query(
            "SELECT m.numero_mesa, m.ubicacion,
                    COUNT(r.id_reserva) AS total_reservas,
                    SUM(CASE WHEN r.estado='completada' THEN 1 ELSE 0 END) AS completadas
             FROM mesa m
             LEFT JOIN reserva r ON r.id_mesa = m.id_mesa
             GROUP BY m.id_mesa
             ORDER BY total_reservas DESC
             LIMIT 10"
        )->fetchAll();

        // Reporte 3: Ventas por fecha (últimos 30 días)
        $ventas = $this->db->query(
            "SELECT DATE(fecha_hora) AS dia, SUM(total) AS total_ventas, COUNT(*) AS num_ordenes
             FROM orden
             WHERE estado='pagada' AND fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY dia ORDER BY dia DESC"
        )->fetchAll();

        // Reporte 4: Productos más vendidos
        $productosTop = $this->db->query(
            "SELECT p.nombre, c.nombre AS categoria,
                    SUM(d.cantidad) AS total_vendido,
                    SUM(d.cantidad * d.precio_unitario) AS ingresos
             FROM detalle_orden d
             JOIN plato p ON p.id_plato = d.id_plato
             JOIN categoria c ON c.id_categoria = p.id_categoria
             GROUP BY d.id_plato
             ORDER BY total_vendido DESC
             LIMIT 10"
        )->fetchAll();

        require __DIR__ . '/../views/reportes/index.php';
    }
}
