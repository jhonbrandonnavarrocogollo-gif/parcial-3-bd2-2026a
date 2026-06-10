<?php
$currentUser = Auth::user();
$esCliente   = Auth::isCliente();
$esMesero    = Auth::isMesero();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Reservas de Restaurante">
    <title><?= $pageTitle ?? 'RestaurantePro' ?> | RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-cup-hot-fill"></i></div>
        <div class="brand-text">
            <span class="brand-name">RestaurantePro</span>
            <span class="brand-sub"><?= $esCliente ? 'Portal Cliente' : 'Panel Mesero' ?></span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <a href="index.php" class="nav-item <?= ($module ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
        </a>

        <?php if ($esCliente): ?>
        <div class="nav-section-label">Mi cuenta</div>
        <a href="index.php?module=perfil" class="nav-item <?= ($module ?? '') === 'perfil' ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i><span>Mis Datos</span>
        </a>
        <a href="index.php?module=reservas" class="nav-item <?= ($module ?? '') === 'reservas' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i><span>Mis Reservas</span>
        </a>
        <a href="index.php?module=disponibilidad" class="nav-item <?= ($module ?? '') === 'disponibilidad' ? 'active' : '' ?>">
            <i class="bi bi-table"></i><span>Disponibilidad</span>
        </a>
        <div class="nav-section-label">Restaurante</div>
        <a href="index.php?module=menu" class="nav-item <?= ($module ?? '') === 'menu' ? 'active' : '' ?>">
            <i class="bi bi-journal-richtext"></i><span>Menú</span>
        </a>
        <a href="index.php?module=ordenes" class="nav-item <?= ($module ?? '') === 'ordenes' ? 'active' : '' ?>">
            <i class="bi bi-receipt-cutoff"></i><span>Mis Órdenes</span>
        </a>

        <?php else: ?>
        <div class="nav-section-label">Gestión</div>
        <a href="index.php?module=clientes" class="nav-item <?= ($module ?? '') === 'clientes' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i><span>Clientes</span>
        </a>
        <a href="index.php?module=mesas" class="nav-item <?= ($module ?? '') === 'mesas' ? 'active' : '' ?>">
            <i class="bi bi-table"></i><span>Mesas</span>
        </a>
        <a href="index.php?module=reservas" class="nav-item <?= ($module ?? '') === 'reservas' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i><span>Reservas</span>
        </a>
        <div class="nav-section-label">Menú & Órdenes</div>
        <a href="index.php?module=categorias" class="nav-item <?= ($module ?? '') === 'categorias' ? 'active' : '' ?>">
            <i class="bi bi-tags-fill"></i><span>Categorías</span>
        </a>
        <a href="index.php?module=platos" class="nav-item <?= ($module ?? '') === 'platos' ? 'active' : '' ?>">
            <i class="bi bi-egg-fried"></i><span>Platos & Bebidas</span>
        </a>
        <a href="index.php?module=ordenes" class="nav-item <?= ($module ?? '') === 'ordenes' ? 'active' : '' ?>">
            <i class="bi bi-receipt-cutoff"></i><span>Órdenes</span>
        </a>
        <div class="nav-section-label">Personal</div>
        <a href="index.php?module=meseros" class="nav-item <?= ($module ?? '') === 'meseros' ? 'active' : '' ?>">
            <i class="bi bi-person-badge-fill"></i><span>Meseros</span>
        </a>
        <div class="nav-section-label">Análisis</div>
        <a href="index.php?module=reportes" class="nav-item <?= ($module ?? '') === 'reportes' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-fill"></i><span>Reportes</span>
        </a>
        <?php endif; ?>

        <div class="nav-section-label">Sesión</div>
        <a href="index.php?module=auth&action=logout" class="nav-item">
            <i class="bi bi-box-arrow-left"></i><span>Cerrar Sesión</span>
        </a>
    </nav>
</aside>

<div class="main-wrapper" id="mainWrapper">
    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle" title="Menú">
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <?php if (!empty($breadcrumb)): foreach ($breadcrumb as $bc): ?>
                    <li class="breadcrumb-item <?= $bc['active'] ? 'active' : '' ?>">
                        <?= $bc['active'] ? htmlspecialchars($bc['label']) : '<a href="'.htmlspecialchars($bc['url']).'">'.htmlspecialchars($bc['label']).'</a>' ?>
                    </li>
                    <?php endforeach; endif; ?>
                </ol>
            </nav>
        </div>
        <div class="topbar-right">
            <span class="topbar-date">
                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i') ?>
            </span>
            <div class="topbar-avatar">
                <i class="bi bi-person-circle"></i>
                <span><?= htmlspecialchars(($currentUser['nombre'] ?? '') . ' ' . ($currentUser['apellido'] ?? '')) ?></span>
                <small class="badge bg-light text-dark ms-1"><?= $esCliente ? 'Cliente' : 'Mesero' ?></small>
            </div>
        </div>
    </header>

    <main class="content-area">
        <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?php
            $msgs = [
                'created'    => 'Registro creado exitosamente.',
                'updated'    => 'Registro actualizado exitosamente.',
                'deleted'    => 'Registro eliminado exitosamente.',
                'cancelled'  => 'Reserva cancelada exitosamente.',
                'estado'     => 'Estado actualizado exitosamente.',
                'registered' => '¡Bienvenido! Tu cuenta fue creada exitosamente.',
            ];
            echo $msgs[$_GET['success']] ?? 'Operación exitosa.';
            ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php
            $errs = ['fk' => 'No se puede eliminar: el registro tiene datos relacionados.'];
            echo $errs[$_GET['error']] ?? 'Ocurrió un error.';
            ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
