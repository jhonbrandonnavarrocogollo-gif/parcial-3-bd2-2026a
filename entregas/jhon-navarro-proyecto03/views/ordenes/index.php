<?php
$pageTitle  = 'Órdenes';
$breadcrumb = [['label'=>'Órdenes','active'=>true,'url'=>'']];
$estadoBadge = [
    'recibida'   => 'badge-info',
    'en_cocina'  => 'badge-warning',
    'servida'    => 'badge-primary',
    'pagada'     => 'badge-success',
    'cancelada'  => 'badge-danger',
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-receipt-cutoff me-2"></i>Gestión de Órdenes</h1>
        <p class="page-subtitle">Administra las órdenes de consumo</p>
    </div>
    <a href="index.php?module=ordenes&action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle-fill me-2"></i>Nueva Orden
    </a>
</div>

<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <input type="hidden" name="module" value="ordenes">
            <div class="flex-grow-1">
                <label class="form-label-custom">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Mesa o estado..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary-custom">Buscar</button>
            <?php if ($search): ?><a href="index.php?module=ordenes" class="btn btn-outline-secondary">Limpiar</a><?php endif; ?>
        </form>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-list-ul me-2"></i>Listado de Órdenes (<?= $total ?>)</h6>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mesa</th>
                        <th>Mesero</th>
                        <th>Fecha/Hora</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($ordenes)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-receipt display-6 d-block mb-2"></i>No hay órdenes registradas.
                    </td></tr>
                <?php else: foreach ($ordenes as $o): ?>
                    <tr>
                        <td><span class="badge-id"><?= $o['id_orden'] ?></span></td>
                        <td><strong>Mesa <?= $o['numero_mesa'] ?></strong></td>
                        <td><?= $o['mesero_nombre'] ? htmlspecialchars($o['mesero_nombre'].' '.$o['mesero_apellido']) : '<span class="text-muted">—</span>' ?></td>
                        <td>
                            <div><?= date('d/m/Y', strtotime($o['fecha_hora'])) ?></div>
                            <small class="text-muted"><?= date('H:i', strtotime($o['fecha_hora'])) ?></small>
                        </td>
                        <td class="fw-700 text-success">$<?= number_format($o['total'], 2) ?></td>
                        <td>
                            <span class="status-badge <?= $estadoBadge[$o['estado']] ?? 'badge-secondary' ?>">
                                <?= ucfirst(str_replace('_',' ',$o['estado'])) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="index.php?module=ordenes&action=view&id=<?= $o['id_orden'] ?>"
                                   class="btn-action btn-view" title="Ver detalle">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <button class="btn-action btn-delete"
                                        onclick="confirmDelete('index.php?module=ordenes&action=delete&id=<?= $o['id_orden'] ?>')"
                                        title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
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
        <nav><ul class="pagination-custom">
            <?php for ($p = 1; $p <= $pages; $p++): ?>
            <li><a href="index.php?module=ordenes&page=<?= $p ?>&search=<?= urlencode($search) ?>"
                   class="<?= $page === $p ? 'active' : '' ?>"><?= $p ?></a></li>
            <?php endfor; ?>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
