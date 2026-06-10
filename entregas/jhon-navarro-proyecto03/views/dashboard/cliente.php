<?php
$pageTitle  = 'Mi Panel';
$module     = 'dashboard';
$breadcrumb = [['label' => 'Mi Panel', 'active' => true]];
$estadoBadge = [
    'pendiente'  => 'badge-warning',
    'confirmada' => 'badge-success',
    'cancelada'  => 'badge-danger',
    'completada' => 'badge-info',
    'recibida'   => 'badge-info',
    'en_cocina'  => 'badge-warning',
    'servida'    => 'badge-primary',
    'pagada'     => 'badge-success',
];
require __DIR__ . '/../layout/header.php';
$user = Auth::user();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-house-heart-fill me-2"></i>¡Hola, <?= htmlspecialchars($user['nombre']) ?>!</h1>
        <p class="page-subtitle">Bienvenido a tu portal de cliente</p>
    </div>
    <a href="index.php?module=reservas&action=create" class="btn btn-primary-custom">
        <i class="bi bi-calendar-plus me-2"></i>Nueva Reserva
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-teal">
            <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= $stats['total_reservas'] ?></div>
                <div class="stat-label">Mis Reservas</div>
            </div>
            <a href="index.php?module=reservas" class="stat-link">Ver todas <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-amber">
            <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= $stats['proxima_reserva'] ? date('d/m', strtotime($stats['proxima_reserva']['fecha_hora_inicio'])) : '—' ?></div>
                <div class="stat-label">Próxima Reserva</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="bi bi-receipt-cutoff"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= $stats['ordenes_activas'] ?></div>
                <div class="stat-label">Órdenes Activas</div>
            </div>
            <a href="index.php?module=ordenes" class="stat-link">Ver órdenes <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="bi bi-journal-richtext"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= count($menuDestacado) ?></div>
                <div class="stat-label">Platos Destacados</div>
            </div>
            <a href="index.php?module=menu" class="stat-link">Ver menú <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card-modern h-100">
            <div class="card-modern-header d-flex justify-content-between">
                <h6><i class="bi bi-calendar-event me-2"></i>Próxima Reserva</h6>
            </div>
            <div class="card-modern-body">
                <?php if ($stats['proxima_reserva']): $pr = $stats['proxima_reserva']; ?>
                <div class="proxima-reserva-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">Mesa <?= $pr['numero_mesa'] ?></h5>
                            <small class="text-muted"><?= htmlspecialchars($pr['ubicacion']) ?></small>
                        </div>
                        <span class="status-badge <?= $estadoBadge[$pr['estado']] ?? '' ?>"><?= ucfirst($pr['estado']) ?></span>
                    </div>
                    <p class="mb-1"><i class="bi bi-calendar3 me-2"></i><?= date('d/m/Y', strtotime($pr['fecha_hora_inicio'])) ?></p>
                    <p class="mb-1"><i class="bi bi-clock me-2"></i><?= date('H:i', strtotime($pr['fecha_hora_inicio'])) ?> - <?= date('H:i', strtotime($pr['fecha_hora_fin'])) ?></p>
                    <p class="mb-0"><i class="bi bi-people me-2"></i><?= $pr['num_personas'] ?> personas</p>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-4 mb-0">No tienes reservas próximas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-modern h-100">
            <div class="card-modern-header"><h6><i class="bi bi-receipt me-2"></i>Estado de Mis Órdenes</h6></div>
            <div class="card-modern-body p-0">
                <?php if (empty($misOrdenes)): ?>
                <p class="text-muted text-center py-4 mb-0">No tienes órdenes activas.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table-modern mb-0">
                        <thead><tr><th>#</th><th>Mesa</th><th>Estado</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($misOrdenes as $o): ?>
                        <tr>
                            <td><?= $o['id_orden'] ?></td>
                            <td>Mesa <?= $o['numero_mesa'] ?></td>
                            <td><span class="status-badge <?= $estadoBadge[$o['estado']] ?? '' ?>"><?= ucfirst(str_replace('_',' ',$o['estado'])) ?></span></td>
                            <td><a href="index.php?module=ordenes&action=view&id=<?= $o['id_orden'] ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header d-flex justify-content-between">
                <h6><i class="bi bi-clock-history me-2"></i>Historial de Reservas</h6>
                <a href="index.php?module=reservas" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-modern-body p-0">
                <div class="table-responsive">
                    <table class="table-modern mb-0">
                        <thead><tr><th>Fecha</th><th>Mesa</th><th>Personas</th><th>Estado</th></tr></thead>
                        <tbody>
                        <?php if (empty($historial)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Sin historial.</td></tr>
                        <?php else: foreach ($historial as $r): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($r['fecha_hora_inicio'])) ?></td>
                            <td>Mesa <?= $r['numero_mesa'] ?></td>
                            <td><?= $r['num_personas'] ?></td>
                            <td><span class="status-badge <?= $estadoBadge[$r['estado']] ?? '' ?>"><?= ucfirst($r['estado']) ?></span></td>
                        </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-modern">
            <div class="card-modern-header d-flex justify-content-between">
                <h6><i class="bi bi-egg-fried me-2"></i>Menú Disponible</h6>
                <a href="index.php?module=menu" class="btn btn-sm btn-outline-primary">Ver completo</a>
            </div>
            <div class="card-modern-body">
                <?php foreach ($menuDestacado as $plato): ?>
                <div class="menu-item-row d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <div class="fw-600"><?= htmlspecialchars($plato['nombre']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($plato['cat_nombre']) ?></small>
                    </div>
                    <span class="text-success fw-600">$<?= number_format($plato['precio'], 0) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
