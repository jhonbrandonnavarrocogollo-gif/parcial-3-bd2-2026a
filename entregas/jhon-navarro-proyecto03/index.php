<?php
// ─── Front Controller ────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';

$module = preg_replace('/[^a-z_]/', '', strtolower($_GET['module'] ?? 'dashboard'));
$action = preg_replace('/[^a-z_]/', '', strtolower($_GET['action'] ?? 'index'));

$map = [
    'dashboard'  => ['file' => 'views/dashboard/index.php',   'ctrl' => null],
    'clientes'   => ['file' => null, 'ctrl' => 'ClienteController'],
    'mesas'      => ['file' => null, 'ctrl' => 'MesaController'],
    'reservas'   => ['file' => null, 'ctrl' => 'ReservaController'],
    'categorias' => ['file' => null, 'ctrl' => 'CategoriaController'],
    'platos'     => ['file' => null, 'ctrl' => 'PlatoController'],
    'ordenes'    => ['file' => null, 'ctrl' => 'OrdenController'],
    'meseros'    => ['file' => null, 'ctrl' => 'MeseroController'],
    'reportes'   => ['file' => null, 'ctrl' => 'ReporteController'],
];

if (!isset($map[$module])) {
    $module = 'dashboard';
}

$entry = $map[$module];

if ($entry['ctrl']) {
    $ctrlFile = __DIR__ . '/controllers/' . $entry['ctrl'] . '.php';
    if (file_exists($ctrlFile)) {
        require_once $ctrlFile;
        $ctrl = new $entry['ctrl']();
        if (method_exists($ctrl, $action)) {
            $ctrl->$action();
        } else {
            $ctrl->index();
        }
    }
} else {
    // Dashboard sin controlador dedicado
    require_once __DIR__ . '/models/Cliente.php';
    require_once __DIR__ . '/models/Mesa.php';
    require_once __DIR__ . '/models/Reserva.php';
    require_once __DIR__ . '/models/Orden.php';

    $clienteModel = new Cliente();
    $mesaModel    = new Mesa();
    $reservaModel = new Reserva();
    $ordenModel   = new Orden();

    $stats = [
        'total_clientes'   => $clienteModel->count(),
        'total_mesas'      => $mesaModel->count(),
        'total_reservas'   => $reservaModel->count(),
        'reservas_hoy'     => $reservaModel->countHoy(),
        'mesas_disponibles'=> $mesaModel->countDisponibles(),
        'ordenes_activas'  => $ordenModel->countActivas(),
        'ventas_hoy'       => $ordenModel->getVentasHoy(),
    ];
    $reservasPorDia = $reservaModel->getReservasPorDia(7);
    $ventasPorDia   = $ordenModel->getVentasPorDia(7);
    $mesasTop       = $mesaModel->getMesasMasReservadas(5);

    require __DIR__ . '/views/dashboard/index.php';
}
