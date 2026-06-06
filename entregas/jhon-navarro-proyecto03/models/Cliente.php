<?php
require_once __DIR__ . '/../config/database.php';

class Cliente {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(int $limit = 10, int $offset = 0, string $search = ''): array {
        if ($search) {
            $stmt = $this->db->prepare(
                "SELECT * FROM cliente WHERE nombre LIKE :s OR apellido LIKE :s OR email LIKE :s ORDER BY nombre LIMIT :l OFFSET :o"
            );
            $like = "%$search%";
            $stmt->bindValue(':s', $like);
            $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM cliente ORDER BY nombre LIMIT :l OFFSET :o");
            $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int {
        if ($search) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM cliente WHERE nombre LIKE :s OR apellido LIKE :s OR email LIKE :s");
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM cliente");
        }
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM cliente WHERE id_cliente = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO cliente (nombre, apellido, telefono, email) VALUES (:nombre, :apellido, :telefono, :email)"
        );
        return $stmt->execute([
            ':nombre'   => htmlspecialchars(trim($data['nombre'])),
            ':apellido' => htmlspecialchars(trim($data['apellido'])),
            ':telefono' => htmlspecialchars(trim($data['telefono'] ?? '')),
            ':email'    => strtolower(trim($data['email'])),
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE cliente SET nombre=:nombre, apellido=:apellido, telefono=:telefono, email=:email WHERE id_cliente=:id"
        );
        return $stmt->execute([
            ':nombre'   => htmlspecialchars(trim($data['nombre'])),
            ':apellido' => htmlspecialchars(trim($data['apellido'])),
            ':telefono' => htmlspecialchars(trim($data['telefono'] ?? '')),
            ':email'    => strtolower(trim($data['email'])),
            ':id'       => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM cliente WHERE id_cliente = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function emailExists(string $email, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM cliente WHERE email = :email AND id_cliente != :id");
        $stmt->execute([':email' => $email, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getAllSimple(): array {
        return $this->db->query("SELECT id_cliente, nombre, apellido FROM cliente ORDER BY nombre")->fetchAll();
    }
}
