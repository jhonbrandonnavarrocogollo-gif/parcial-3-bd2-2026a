<?php
require_once __DIR__ . '/../config/database.php';

class Plato {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(int $limit = 12, int $offset = 0, string $search = '', int $catId = 0): array {
        $where = [];
        $params = [];
        if ($search) {
            $where[] = "(p.nombre LIKE :s OR p.descripcion LIKE :s)";
            $params[':s'] = "%$search%";
        }
        if ($catId > 0) {
            $where[] = "p.id_categoria = :cat";
            $params[':cat'] = $catId;
        }
        $sql = "SELECT p.*, c.nombre AS cat_nombre FROM plato p JOIN categoria c ON c.id_categoria=p.id_categoria";
        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY p.nombre LIMIT :l OFFSET :o";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(string $search = '', int $catId = 0): int {
        $where = [];
        $params = [];
        if ($search) { $where[] = "(p.nombre LIKE :s OR p.descripcion LIKE :s)"; $params[':s'] = "%$search%"; }
        if ($catId > 0) { $where[] = "p.id_categoria = :cat"; $params[':cat'] = $catId; }
        $sql = "SELECT COUNT(*) FROM plato p JOIN categoria c ON c.id_categoria=p.id_categoria";
        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT p.*, c.nombre AS cat_nombre FROM plato p JOIN categoria c ON c.id_categoria=p.id_categoria WHERE p.id_plato=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO plato (nombre, descripcion, precio, disponible, id_categoria) VALUES (:nombre, :desc, :precio, :disp, :cat)"
        );
        return $stmt->execute([
            ':nombre' => htmlspecialchars(trim($data['nombre'])),
            ':desc'   => htmlspecialchars(trim($data['descripcion'] ?? '')),
            ':precio' => (float)$data['precio'],
            ':disp'   => isset($data['disponible']) ? 1 : 0,
            ':cat'    => (int)$data['id_categoria'],
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE plato SET nombre=:nombre, descripcion=:desc, precio=:precio, disponible=:disp, id_categoria=:cat WHERE id_plato=:id"
        );
        return $stmt->execute([
            ':nombre' => htmlspecialchars(trim($data['nombre'])),
            ':desc'   => htmlspecialchars(trim($data['descripcion'] ?? '')),
            ':precio' => (float)$data['precio'],
            ':disp'   => isset($data['disponible']) ? 1 : 0,
            ':cat'    => (int)$data['id_categoria'],
            ':id'     => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM plato WHERE id_plato=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function getAllDisponibles(): array {
        return $this->db->query("SELECT p.*, c.nombre AS cat_nombre FROM plato p JOIN categoria c ON c.id_categoria=p.id_categoria WHERE p.disponible=1 ORDER BY p.nombre")->fetchAll();
    }

    public function getDisponibles(int $limit = 12, int $offset = 0, string $search = '', int $catId = 0): array {
        $where = ['p.disponible = 1'];
        $params = [];
        if ($search) {
            $where[] = "(p.nombre LIKE :s OR p.descripcion LIKE :s)";
            $params[':s'] = "%$search%";
        }
        if ($catId > 0) {
            $where[] = "p.id_categoria = :cat";
            $params[':cat'] = $catId;
        }
        $sql = "SELECT p.*, c.nombre AS cat_nombre FROM plato p JOIN categoria c ON c.id_categoria=p.id_categoria
                WHERE " . implode(' AND ', $where) . " ORDER BY c.nombre, p.nombre LIMIT :l OFFSET :o";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countDisponibles(string $search = '', int $catId = 0): int {
        $where = ['p.disponible = 1'];
        $params = [];
        if ($search) {
            $where[] = "(p.nombre LIKE :s OR p.descripcion LIKE :s)";
            $params[':s'] = "%$search%";
        }
        if ($catId > 0) {
            $where[] = "p.id_categoria = :cat";
            $params[':cat'] = $catId;
        }
        $sql = "SELECT COUNT(*) FROM plato p WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getMasVendidos(int $limit = 10): array {
        $stmt = $this->db->prepare(
            "SELECT p.nombre, SUM(d.cantidad) AS total_vendido
             FROM detalle_orden d JOIN plato p ON p.id_plato=d.id_plato
             GROUP BY d.id_plato ORDER BY total_vendido DESC LIMIT :l"
        );
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
