<?php
require_once __DIR__ . '/../models/Mesero.php';

class MeseroController {
    private Mesero $model;

    public function __construct() {
        $this->model = new Mesero();
    }

    public function index(): void {
        $meseros = $this->model->getAll();
        require __DIR__ . '/../views/meseros/index.php';
    }

    public function create(): void {
        $errors = [];
        $data   = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->create($data);
                header('Location: index.php?module=meseros&action=index&success=created');
                exit;
            }
        }
        require __DIR__ . '/../views/meseros/form.php';
    }

    public function edit(): void {
        $id     = (int)($_GET['id'] ?? 0);
        $errors = [];
        $data   = $this->model->getById($id);
        if (!$data) { header('Location: index.php?module=meseros&action=index'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = array_merge($data, $_POST);
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->update($id, $data);
                header('Location: index.php?module=meseros&action=index&success=updated');
                exit;
            }
        }
        require __DIR__ . '/../views/meseros/form.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        try {
            $this->model->delete($id);
            header('Location: index.php?module=meseros&action=index&success=deleted');
        } catch (PDOException $e) {
            header('Location: index.php?module=meseros&action=index&error=fk');
        }
        exit;
    }

    private function validate(array $data): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? '')))   $errors['nombre']   = 'El nombre es obligatorio.';
        if (empty(trim($data['apellido'] ?? '')))  $errors['apellido'] = 'El apellido es obligatorio.';
        return $errors;
    }
}
