<?php
$pageTitle  = 'Disponibilidad de Mesas';
$module     = 'disponibilidad';
$breadcrumb = [['label' => 'Disponibilidad', 'active' => true]];
$estadoBadge = [
    'disponible'    => 'badge-success',
    'ocupada'       => 'badge-danger',
    'reservada'     => 'badge-warning',
    'mantenimiento' => 'badge-secondary',
    'pendiente'     => 'badge-warning',
    'confirmada'    => 'badge-success',
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-table me-2"></i>Disponibilidad de Mesas</h1>
        <p class="page-subtitle">Consulta el estado de las mesas por fecha</p>
    </div>
    <a href="index.php?module=reservas&action=create" class="btn btn-primary-custom">
        <i class="bi bi-calendar-plus me-2"></i>Reservar Mesa
    </a>
</div>

<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <input type="hidden" name="module" value="disponibilidad">
            <div>
                <label class="form-label-custom">Fecha</label>
                <input type="date" name="fecha" class="form-control-custom" value="<?= htmlspecialchars($fecha) ?>">
            </div>
            <button type="submit" class="btn btn-primary-custom">Consultar</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($mesas as $m): ?>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="card-modern mesa-dispon-card h-100">
            <div class="card-modern-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="mb-0">Mesa <?= $m['numero_mesa'] ?></h5>
                    <span class="status-badge <?= $estadoBadge[$m['estado']] ?? '' ?>"><?= ucfirst($m['estado']) ?></span>
                </div>
                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($m['ubicacion']) ?></p>
                <p class="small mb-3"><i class="bi bi-people me-1"></i>Capacidad: <?= $m['capacidad'] ?> personas</p>
                <?php if (!empty($ocupacion[$m['id_mesa']])): ?>
                <div class="reservas-del-dia">
                    <small class="text-muted d-block mb-1">Reservas del día:</small>
                    <?php foreach ($ocupacion[$m['id_mesa']] as $res): ?>
                    <div class="small py-1 border-top">
                        <?= date('H:i', strtotime($res['fecha_hora_inicio'])) ?> - <?= date('H:i', strtotime($res['fecha_hora_fin'])) ?>
                        <span class="status-badge <?= $estadoBadge[$res['estado']] ?? '' ?> ms-1"><?= ucfirst($res['estado']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <small class="text-success"><i class="bi bi-check-circle me-1"></i>Sin reservas este día</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
