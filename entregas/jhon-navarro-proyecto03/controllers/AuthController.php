<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Cliente.php';

class AuthController {
    private Usuario $model;
    private Cliente $clienteModel;

    public function __construct() {
        $this->model        = new Usuario();
        $this->clienteModel = new Cliente();
    }

    public function login(): void {
        Auth::guestOnly();
        $errors = [];
        $email  = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $errors['general'] = 'Ingrese correo y contraseña.';
            } else {
                $usuario = $this->model->verifyLogin($email, $password);
                if ($usuario) {
                    Auth::login($usuario);
                    Auth::redirectToDashboard();
                } else {
                    $errors['general'] = 'Credenciales incorrectas.';
                }
            }
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function register(): void {
        Auth::guestOnly();
        $errors = [];
        $data   = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $errors = $this->validateRegister($data);
            if (empty($errors)) {
                try {
                    $this->model->registerCliente($data);
                    $usuario = $this->model->verifyLogin($data['email'], $data['password']);
                    if ($usuario) {
                        Auth::login($usuario);
                    }
                    header('Location: index.php?module=dashboard&success=registered');
                    exit;
                } catch (PDOException $e) {
                    $errors['general'] = 'No se pudo completar el registro. El correo podría estar en uso.';
                }
            }
        }
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register_mesero(): void {
        Auth::guestOnly();
        $errors = [];
        $data   = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validateRegisterMesero($data);
            if (empty($errors)) {
                try {
                    $this->model->registerMesero($data);
                    header('Location: index.php?module=auth&action=login&success=mesero_registered');
                    exit;
                } catch (PDOException $e) {
                    $errors['general'] = 'No se pudo completar el registro. El correo podría estar en uso.';
                }
            }
        }
        require __DIR__ . '/../views/auth/register_mesero.php';
    }

    public function logout(): void {
        Auth::logout();
        header('Location: index.php?module=auth&action=login');
        exit;
    }

    private function validateRegisterMesero(array $data): array {
        $errors = $this->validateRegister($data);

        if (empty(trim($data['admin_password'] ?? ''))) {
            $errors['admin_password'] = 'La contraseña de administrador es obligatoria.';
        } elseif (!Auth::verifyAdminRegisterPassword($data['admin_password'])) {
            $errors['admin_password'] = 'Contraseña de administrador incorrecta.';
        }

        return $errors;
    }

    private function validateRegister(array $data): array {
        $errors = [];
        if (empty(trim($data['nombre'] ?? '')))   $errors['nombre']   = 'El nombre es obligatorio.';
        if (empty(trim($data['apellido'] ?? '')))  $errors['apellido'] = 'El apellido es obligatorio.';
        if (empty(trim($data['email'] ?? '')))     $errors['email']    = 'El correo es obligatorio.';
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Correo inválido.';
        elseif ($this->model->emailExists($data['email']) || $this->clienteModel->emailExists($data['email'])) {
            $errors['email'] = 'El correo ya está registrado.';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'La contraseña es obligatoria.';
        } else {
            $pwdErrors = Auth::validatePassword($data['password']);
            if ($pwdErrors) $errors['password'] = implode(' ', $pwdErrors);
        }
        if (($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
            $errors['password_confirm'] = 'Las contraseñas no coinciden.';
        }
        return $errors;
    }
}
