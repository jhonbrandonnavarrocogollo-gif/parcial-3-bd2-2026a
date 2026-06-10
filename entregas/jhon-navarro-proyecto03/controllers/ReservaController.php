<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Mesa.php';

class ReservaController {
    private Reserva $model;
    private Cliente $clienteModel;
    private Mesa    $mesaModel;

    public function __construct() {
        $this->model        = new Reserva();
        $this->clienteModel = new Cliente();
        $this->mesaModel    = new Mesa();
    }

    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 10;
        $offset  = ($page - 1) * $limit;
        $esCliente = Auth::isCliente();

        if ($esCliente) {
            $idCliente = Auth::idCliente();
            $reservas  = $this->model->getByCliente($idCliente, $limit, $offset, $search);
            $total     = $this->model->countByCliente($idCliente, $search);
        } else {
            $reservas = $this->model->getAll($limit, $offset, $search);
            $total    = $this->model->count($search);
        }
        $pages = ceil($total / $limit);

        $fechaCal       = null;
        $reservasCal    = [];
        $reservasSemana = [];
        $mesas          = [];
        $conteoPorDia   = [];

        if (!$esCliente) {
            $fechaCal       = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'] ?? '') ? $_GET['fecha'] : date('Y-m-d');
            $reservasCal    = $this->model->getByFecha($fechaCal);
            $reservasSemana = $this->model->getReservasSemana();
            $mesas          = $this->mesaModel->getAllSimple();
            foreach ($reservasSemana as $rs) {
                $conteoPorDia[$rs['dia']] = (int)$rs['total'];
            }
        }

        require __DIR__ . '/../views/reservas/index.php';
    }

    public function create(): void {
        $errors   = [];
        $data     = [];
        $esCliente = Auth::isCliente();
        $clientes = $esCliente ? [] : $this->clienteModel->getAllSimple();
        $mesas    = $this->mesaModel->getAllSimple();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            if ($esCliente) {
                $data['id_cliente'] = Auth::idCliente();
                $data['estado']     = 'pendiente';
            }
            $errors = $this->validate($data, $esCliente);
            if (empty($errors)) {
                $conflicto = $this->model->tieneConflicto(
                    (int)$data['id_mesa'],
                    $data['fecha_hora_inicio'],
                    $data['fecha_hora_fin']
                );
                if ($conflicto) {
                    $errors['conflicto'] = 'Ya existe una reserva para esa mesa en ese horario.';
                } else {
                    $this->model->create($data);
                    header('Location: index.php?module=reservas&action=index&success=created');
                    exit;
                }
            }
        } elseif ($esCliente) {
            $data['id_cliente'] = Auth::idCliente();
        }
        require __DIR__ . '/../views/reservas/form.php';
    }

    public function edit(): void {
        $id        = (int)($_GET['id'] ?? 0);
        $errors    = [];
        $esCliente = Auth::isCliente();
        $clientes  = $esCliente ? [] : $this->clienteModel->getAllSimple();
        $mesas     = $this->mesaModel->getAllSimple();
        $data      = $this->model->getById($id);

        if (!$data || ($esCliente && !$this->model->belongsToCliente($id, Auth::idCliente()))) {
            header('Location: index.php?module=reservas&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            if ($esCliente) {
                $data['id_cliente'] = Auth::idCliente();
                $data['estado']     = $_POST['estado'] ?? $data['estado'] ?? 'pendiente';
                if (!in_array($data['estado'], ['pendiente', 'cancelada'], true)) {
                    $data['estado'] = 'pendiente';
                }
            }
            $errors = $this->validate($data, $esCliente);
            if (empty($errors)) {
                $conflicto = $this->model->tieneConflicto(
                    (int)$data['id_mesa'],
                    $data['fecha_hora_inicio'],
                    $data['fecha_hora_fin'],
                    $id
                );
                if ($conflicto) {
                    $errors['conflicto'] = 'Ya existe una reserva para esa mesa en ese horario.';
                } else {
                    $this->model->update($id, $data);
                    header('Location: index.php?module=reservas&action=index&success=updated');
                    exit;
                }
            }
        }
        require __DIR__ . '/../views/reservas/form.php';
    }

    public function cancelar(): void {
        $id        = (int)($_GET['id'] ?? 0);
        $esCliente = Auth::isCliente();
        if ($esCliente && !$this->model->belongsToCliente($id, Auth::idCliente())) {
            header('Location: index.php?module=reservas&action=index');
            exit;
        }
        $this->model->cancelar($id);
        header('Location: index.php?module=reservas&action=index&success=cancelled');
        exit;
    }

    public function delete(): void {
        if (Auth::isCliente()) {
            Auth::redirectToDashboard();
        }
        $id = (int)($_GET['id'] ?? 0);
        try {
            $this->model->delete($id);
            header('Location: index.php?module=reservas&action=index&success=deleted');
        } catch (PDOException $e) {
            header('Location: index.php?module=reservas&action=index&error=fk');
        }
        exit;
    }

    public function checkConflicto(): void {
        header('Content-Type: application/json');
        $idMesa  = (int)($_POST['id_mesa'] ?? 0);
        $inicio  = $_POST['inicio'] ?? '';
        $fin     = $_POST['fin'] ?? '';
        $exclude = (int)($_POST['exclude'] ?? 0);
        if (!$idMesa || !$inicio || !$fin) {
            echo json_encode(['conflicto' => false]);
            exit;
        }
        $conflicto = $this->model->tieneConflicto($idMesa, $inicio, $fin, $exclude);
        echo json_encode(['conflicto' => $conflicto]);
        exit;
    }

    private function validate(array $data, bool $esCliente = false): array {
        $errors = [];
        if (empty($data['id_cliente']))          $errors['id_cliente']        = 'Seleccione un cliente.';
        if (empty($data['id_mesa']))              $errors['id_mesa']           = 'Seleccione una mesa.';
        if (empty($data['fecha_hora_inicio']))    $errors['fecha_hora_inicio'] = 'Fecha/hora inicio requerida.';
        if (empty($data['fecha_hora_fin']))       $errors['fecha_hora_fin']    = 'Fecha/hora fin requerida.';
        if (!empty($data['fecha_hora_inicio']) && !empty($data['fecha_hora_fin'])) {
            if (strtotime($data['fecha_hora_fin']) <= strtotime($data['fecha_hora_inicio'])) {
                $errors['fecha_hora_fin'] = 'La hora de fin debe ser posterior a la de inicio.';
            }
            if (strtotime($data['fecha_hora_inicio']) < time() && $esCliente) {
                $errors['fecha_hora_inicio'] = 'No puede reservar en fechas pasadas.';
            }
        }
        if (empty($data['num_personas']) || (int)$data['num_personas'] < 1 || (int)$data['num_personas'] > 50) {
            $errors['num_personas'] = 'Número de personas entre 1 y 50.';
        }
        return $errors;
    }
}
