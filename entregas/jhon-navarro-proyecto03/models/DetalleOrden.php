<?php
require_once __DIR__ . '/../config/database.php';

class DetalleOrden {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByOrden(int $idOrden): array {
        $stmt = $this->db->prepare(
            "SELECT d.*, p.nombre AS plato_nombre, p.precio AS precio_plato,
                    (d.cantidad * d.precio_unitario) AS subtotal
             FROM detalle_orden d
             JOIN plato p ON p.id_plato = d.id_plato
             WHERE d.id_orden = :id
             ORDER BY d.id_detalle"
        );
        $stmt->execute([':id' => $idOrden]);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO detalle_orden (cantidad, precio_unitario, notas, id_orden, id_plato)
             VALUES (:cant, :precio, :notas, :orden, :plato)"
        );
        return $stmt->execute([
            ':cant'   => (int)$data['cantidad'],
            ':precio' => (float)$data['precio_unitario'],
            ':notas'  => htmlspecialchars(trim($data['notas'] ?? '')),
            ':orden'  => (int)$data['id_orden'],
            ':plato'  => (int)$data['id_plato'],
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM detalle_orden WHERE id_detalle=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function deleteByOrden(int $idOrden): bool {
        $stmt = $this->db->prepare("DELETE FROM detalle_orden WHERE id_orden=:id");
        return $stmt->execute([':id' => $idOrden]);
    }
}
