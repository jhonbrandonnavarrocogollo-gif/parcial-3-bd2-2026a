<?php
$pageTitle  = 'Mesas';
$breadcrumb = [['label'=>'Mesas','active'=>true,'url'=>'']];
$estadoBadge = [
    'disponible'    => 'badge-success',
    'ocupada'       => 'badge-danger',
    'reservada'     => 'badge-warning',
    'mantenimiento' => 'badge-secondary',
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-table me-2"></i>Gestión de Mesas</h1>
        <p class="page-subtitle">Administra la distribución de mesas del restaurante</p>
    </div>
    <a href="index.php?module=mesas&action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle-fill me-2"></i>Nueva Mesa
    </a>
</div>

<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <input type="hidden" name="module" value="mesas">
            <div class="flex-grow-1">
                <label class="form-label-custom">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Número, ubicación o estado..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary-custom">Buscar</button>
            <?php if ($search): ?>
            <a href="index.php?module=mesas" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-list-ul me-2"></i>Listado de Mesas (<?= $total ?>)</h6>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Número</th>
                        <th>Capacidad</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($mesas)): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>No hay mesas registradas.
                    </td></tr>
                <?php else: foreach ($mesas as $m): ?>
                    <tr>
                        <td><span class="badge-id"><?= $m['id_mesa'] ?></span></td>
                        <td><strong>Mesa <?= $m['numero_mesa'] ?></strong></td>
                        <td><i class="bi bi-people me-1 text-muted"></i><?= $m['capacidad'] ?> personas</td>
                        <td><?= htmlspecialchars($m['ubicacion']) ?></td>
                        <td>
                            <span class="status-badge <?= $estadoBadge[$m['estado']] ?? 'badge-secondary' ?>">
                                <?= ucfirst($m['estado']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="index.php?module=mesas&action=edit&id=<?= $m['id_mesa'] ?>"
                                   class="btn-action btn-edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="btn-action btn-delete"
                                        onclick="confirmDelete('index.php?module=mesas&action=delete&id=<?= $m['id_mesa'] ?>')"
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
        <nav>
            <ul class="pagination-custom">
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                <li>
                    <a href="index.php?module=mesas&page=<?= $p ?>&search=<?= urlencode($search) ?>"
                       class="<?= $page === $p ? 'active' : '' ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
