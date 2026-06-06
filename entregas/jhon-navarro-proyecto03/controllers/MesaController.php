<?php
require_once __DIR__ . '/../models/Mesa.php';

class MesaController {
    private Mesa $model;

    public function __construct() {
        $this->model = new Mesa();
    }

    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $mesas = $this->model->getAll($limit, $offset, $search);
        $total = $this->model->count($search);
        $pages = ceil($total / $limit);

        require __DIR__ . '/../views/mesas/index.php';
    }

    public function create(): void {
        $errors = [];
        $data   = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                if ($this->model->numeroExists((int)$data['numero_mesa'])) {
                    $errors['numero_mesa'] = 'Ya existe una mesa con ese número.';
                } else {
                    $this->model->create($data);
                    header('Location: index.php?module=mesas&action=index&success=created');
                    exit;
                }
            }
        }
        require __DIR__ . '/../views/mesas/form.php';
    }

    public function edit(): void {
        $id   = (int)($_GET['id'] ?? 0);
        $errors = [];
        $data = $this->model->getById($id);
        if (!$data) { header('Location: index.php?module=mesas&action=index'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                if ($this->model->numeroExists((int)$data['numero_mesa'], $id)) {
                    $errors['numero_mesa'] = 'Ya existe otra mesa con ese número.';
                } else {
                    $this->model->update($id, $data);
                    header('Location: index.php?module=mesas&action=index&success=updated');
                    exit;
                }
            }
        }
        require __DIR__ . '/../views/mesas/form.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        try {
            $this->model->delete($id);
            header('Location: index.php?module=mesas&action=index&success=deleted');
        } catch (PDOException $e) {
            header('Location: index.php?module=mesas&action=index&error=fk');
        }
        exit;
    }

    private function validate(array $data): array {
        $errors = [];
        if (!isset($data['numero_mesa']) || (int)$data['numero_mesa'] <= 0) $errors['numero_mesa'] = 'Número de mesa inválido.';
        if (!isset($data['capacidad']) || (int)$data['capacidad'] < 1 || (int)$data['capacidad'] > 20) $errors['capacidad'] = 'Capacidad entre 1 y 20.';
        if (empty(trim($data['ubicacion'] ?? ''))) $errors['ubicacion'] = 'La ubicación es obligatoria.';
        $estados = ['disponible','ocupada','reservada','mantenimiento'];
        if (!in_array($data['estado'] ?? '', $estados)) $errors['estado'] = 'Estado inválido.';
        return $errors;
    }
}
