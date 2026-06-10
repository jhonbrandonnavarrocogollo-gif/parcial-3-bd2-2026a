<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../models/Reserva.php';

class DisponibilidadController {
    private Mesa $mesaModel;
    private Reserva $reservaModel;

    public function __construct() {
        Auth::requireRole('cliente');
        $this->mesaModel    = new Mesa();
        $this->reservaModel = new Reserva();
    }

    public function index(): void {
        $fecha = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'] ?? '')
            ? $_GET['fecha']
            : date('Y-m-d');
        $mesas = $this->mesaModel->getAllSimple();
        $reservasDelDia = $this->reservaModel->getByFecha($fecha);
        $ocupacion = [];
        foreach ($reservasDelDia as $r) {
            $ocupacion[$r['id_mesa']][] = $r;
        }
        require __DIR__ . '/../views/disponibilidad/index.php';
    }
}
