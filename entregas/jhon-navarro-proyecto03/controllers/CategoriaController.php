<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController {
    private Categoria $model;

    public function __construct() {
        Auth::requireRole('mesero');
        $this->model = new Categoria();
    }

    public function index(): void {
        $categorias = $this->model->getAll();
        require __DIR__ . '/../views/categorias/index.php';
    }

    public function create(): void {
        $errors = [];
        $data   = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->create($data);
                header('Location: index.php?module=categorias&action=index&success=created');
                exit;
            }
        }
        require __DIR__ . '/../views/categorias/form.php';
    }

    public function edit(): void {
        $id   = (int)($_GET['id'] ?? 0);
        $errors = [];
        $data = $this->model->getById($id);
        if (!$data) { header('Location: index.php?module=categorias&action=index'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->update($id, $data);
                header('Location: index.php?module=categorias&action=index&success=updated');
                exit;
            }
        }
        require __DIR__ . '/../views/categorias/form.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        try {
            $this->model->delete($id);
            header('Location: index.php?module=categorias&action=index&success=deleted');
        } catch (PDOException $e) {
            header('Location: index.php?module=categorias&action=index&error=fk');
        }
        exit;
    }

    private function validate(array $data): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? ''))) $errors['nombre'] = 'El nombre es obligatorio.';
        return $errors;
    }
}
