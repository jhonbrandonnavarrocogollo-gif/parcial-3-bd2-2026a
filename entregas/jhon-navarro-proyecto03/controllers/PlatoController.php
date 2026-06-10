<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Categoria.php';

class PlatoController {
    private Plato     $model;
    private Categoria $catModel;

    public function __construct() {
        Auth::requireRole('mesero');
        $this->model    = new Plato();
        $this->catModel = new Categoria();
    }

    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $catId   = (int)($_GET['categoria'] ?? 0);
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 12;
        $offset  = ($page - 1) * $limit;
        $platos     = $this->model->getAll($limit, $offset, $search, $catId);
        $total      = $this->model->count($search, $catId);
        $pages      = ceil($total / $limit);
        $categorias = $this->catModel->getAll();
        require __DIR__ . '/../views/platos/index.php';
    }

    public function create(): void {
        $errors     = [];
        $data       = [];
        $categorias = $this->catModel->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->create($data);
                header('Location: index.php?module=platos&action=index&success=created');
                exit;
            }
        }
        require __DIR__ . '/../views/platos/form.php';
    }

    public function edit(): void {
        $id         = (int)($_GET['id'] ?? 0);
        $errors     = [];
        $data       = $this->model->getById($id);
        $categorias = $this->catModel->getAll();
        if (!$data) { header('Location: index.php?module=platos&action=index'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->update($id, $data);
                header('Location: index.php?module=platos&action=index&success=updated');
                exit;
            }
        }
        require __DIR__ . '/../views/platos/form.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        try {
            $this->model->delete($id);
            header('Location: index.php?module=platos&action=index&success=deleted');
        } catch (PDOException $e) {
            header('Location: index.php?module=platos&action=index&error=fk');
        }
        exit;
    }

    private function validate(array $data): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? '')))    $errors['nombre']       = 'El nombre es obligatorio.';
        if (!isset($data['precio']) || (float)$data['precio'] < 0) $errors['precio'] = 'Precio inválido.';
        if (empty($data['id_categoria']))           $errors['id_categoria'] = 'Seleccione una categoría.';
        return $errors;
    }
}
