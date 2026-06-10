<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Usuario.php';

class PerfilController {
    private Cliente $model;
    private Usuario $usuarioModel;

    public function __construct() {
        Auth::requireRole('cliente');
        $this->model        = new Cliente();
        $this->usuarioModel = new Usuario();
    }

    public function index(): void {
        $id   = Auth::idCliente();
        $data = $this->model->getById($id);
        if (!$data) {
            header('Location: index.php?module=dashboard');
            exit;
        }
        require __DIR__ . '/../views/perfil/index.php';
    }

    public function edit(): void {
        $id     = Auth::idCliente();
        $errors = [];
        $data   = $this->model->getById($id);
        if (!$data) {
            header('Location: index.php?module=dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validate($data);
            if (empty($errors)) {
                $usuarioId = $this->usuarioModel->getIdByCliente($id) ?? 0;
                if ($this->model->emailExists($data['email'], $id) || $this->usuarioModel->emailExists($data['email'], $usuarioId)) {
                    $errors['email'] = 'El correo ya está en uso.';
                } else {
                    $this->model->update($id, $data);
                    $this->usuarioModel->updateEmailByCliente($id, $data['email']);
                    $_SESSION['usuario']['email'] = strtolower(trim($data['email']));
                    $_SESSION['usuario']['nombre'] = htmlspecialchars(trim($data['nombre']));
                    $_SESSION['usuario']['apellido'] = htmlspecialchars(trim($data['apellido']));
                    header('Location: index.php?module=perfil&success=updated');
                    exit;
                }
            }
        }
        require __DIR__ . '/../views/perfil/form.php';
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
