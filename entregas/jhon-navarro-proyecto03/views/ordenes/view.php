<?php
$pageTitle  = 'Detalle Orden #' . $orden['id_orden'];
$breadcrumb = [
    ['label'=>'Órdenes','active'=>false,'url'=>'index.php?module=ordenes'],
    ['label'=>'Orden #'.$orden['id_orden'],'active'=>true,'url'=>''],
];
$estadoBadge = [
    'recibida'  => 'badge-info',
    'en_cocina' => 'badge-warning',
    'servida'   => 'badge-primary',
    'pagada'    => 'badge-success',
    'cancelada' => 'badge-danger',
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-receipt-cutoff me-2"></i>Orden #<?= $orden['id_orden'] ?></h1>
        <span class="status-badge <?= $estadoBadge[$orden['estado']] ?> ms-2">
            <?= ucfirst(str_replace('_',' ',$orden['estado'])) ?>
        </span>
    </div>
    <a href="index.php?module=ordenes" class="btn btn-outline-secondary-custom"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<div class="row g-4">
    <!-- Info orden -->
    <div class="col-lg-4">
        <div class="card-modern mb-4">
            <div class="card-modern-header"><h6><i class="bi bi-info-circle me-2"></i>Información</h6></div>
            <div class="card-modern-body">
                <table class="w-100 info-table">
                    <tr><td class="text-muted">Mesa:</td><td class="fw-600">Mesa <?= $orden['numero_mesa'] ?></td></tr>
                    <tr><td class="text-muted">Ubicación:</td><td><?= htmlspecialchars($orden['ubicacion']) ?></td></tr>
                    <tr><td class="text-muted">Mesero:</td><td><?= $orden['mesero_nombre'] ? htmlspecialchars($orden['mesero_nombre'].' '.$orden['mesero_apellido']) : '—' ?></td></tr>
                    <tr><td class="text-muted">Fecha:</td><td><?= date('d/m/Y H:i', strtotime($orden['fecha_hora'])) ?></td></tr>
                    <tr><td class="text-muted">Reserva:</td><td><?= $orden['id_reserva'] ? '#'.$orden['id_reserva'] : '—' ?></td></tr>
                    <tr class="border-top"><td class="fw-700 pt-2">TOTAL:</td><td class="fw-700 text-success pt-2 fs-5">$<?= number_format($orden['total'],2) ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Cambiar estado -->
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-arrow-repeat me-2"></i>Cambiar Estado</h6></div>
            <div class="card-modern-body">
                <form method="POST" action="index.php?module=ordenes&action=updateEstado">
                    <input type="hidden" name="id_orden" value="<?= $orden['id_orden'] ?>">
                    <select name="estado" class="form-select-custom mb-3">
                        <?php foreach (['recibida','en_cocina','servida','pagada','cancelada'] as $est): ?>
                        <option value="<?= $est ?>" <?= $orden['estado'] === $est ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('_',' ',$est)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="bi bi-save-fill me-2"></i>Actualizar Estado
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Detalles -->
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-basket me-2"></i>Detalle de Productos</h6></div>
            <div class="card-modern-body p-0">
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Plato</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($detalles)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Sin productos.</td></tr>
                        <?php else:
                            $gran_total = 0;
                            foreach ($detalles as $d):
                            $gran_total += $d['subtotal'];
                        ?>
                            <tr>
                                <td><span class="badge-id"><?= $d['id_detalle'] ?></span></td>
                                <td class="fw-600"><?= htmlspecialchars($d['plato_nombre']) ?></td>
                                <td class="text-center"><?= $d['cantidad'] ?></td>
                                <td class="text-end">$<?= number_format($d['precio_unitario'],2) ?></td>
                                <td class="text-end fw-600 text-success">$<?= number_format($d['subtotal'],2) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($d['notas'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                        <?php if (!empty($detalles)): ?>
                        <tfoot>
                            <tr class="table-active fw-700">
                                <td colspan="4" class="text-end">TOTAL:</td>
                                <td class="text-end text-success">$<?= number_format($orden['total'],2) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
