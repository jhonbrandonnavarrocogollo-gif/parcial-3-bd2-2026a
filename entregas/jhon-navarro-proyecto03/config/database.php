<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'reservas_restaurante');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                $msg = 'No se pudo conectar a MySQL. Abre el panel de XAMPP e inicia el servicio MySQL.';
                if (php_sapi_name() === 'cli') {
                    die($msg . ' ' . $e->getMessage() . PHP_EOL);
                }
                http_response_code(503);
                die('<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Base de datos</title></head>'
                    . '<body style="font-family:sans-serif;max-width:520px;margin:80px auto;padding:0 20px;">'
                    . '<h1>Base de datos no disponible</h1>'
                    . '<p>' . htmlspecialchars($msg) . '</p>'
                    . '<p><a href="http://localhost/phpmyadmin">phpMyAdmin</a></p></body></html>');
            }
        }
        return self::$instance;
    }
}
