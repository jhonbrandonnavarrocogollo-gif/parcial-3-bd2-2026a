<?php
require_once __DIR__ . '/../config/database.php';

class Reserva {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(int $limit = 10, int $offset = 0, string $search = ''): array {
        $sql = "SELECT r.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido,
                       m.numero_mesa, m.ubicacion
                FROM reserva r
                JOIN cliente c ON c.id_cliente = r.id_cliente
                JOIN mesa m ON m.id_mesa = r.id_mesa";
        if ($search) {
            $sql .= " WHERE c.nombre LIKE :s OR c.apellido LIKE :s OR m.numero_mesa LIKE :s OR r.estado LIKE :s";
        }
        $sql .= " ORDER BY r.fecha_hora_inicio DESC LIMIT :l OFFSET :o";
        $stmt = $this->db->prepare($sql);
        if ($search) {
            $stmt->bindValue(':s', "%$search%");
        }
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int {
        if ($search) {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM reserva r JOIN cliente c ON c.id_cliente=r.id_cliente JOIN mesa m ON m.id_mesa=r.id_mesa
                 WHERE c.nombre LIKE :s OR c.apellido LIKE :s OR m.numero_mesa LIKE :s OR r.estado LIKE :s"
            );
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM reserva");
        }
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT r.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido,
                    m.numero_mesa, m.ubicacion
             FROM reserva r
             JOIN cliente c ON c.id_cliente = r.id_cliente
             JOIN mesa m ON m.id_mesa = r.id_mesa
             WHERE r.id_reserva = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO reserva (fecha_hora_inicio, fecha_hora_fin, num_personas, estado, notas, id_cliente, id_mesa)
             VALUES (:inicio, :fin, :personas, :estado, :notas, :cliente, :mesa)"
        );
        return $stmt->execute([
            ':inicio'  => $data['fecha_hora_inicio'],
            ':fin'     => $data['fecha_hora_fin'],
            ':personas'=> (int)$data['num_personas'],
            ':estado'  => $data['estado'] ?? 'pendiente',
            ':notas'   => htmlspecialchars(trim($data['notas'] ?? '')),
            ':cliente' => (int)$data['id_cliente'],
            ':mesa'    => (int)$data['id_mesa'],
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE reserva SET fecha_hora_inicio=:inicio, fecha_hora_fin=:fin, num_personas=:personas,
             estado=:estado, notas=:notas, id_cliente=:cliente, id_mesa=:mesa WHERE id_reserva=:id"
        );
        return $stmt->execute([
            ':inicio'  => $data['fecha_hora_inicio'],
            ':fin'     => $data['fecha_hora_fin'],
            ':personas'=> (int)$data['num_personas'],
            ':estado'  => $data['estado'],
            ':notas'   => htmlspecialchars(trim($data['notas'] ?? '')),
            ':cliente' => (int)$data['id_cliente'],
            ':mesa'    => (int)$data['id_mesa'],
            ':id'      => $id,
        ]);
    }

    public function cancelar(int $id): bool {
        $stmt = $this->db->prepare("UPDATE reserva SET estado='cancelada' WHERE id_reserva=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM reserva WHERE id_reserva=:id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Verifica si existe conflicto de horario para la mesa dada.
     * Excluye la reserva con $excludeId si se está editando.
     */
    public function tieneConflicto(int $idMesa, string $inicio, string $fin, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reserva
             WHERE id_mesa = :mesa
               AND id_reserva != :excl
               AND estado NOT IN ('cancelada','completada')
               AND fecha_hora_inicio < :fin
               AND fecha_hora_fin > :inicio"
        );
        $stmt->execute([':mesa' => $idMesa, ':excl' => $excludeId, ':inicio' => $inicio, ':fin' => $fin]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getDelDia(): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido, m.numero_mesa
             FROM reserva r
             JOIN cliente c ON c.id_cliente=r.id_cliente
             JOIN mesa m ON m.id_mesa=r.id_mesa
             WHERE DATE(r.fecha_hora_inicio) = CURDATE()
             ORDER BY r.fecha_hora_inicio"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countHoy(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM reserva WHERE DATE(fecha_hora_inicio)=CURDATE()")->fetchColumn();
    }

    public function getByMesaYFecha(int $idMesa, string $fecha): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido
             FROM reserva r JOIN cliente c ON c.id_cliente=r.id_cliente
             WHERE r.id_mesa=:mesa AND DATE(r.fecha_hora_inicio)=:fecha AND r.estado NOT IN ('cancelada')"
        );
        $stmt->execute([':mesa' => $idMesa, ':fecha' => $fecha]);
        return $stmt->fetchAll();
    }

    public function getReservasPorDia(int $dias = 7): array {
        $stmt = $this->db->prepare(
            "SELECT DATE(fecha_hora_inicio) AS dia, COUNT(*) AS total
             FROM reserva
             WHERE fecha_hora_inicio >= DATE_SUB(CURDATE(), INTERVAL :d DAY)
             GROUP BY dia ORDER BY dia"
        );
        $stmt->execute([':d' => $dias]);
        return $stmt->fetchAll();
    }

    public function getReservasSemana(): array {
        $stmt = $this->db->prepare(
            "SELECT DATE(fecha_hora_inicio) AS dia, COUNT(*) AS total
             FROM reserva
             WHERE fecha_hora_inicio >= CURDATE()
               AND fecha_hora_inicio < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
               AND estado NOT IN ('cancelada')
             GROUP BY dia"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByFecha(string $fecha): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido, m.numero_mesa
             FROM reserva r
             JOIN cliente c ON c.id_cliente = r.id_cliente
             JOIN mesa m ON m.id_mesa = r.id_mesa
             WHERE DATE(r.fecha_hora_inicio) = :fecha
               AND r.estado NOT IN ('cancelada')
             ORDER BY r.fecha_hora_inicio"
        );
        $stmt->execute([':fecha' => $fecha]);
        return $stmt->fetchAll();
    }
}
