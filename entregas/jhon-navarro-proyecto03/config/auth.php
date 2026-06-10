<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    private const ADMIN_REGISTER_PASSWORD = 'administrador123';

    public static function verifyAdminRegisterPassword(string $password): bool {
        return $password === self::ADMIN_REGISTER_PASSWORD;
    }

    public static function login(array $usuario): void {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id_usuario' => (int)$usuario['id_usuario'],
            'email'      => $usuario['email'],
            'rol'        => $usuario['rol'],
            'id_cliente' => $usuario['id_cliente'] ? (int)$usuario['id_cliente'] : null,
            'id_mesero'  => $usuario['id_mesero'] ? (int)$usuario['id_mesero'] : null,
            'nombre'     => $usuario['nombre'] ?? '',
            'apellido'   => $usuario['apellido'] ?? '',
        ];
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool {
        return !empty($_SESSION['usuario']['id_usuario']);
    }

    public static function user(): ?array {
        return $_SESSION['usuario'] ?? null;
    }

    public static function rol(): ?string {
        return $_SESSION['usuario']['rol'] ?? null;
    }

    public static function isCliente(): bool {
        return self::rol() === 'cliente';
    }

    public static function isMesero(): bool {
        return self::rol() === 'mesero';
    }

    public static function idCliente(): ?int {
        return self::user()['id_cliente'] ?? null;
    }

    public static function idMesero(): ?int {
        return self::user()['id_mesero'] ?? null;
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location: index.php?module=auth&action=login');
            exit;
        }
    }

    public static function requireRole(string|array $roles): void {
        self::requireLogin();
        $roles = (array)$roles;
        if (!in_array(self::rol(), $roles, true)) {
            self::redirectToDashboard();
        }
    }

    public static function redirectToDashboard(): void {
        if (self::isCliente()) {
            header('Location: index.php?module=dashboard');
        } else {
            header('Location: index.php?module=dashboard');
        }
        exit;
    }

    public static function guestOnly(): void {
        if (self::check()) {
            self::redirectToDashboard();
        }
    }

    public static function validatePassword(string $password): array {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Mínimo 8 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Debe incluir al menos una mayúscula.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Debe incluir al menos una minúscula.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Debe incluir al menos un número.';
        }
        return $errors;
    }
}
