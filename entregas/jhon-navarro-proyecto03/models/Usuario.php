<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            "SELECT u.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido,
                    m.nombre AS mes_nombre, m.apellido AS mes_apellido
             FROM usuario u
             LEFT JOIN cliente c ON c.id_cliente = u.id_cliente
             LEFT JOIN mesero m ON m.id_mesero = u.id_mesero
             WHERE u.email = :email AND u.estado = 'activo'"
        );
        $stmt->execute([':email' => strtolower(trim($email))]);
        $row = $stmt->fetch();
        if ($row) {
            $row['nombre']   = $row['cli_nombre'] ?? $row['mes_nombre'] ?? '';
            $row['apellido'] = $row['cli_apellido'] ?? $row['mes_apellido'] ?? '';
        }
        return $row;
    }

    public function emailExists(string $email, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM usuario WHERE email = :email AND id_usuario != :id"
        );
        $stmt->execute([':email' => strtolower(trim($email)), ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function registerMesero(array $data): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO mesero (nombre, apellido, telefono)
                 VALUES (:nombre, :apellido, :telefono)"
            );
            $stmt->execute([
                ':nombre'   => htmlspecialchars(trim($data['nombre'])),
                ':apellido' => htmlspecialchars(trim($data['apellido'])),
                ':telefono' => htmlspecialchars(trim($data['telefono'] ?? '')),
            ]);
            $idMesero = (int)$this->db->lastInsertId();

            $stmt = $this->db->prepare(
                "INSERT INTO usuario (email, password, rol, estado, id_mesero)
                 VALUES (:email, :password, 'mesero', 'activo', :id_mesero)"
            );
            $stmt->execute([
                ':email'     => strtolower(trim($data['email'])),
                ':password'  => password_hash($data['password'], PASSWORD_DEFAULT),
                ':id_mesero' => $idMesero,
            ]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function registerCliente(array $data): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO cliente (nombre, apellido, telefono, email)
                 VALUES (:nombre, :apellido, :telefono, :email)"
            );
            $stmt->execute([
                ':nombre'   => htmlspecialchars(trim($data['nombre'])),
                ':apellido' => htmlspecialchars(trim($data['apellido'])),
                ':telefono' => htmlspecialchars(trim($data['telefono'] ?? '')),
                ':email'    => strtolower(trim($data['email'])),
            ]);
            $idCliente = (int)$this->db->lastInsertId();

            $stmt = $this->db->prepare(
                "INSERT INTO usuario (email, password, rol, estado, id_cliente)
                 VALUES (:email, :password, 'cliente', 'activo', :id_cliente)"
            );
            $stmt->execute([
                ':email'      => strtolower(trim($data['email'])),
                ':password'   => password_hash($data['password'], PASSWORD_DEFAULT),
                ':id_cliente' => $idCliente,
            ]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function verifyLogin(string $email, string $password): array|false {
        $usuario = $this->findByEmail($email);
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            return false;
        }
        return $usuario;
    }

    public function updateEmailByCliente(int $idCliente, string $email): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuario SET email = :email WHERE id_cliente = :id"
        );
        return $stmt->execute([
            ':email' => strtolower(trim($email)),
            ':id'    => $idCliente,
        ]);
    }

    public function getIdByCliente(int $idCliente): ?int {
        $stmt = $this->db->prepare("SELECT id_usuario FROM usuario WHERE id_cliente = :id");
        $stmt->execute([':id' => $idCliente]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }
}
