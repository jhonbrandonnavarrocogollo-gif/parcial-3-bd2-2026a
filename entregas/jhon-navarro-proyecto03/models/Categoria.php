<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM categoria ORDER BY nombre")->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM categoria WHERE id_categoria=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO categoria (nombre, descripcion) VALUES (:nombre, :desc)");
        return $stmt->execute([
            ':nombre' => htmlspecialchars(trim($data['nombre'])),
            ':desc'   => htmlspecialchars(trim($data['descripcion'] ?? '')),
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE categoria SET nombre=:nombre, descripcion=:desc WHERE id_categoria=:id");
        return $stmt->execute([
            ':nombre' => htmlspecialchars(trim($data['nombre'])),
            ':desc'   => htmlspecialchars(trim($data['descripcion'] ?? '')),
            ':id'     => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM categoria WHERE id_categoria=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM categoria")->fetchColumn();
    }
}
