<?php
// ─── Front Controller ────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$module = preg_replace('/[^a-z_]/', '', strtolower($_GET['module'] ?? 'dashboard'));
$action = preg_replace('/[^a-z_]/', '', strtolower($_GET['action'] ?? 'index'));

$publicModules = ['auth'];

$meseroModules = [
    'dashboard', 'clientes', 'mesas', 'reservas', 'categorias',
    'platos', 'ordenes', 'meseros', 'reportes',
];

$clienteModules = [
    'dashboard', 'perfil', 'reservas', 'menu', 'ordenes', 'disponibilidad',
];

$map = [
    'auth'           => ['file' => null, 'ctrl' => 'AuthController', 'public' => true],
    'dashboard'      => ['file' => null, 'ctrl' => null],
    'perfil'         => ['file' => null, 'ctrl' => 'PerfilController'],
    'menu'           => ['file' => null, 'ctrl' => 'MenuController'],
    'disponibilidad' => ['file' => null, 'ctrl' => 'DisponibilidadController'],
    'clientes'       => ['file' => null, 'ctrl' => 'ClienteController'],
    'mesas'          => ['file' => null, 'ctrl' => 'MesaController'],
    'reservas'       => ['file' => null, 'ctrl' => 'ReservaController'],
    'categorias'     => ['file' => null, 'ctrl' => 'CategoriaController'],
    'platos'         => ['file' => null, 'ctrl' => 'PlatoController'],
    'ordenes'        => ['file' => null, 'ctrl' => 'OrdenController'],
    'meseros'        => ['file' => null, 'ctrl' => 'MeseroController'],
    'reportes'       => ['file' => null, 'ctrl' => 'ReporteController'],
];

if (!isset($map[$module])) {
    $module = 'dashboard';
}

$entry = $map[$module];
$isPublic = !empty($entry['public']);

if (!$isPublic) {
    Auth::requireLogin();
    if (Auth::isCliente() && !in_array($module, $clienteModules, true)) {
        Auth::redirectToDashboard();
    }
    if (Auth::isMesero() && !in_array($module, $meseroModules, true)) {
        Auth::redirectToDashboard();
    }
}

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
    // Dashboard según rol
    if (Auth::isCliente()) {
        require_once __DIR__ . '/models/Reserva.php';
        require_once __DIR__ . '/models/Orden.php';
        require_once __DIR__ . '/models/Plato.php';

        $idCliente    = Auth::idCliente();
        $reservaModel = new Reserva();
        $ordenModel   = new Orden();
        $platoModel   = new Plato();

        $stats = [
            'total_reservas'  => $reservaModel->countByCliente($idCliente),
            'proxima_reserva' => $reservaModel->getProximaByCliente($idCliente),
            'ordenes_activas' => count($ordenModel->getActivasByCliente($idCliente)),
            'total_ordenes'   => $ordenModel->countByCliente($idCliente),
        ];
        $misReservas  = $reservaModel->getByCliente($idCliente, 5);
        $misOrdenes   = $ordenModel->getActivasByCliente($idCliente);
        $historial    = $reservaModel->getHistorialByCliente($idCliente, 5);
        $menuDestacado = array_slice($platoModel->getAllDisponibles(), 0, 4);

        require __DIR__ . '/views/dashboard/cliente.php';
    } else {
        require_once __DIR__ . '/models/Cliente.php';
        require_once __DIR__ . '/models/Mesa.php';
        require_once __DIR__ . '/models/Reserva.php';
        require_once __DIR__ . '/models/Orden.php';

        $clienteModel = new Cliente();
        $mesaModel    = new Mesa();
        $reservaModel = new Reserva();
        $ordenModel   = new Orden();

        $stats = [
            'total_clientes'    => $clienteModel->count(),
            'total_mesas'       => $mesaModel->count(),
            'total_reservas'    => $reservaModel->count(),
            'reservas_hoy'      => $reservaModel->countHoy(),
            'mesas_disponibles' => $mesaModel->countDisponibles(),
            'mesas_ocupadas'    => $reservaModel->countMesasOcupadas(),
            'ordenes_activas'   => $ordenModel->countActivas(),
            'ventas_hoy'        => $ordenModel->getVentasHoy(),
        ];
        $reservasPorDia = $reservaModel->getReservasPorDia(7);
        $ventasPorDia   = $ordenModel->getVentasPorDia(7);
        $mesasTop       = $mesaModel->getMesasMasReservadas(5);

        require __DIR__ . '/views/dashboard/index.php';
    }
}
