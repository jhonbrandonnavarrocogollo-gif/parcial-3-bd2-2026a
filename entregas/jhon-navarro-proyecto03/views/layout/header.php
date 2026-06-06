<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Reservas de Restaurante — Panel Administrativo">
    <title><?= $pageTitle ?? 'RestaurantePro' ?> | RestaurantePro</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-cup-hot-fill"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">RestaurantePro</span>
            <span class="brand-sub">Sistema de Reservas</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <a href="index.php" class="nav-item <?= ($module ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
        </a>
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
    </nav>
</aside>

<!-- MAIN WRAPPER -->
<div class="main-wrapper" id="mainWrapper">
    <!-- TOPBAR -->
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
                <i class="bi bi-calendar3 me-1"></i>
                <?= date('d/m/Y H:i') ?>
            </span>
            <div class="topbar-avatar">
                <i class="bi bi-person-circle"></i>
                <span>Admin</span>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="content-area">
        <!-- Alertas globales -->
        <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?php
            $msgs = [
                'created'   => 'Registro creado exitosamente.',
                'updated'   => 'Registro actualizado exitosamente.',
                'deleted'   => 'Registro eliminado exitosamente.',
                'cancelled' => 'Reserva cancelada exitosamente.',
                'estado'    => 'Estado actualizado exitosamente.',
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
