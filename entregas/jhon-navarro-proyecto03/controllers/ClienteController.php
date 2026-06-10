<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private Cliente $model;

    public function __construct() {
        Auth::requireRole('mesero');
        $this->model = new Cliente();
    }

    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $clientes = $this->model->getAll($limit, $offset, $search);
        $total    = $this->model->count($search);
        $pages    = ceil($total / $limit);

        require __DIR__ . '/../views/clientes/index.php';
    }

    public function create(): void {
        $errors = [];
        $data   = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                if ($this->model->emailExists($data['email'])) {
                    $errors['email'] = 'El correo ya está registrado.';
                } else {
                    $this->model->create($data);
                    header('Location: index.php?module=clientes&action=index&success=created');
                    exit;
                }
            }
        }
        require __DIR__ . '/../views/clientes/form.php';
    }

    public function edit(): void {
        $id     = (int)($_GET['id'] ?? 0);
        $errors = [];
        $data   = $this->model->getById($id);
        if (!$data) { header('Location: index.php?module=clientes&action=index'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                if ($this->model->emailExists($data['email'], $id)) {
                    $errors['email'] = 'El correo ya está en uso por otro cliente.';
                } else {
                    $this->model->update($id, $data);
                    header('Location: index.php?module=clientes&action=index&success=updated');
                    exit;
                }
            }
        }
        require __DIR__ . '/../views/clientes/form.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        try {
            $this->model->delete($id);
            header('Location: index.php?module=clientes&action=index&success=deleted');
        } catch (PDOException $e) {
            header('Location: index.php?module=clientes&action=index&error=fk');
        }
        exit;
    }

    private function validate(array $data): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? '')))   $errors['nombre']   = 'El nombre es obligatorio.';
        if (empty(trim($data['apellido'] ?? '')))  $errors['apellido'] = 'El apellido es obligatorio.';
        if (empty(trim($data['email'] ?? '')))     $errors['email']    = 'El correo es obligatorio.';
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Correo inválido.';
        return $errors;
    }
}
