<?php
require_once __DIR__ . '/../config/database.php';

class Mesero {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM mesero ORDER BY nombre")->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM mesero WHERE id_mesero=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO mesero (nombre, apellido, telefono) VALUES (:nombre, :apellido, :tel)");
        return $stmt->execute([
            ':nombre'   => htmlspecialchars(trim($data['nombre'])),
            ':apellido' => htmlspecialchars(trim($data['apellido'])),
            ':tel'      => htmlspecialchars(trim($data['telefono'] ?? '')),
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE mesero SET nombre=:nombre, apellido=:apellido, telefono=:tel WHERE id_mesero=:id");
        return $stmt->execute([
            ':nombre'   => htmlspecialchars(trim($data['nombre'])),
            ':apellido' => htmlspecialchars(trim($data['apellido'])),
            ':tel'      => htmlspecialchars(trim($data['telefono'] ?? '')),
            ':id'       => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM mesero WHERE id_mesero=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM mesero")->fetchColumn();
    }
}
