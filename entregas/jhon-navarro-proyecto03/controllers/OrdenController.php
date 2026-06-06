<?php
require_once __DIR__ . '/../models/Orden.php';
require_once __DIR__ . '/../models/DetalleOrden.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../models/Mesero.php';
require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../models/Plato.php';

class OrdenController {
    private Orden       $model;
    private DetalleOrden $detalleModel;
    private Mesa        $mesaModel;
    private Mesero      $meseroModel;
    private Reserva     $reservaModel;
    private Plato       $platoModel;

    public function __construct() {
        $this->model        = new Orden();
        $this->detalleModel = new DetalleOrden();
        $this->mesaModel    = new Mesa();
        $this->meseroModel  = new Mesero();
        $this->reservaModel = new Reserva();
        $this->platoModel   = new Plato();
    }

    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 10;
        $offset  = ($page - 1) * $limit;
        $ordenes = $this->model->getAll($limit, $offset, $search);
        $total   = $this->model->count($search);
        $pages   = ceil($total / $limit);
        require __DIR__ . '/../views/ordenes/index.php';
    }

    public function create(): void {
        $errors   = [];
        $data     = [];
        $mesas    = $this->mesaModel->getAllSimple();
        $meseros  = $this->meseroModel->getAll();
        $platos   = $this->platoModel->getAllDisponibles();
        $reservas = $this->reservaModel->getDelDia();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $_POST;
            $errors = $this->validateOrden($data);
            if (empty($errors)) {
                $idOrden = $this->model->create($data);
                // Guardar detalles
                $ids      = $data['plato_id']    ?? [];
                $cants    = $data['cantidad']     ?? [];
                $precios  = $data['precio_unit']  ?? [];
                $notasArr = $data['notas_item']   ?? [];
                foreach ($ids as $i => $platoId) {
                    if (!$platoId) continue;
                    $this->detalleModel->create([
                        'id_orden'       => $idOrden,
                        'id_plato'       => (int)$platoId,
                        'cantidad'       => (int)($cants[$i] ?? 1),
                        'precio_unitario'=> (float)($precios[$i] ?? 0),
                        'notas'          => $notasArr[$i] ?? '',
                    ]);
                }
                $this->model->updateTotal($idOrden);
                header('Location: index.php?module=ordenes&action=view&id=' . $idOrden . '&success=created');
                exit;
            }
        }
        require __DIR__ . '/../views/ordenes/form.php';
    }

    public function view(): void {
        $id     = (int)($_GET['id'] ?? 0);
        $orden  = $this->model->getById($id);
        if (!$orden) { header('Location: index.php?module=ordenes&action=index'); exit; }
        $detalles = $this->detalleModel->getByOrden($id);
        require __DIR__ . '/../views/ordenes/view.php';
    }

    public function updateEstado(): void {
        $id     = (int)($_POST['id_orden'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        $valid  = ['recibida','en_cocina','servida','pagada','cancelada'];
        if ($id && in_array($estado, $valid)) {
            $this->model->updateEstado($id, $estado);
        }
        header('Location: index.php?module=ordenes&action=view&id=' . $id . '&success=estado');
        exit;
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        header('Location: index.php?module=ordenes&action=index&success=deleted');
        exit;
    }

    /** AJAX: retorna precio de un plato */
    public function getPrecio(): void {
        header('Content-Type: application/json');
        $id    = (int)($_GET['id'] ?? 0);
        $plato = $this->platoModel->getById($id);
        echo json_encode(['precio' => $plato ? $plato['precio'] : 0]);
        exit;
    }

    private function validateOrden(array $data): array {
        $errors = [];
        if (empty($data['id_mesa'])) $errors['id_mesa'] = 'Seleccione una mesa.';
        $ids = array_filter($data['plato_id'] ?? []);
        if (empty($ids)) $errors['items'] = 'Agregue al menos un producto.';
        return $errors;
    }
}
