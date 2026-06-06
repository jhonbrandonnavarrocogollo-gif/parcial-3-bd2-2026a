<?php
require_once __DIR__ . '/../config/database.php';

class Mesa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(int $limit = 10, int $offset = 0, string $search = ''): array {
        if ($search) {
            $stmt = $this->db->prepare(
                "SELECT * FROM mesa WHERE numero_mesa LIKE :s OR ubicacion LIKE :s OR estado LIKE :s ORDER BY numero_mesa LIMIT :l OFFSET :o"
            );
            $like = "%$search%";
            $stmt->bindValue(':s', $like);
            $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM mesa ORDER BY numero_mesa LIMIT :l OFFSET :o");
            $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int {
        if ($search) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM mesa WHERE numero_mesa LIKE :s OR ubicacion LIKE :s OR estado LIKE :s");
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM mesa");
        }
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM mesa WHERE id_mesa = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO mesa (numero_mesa, capacidad, ubicacion, estado) VALUES (:numero, :capacidad, :ubicacion, :estado)"
        );
        return $stmt->execute([
            ':numero'    => (int)$data['numero_mesa'],
            ':capacidad' => (int)$data['capacidad'],
            ':ubicacion' => htmlspecialchars(trim($data['ubicacion'])),
            ':estado'    => $data['estado'],
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE mesa SET numero_mesa=:numero, capacidad=:capacidad, ubicacion=:ubicacion, estado=:estado WHERE id_mesa=:id"
        );
        return $stmt->execute([
            ':numero'    => (int)$data['numero_mesa'],
            ':capacidad' => (int)$data['capacidad'],
            ':ubicacion' => htmlspecialchars(trim($data['ubicacion'])),
            ':estado'    => $data['estado'],
            ':id'        => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM mesa WHERE id_mesa = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getDisponibles(): array {
        return $this->db->query("SELECT * FROM mesa WHERE estado='disponible' ORDER BY numero_mesa")->fetchAll();
    }

    public function getAllSimple(): array {
        return $this->db->query("SELECT id_mesa, numero_mesa, capacidad, ubicacion, estado FROM mesa ORDER BY numero_mesa")->fetchAll();
    }

    public function countDisponibles(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM mesa WHERE estado='disponible'")->fetchColumn();
    }

    public function numeroExists(int $numero, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM mesa WHERE numero_mesa = :n AND id_mesa != :id");
        $stmt->execute([':n' => $numero, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getMesasMasReservadas(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT m.numero_mesa, m.ubicacion, COUNT(r.id_reserva) AS total_reservas
             FROM mesa m
             LEFT JOIN reserva r ON r.id_mesa = m.id_mesa AND r.estado NOT IN ('cancelada')
             GROUP BY m.id_mesa
             ORDER BY total_reservas DESC
             LIMIT :l"
        );
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
