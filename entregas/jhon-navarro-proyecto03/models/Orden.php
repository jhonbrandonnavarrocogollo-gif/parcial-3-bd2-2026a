<?php
require_once __DIR__ . '/../config/database.php';

class Orden {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(int $limit = 10, int $offset = 0, string $search = ''): array {
        $sql = "SELECT o.*, m.numero_mesa, ms.nombre AS mesero_nombre, ms.apellido AS mesero_apellido
                FROM orden o
                JOIN mesa m ON m.id_mesa = o.id_mesa
                LEFT JOIN mesero ms ON ms.id_mesero = o.id_mesero";
        if ($search) {
            $sql .= " WHERE m.numero_mesa LIKE :s OR o.estado LIKE :s";
        }
        $sql .= " ORDER BY o.fecha_hora DESC LIMIT :l OFFSET :o";
        $stmt = $this->db->prepare($sql);
        if ($search) $stmt->bindValue(':s', "%$search%");
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int {
        if ($search) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM orden o JOIN mesa m ON m.id_mesa=o.id_mesa WHERE m.numero_mesa LIKE :s OR o.estado LIKE :s");
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM orden");
        }
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT o.*, m.numero_mesa, m.ubicacion,
                    ms.nombre AS mesero_nombre, ms.apellido AS mesero_apellido,
                    r.fecha_hora_inicio AS reserva_inicio
             FROM orden o
             JOIN mesa m ON m.id_mesa=o.id_mesa
             LEFT JOIN mesero ms ON ms.id_mesero=o.id_mesero
             LEFT JOIN reserva r ON r.id_reserva=o.id_reserva
             WHERE o.id_orden=:id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO orden (fecha_hora, estado, total, id_mesa, id_reserva, id_mesero)
             VALUES (NOW(), :estado, 0, :mesa, :reserva, :mesero)"
        );
        $stmt->execute([
            ':estado'  => $data['estado'] ?? 'recibida',
            ':mesa'    => (int)$data['id_mesa'],
            ':reserva' => !empty($data['id_reserva']) ? (int)$data['id_reserva'] : null,
            ':mesero'  => !empty($data['id_mesero']) ? (int)$data['id_mesero'] : null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateTotal(int $id): void {
        $stmt = $this->db->prepare(
            "UPDATE orden SET total = (SELECT COALESCE(SUM(cantidad * precio_unitario),0) FROM detalle_orden WHERE id_orden=:id) WHERE id_orden=:id2"
        );
        $stmt->execute([':id' => $id, ':id2' => $id]);
    }

    public function updateEstado(int $id, string $estado): bool {
        $stmt = $this->db->prepare("UPDATE orden SET estado=:estado WHERE id_orden=:id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM orden WHERE id_orden=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function getActivas(): array {
        return $this->db->query(
            "SELECT o.*, m.numero_mesa FROM orden o JOIN mesa m ON m.id_mesa=o.id_mesa
             WHERE o.estado NOT IN ('pagada','cancelada') ORDER BY o.fecha_hora DESC"
        )->fetchAll();
    }

    public function countActivas(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM orden WHERE estado NOT IN ('pagada','cancelada')")->fetchColumn();
    }

    public function getVentasHoy(): float {
        return (float)$this->db->query("SELECT COALESCE(SUM(total),0) FROM orden WHERE DATE(fecha_hora)=CURDATE() AND estado='pagada'")->fetchColumn();
    }

    public function getVentasPorDia(int $dias = 7): array {
        $stmt = $this->db->prepare(
            "SELECT DATE(fecha_hora) AS dia, SUM(total) AS total_ventas
             FROM orden WHERE estado='pagada' AND fecha_hora >= DATE_SUB(CURDATE(), INTERVAL :d DAY)
             GROUP BY dia ORDER BY dia"
        );
        $stmt->execute([':d' => $dias]);
        return $stmt->fetchAll();
    }

    public function getByCliente(int $idCliente, int $limit = 10, int $offset = 0): array {
        $stmt = $this->db->prepare(
            "SELECT o.*, m.numero_mesa, r.fecha_hora_inicio AS reserva_inicio
             FROM orden o
             JOIN mesa m ON m.id_mesa = o.id_mesa
             JOIN reserva r ON r.id_reserva = o.id_reserva
             WHERE r.id_cliente = :cliente
             ORDER BY o.fecha_hora DESC LIMIT :l OFFSET :o"
        );
        $stmt->bindValue(':cliente', $idCliente, PDO::PARAM_INT);
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByCliente(int $idCliente): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM orden o
             LEFT JOIN reserva r ON r.id_reserva = o.id_reserva
             WHERE r.id_cliente = :cliente"
        );
        $stmt->execute([':cliente' => $idCliente]);
        return (int)$stmt->fetchColumn();
    }

    public function getActivasByCliente(int $idCliente): array {
        $stmt = $this->db->prepare(
            "SELECT o.*, m.numero_mesa FROM orden o
             JOIN reserva r ON r.id_reserva = o.id_reserva
             JOIN mesa m ON m.id_mesa = o.id_mesa
             WHERE r.id_cliente = :cliente AND o.estado NOT IN ('pagada','cancelada')
             ORDER BY o.fecha_hora DESC"
        );
        $stmt->execute([':cliente' => $idCliente]);
        return $stmt->fetchAll();
    }

    public function belongsToCliente(int $idOrden, int $idCliente): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM orden o
             JOIN reserva r ON r.id_reserva = o.id_reserva
             WHERE o.id_orden = :id AND r.id_cliente = :cliente"
        );
        $stmt->execute([':id' => $idOrden, ':cliente' => $idCliente]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
