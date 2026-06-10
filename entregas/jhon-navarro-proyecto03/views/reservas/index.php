<?php
$esCliente = $esCliente ?? Auth::isCliente();
$pageTitle  = $esCliente ? 'Mis Reservas' : 'Reservas';
$breadcrumb = [['label'=>$pageTitle,'active'=>true,'url'=>'']];
$estadoBadge = [
    'pendiente'  => 'badge-warning',
    'confirmada' => 'badge-success',
    'cancelada'  => 'badge-danger',
    'completada' => 'badge-info',
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar-check-fill me-2"></i><?= $esCliente ? 'Mis Reservas' : 'Gestión de Reservas' ?></h1>
        <p class="page-subtitle"><?= $esCliente ? 'Consulta y gestiona tus reservas' : 'Administra las reservas del restaurante' ?></p>
    </div>
    <a href="index.php?module=reservas&action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle-fill me-2"></i>Nueva Reserva
    </a>
</div>

<?php if (!$esCliente): ?>
<!-- CALENDARIO Y DISPONIBILIDAD -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Calendario de Reservas — Próximos 7 días</h6>
                <span class="badge bg-primary-subtle text-primary">Hoy: <?= date('d/m/Y') ?></span>
            </div>
            <div class="card-modern-body">
                <div class="calendar-grid mb-3">
                <?php
                $diasSem = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
                for ($d = 0; $d < 7; $d++):
                    $dia     = date('Y-m-d', strtotime("+$d days"));
                    $diaNum  = date('d', strtotime($dia));
                    $diaName = $diasSem[(int)date('w', strtotime($dia))];
                    $count   = $conteoPorDia[$dia] ?? 0;
                    $isToday = $dia === date('Y-m-d');
                    $isSel   = $dia === $fechaCal;
                ?>
                    <a href="index.php?module=reservas&fecha=<?= $dia ?>&search=<?= urlencode($search) ?>"
                       class="calendar-day <?= $isToday ? 'today' : '' ?> <?= $isSel ? 'selected' : '' ?>">
                        <div class="day-name"><?= $diaName ?></div>
                        <div class="day-num"><?= $diaNum ?></div>
                        <?php if ($count > 0): ?>
                        <span class="day-count"><?= $count ?> res.</span>
                        <?php endif; ?>
                    </a>
                <?php endfor; ?>
                </div>

                <h6 class="text-muted mb-3">
                    <i class="bi bi-clock me-1"></i>Reservas del <?= date('d/m/Y', strtotime($fechaCal)) ?>
                    (<?= count($reservasCal) ?>)
                </h6>
                <?php if (empty($reservasCal)): ?>
                    <p class="text-muted text-center py-3 mb-0">
                        <i class="bi bi-calendar-x me-1"></i>No hay reservas para esta fecha.
                    </p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Mesa</th>
                                <th>Personas</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reservasCal as $rc): ?>
                            <tr>
                                <td><?= date('H:i', strtotime($rc['fecha_hora_inicio'])) ?> - <?= date('H:i', strtotime($rc['fecha_hora_fin'])) ?></td>
                                <td class="fw-600"><?= htmlspecialchars($rc['cli_nombre'] . ' ' . $rc['cli_apellido']) ?></td>
                                <td>Mesa <?= $rc['numero_mesa'] ?></td>
                                <td><?= $rc['num_personas'] ?></td>
                                <td><span class="status-badge <?= $estadoBadge[$rc['estado']] ?? 'badge-secondary' ?>"><?= ucfirst($rc['estado']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-table me-2"></i>Disponibilidad de Mesas</h6>
            </div>
            <div class="card-modern-body">
                <div class="mesa-grid">
                <?php foreach ($mesas as $m): ?>
                    <div class="mesa-tile <?= htmlspecialchars($m['estado']) ?>">
                        <div class="mesa-tile-num">Mesa <?= $m['numero_mesa'] ?></div>
                        <div class="mesa-tile-cap"><i class="bi bi-people me-1"></i><?= $m['capacidad'] ?> pers.</div>
                        <span class="status-badge <?= $estadoBadge[$m['estado']] ?? 'badge-secondary' ?> mt-2">
                            <?= ucfirst($m['estado']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($mesas)): ?>
                    <p class="text-muted text-center col-12">No hay mesas registradas.</p>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <?php if (!$esCliente): ?><input type="hidden" name="fecha" value="<?= htmlspecialchars($fechaCal ?? '') ?>"><?php endif; ?>
            <input type="hidden" name="module" value="reservas">
            <div class="flex-grow-1">
                <label class="form-label-custom">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="<?= $esCliente ? 'Mesa o estado...' : 'Cliente, mesa o estado...' ?>"
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary-custom">Buscar</button>
            <?php if ($search): ?>
            <a href="index.php?module=reservas" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-calendar3 me-2"></i>Listado de Reservas (<?= $total ?>)</h6>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php if (!$esCliente): ?><th>Cliente</th><?php endif; ?>
                        <th>Mesa</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Personas</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($reservas)): ?>
                    <tr><td colspan="<?= $esCliente ? 7 : 8 ?>" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-6 d-block mb-2"></i>No hay reservas registradas.
                    </td></tr>
                <?php else: foreach ($reservas as $r): ?>
                    <tr>
                        <td><span class="badge-id"><?= $r['id_reserva'] ?></span></td>
                        <?php if (!$esCliente): ?>
                        <td>
                            <div class="fw-600"><?= htmlspecialchars(($r['cli_nombre'] ?? '') . ' ' . ($r['cli_apellido'] ?? '')) ?></div>
                        </td>
                        <?php endif; ?>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                Mesa <?= $r['numero_mesa'] ?>
                            </span>
                            <small class="text-muted d-block"><?= htmlspecialchars($r['ubicacion']) ?></small>
                        </td>
                        <td>
                            <div><?= date('d/m/Y', strtotime($r['fecha_hora_inicio'])) ?></div>
                            <small class="text-muted"><?= date('H:i', strtotime($r['fecha_hora_inicio'])) ?></small>
                        </td>
                        <td>
                            <div><?= date('d/m/Y', strtotime($r['fecha_hora_fin'])) ?></div>
                            <small class="text-muted"><?= date('H:i', strtotime($r['fecha_hora_fin'])) ?></small>
                        </td>
                        <td><i class="bi bi-people me-1 text-muted"></i><?= $r['num_personas'] ?></td>
                        <td>
                            <span class="status-badge <?= $estadoBadge[$r['estado']] ?? 'badge-secondary' ?>">
                                <?= ucfirst($r['estado']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="index.php?module=reservas&action=edit&id=<?= $r['id_reserva'] ?>"
                                   class="btn-action btn-edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <?php if (!in_array($r['estado'], ['cancelada','completada'])): ?>
                                <button class="btn-action btn-warning"
                                        onclick="confirmAction('index.php?module=reservas&action=cancelar&id=<?= $r['id_reserva'] ?>','¿Cancelar esta reserva?')"
                                        title="Cancelar reserva">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (!$esCliente): ?>
                                <button class="btn-action btn-delete"
                                        onclick="confirmDelete('index.php?module=reservas&action=delete&id=<?= $r['id_reserva'] ?>')"
                                        title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pages > 1): ?>
    <div class="card-modern-footer">
        <nav>
            <ul class="pagination-custom">
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                <li>
                    <a href="index.php?module=reservas&page=<?= $p ?>&search=<?= urlencode($search) ?><?= !$esCliente ? '&fecha='.urlencode($fechaCal ?? '') : '' ?>"
                       class="<?= $page === $p ? 'active' : '' ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
