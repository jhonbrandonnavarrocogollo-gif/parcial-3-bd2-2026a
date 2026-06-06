<?php
$pageTitle  = 'Clientes';
$breadcrumb = [['label'=>'Clientes','active'=>true,'url'=>'']];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-people-fill me-2"></i>Gestión de Clientes</h1>
        <p class="page-subtitle">Administra el registro de clientes</p>
    </div>
    <a href="index.php?module=clientes&action=create" class="btn btn-primary-custom">
        <i class="bi bi-person-plus-fill me-2"></i>Nuevo Cliente
    </a>
</div>

<!-- BUSCADOR -->
<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <input type="hidden" name="module" value="clientes">
            <div class="flex-grow-1">
                <label class="form-label-custom">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Nombre, apellido o correo..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary-custom">Buscar</button>
            <?php if ($search): ?>
            <a href="index.php?module=clientes" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- TABLA -->
<div class="card-modern">
    <div class="card-modern-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Listado (<?= $total ?> registros)</h6>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($clientes)): ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>No se encontraron registros.
                    </td></tr>
                <?php else: foreach ($clientes as $c): ?>
                    <tr>
                        <td><span class="badge-id"><?= $c['id_cliente'] ?></span></td>
                        <td class="fw-600"><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= htmlspecialchars($c['apellido']) ?></td>
                        <td><?= htmlspecialchars($c['telefono'] ?? '—') ?></td>
                        <td>
                            <?php if ($c['email']): ?>
                            <a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="text-link">
                                <?= htmlspecialchars($c['email']) ?>
                            </a>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="index.php?module=clientes&action=edit&id=<?= $c['id_cliente'] ?>"
                                   class="btn-action btn-edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="btn-action btn-delete"
                                        onclick="confirmDelete('index.php?module=clientes&action=delete&id=<?= $c['id_cliente'] ?>')"
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
    <!-- PAGINACIÓN -->
    <?php if ($pages > 1): ?>
    <div class="card-modern-footer">
        <nav>
            <ul class="pagination-custom">
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                <li>
                    <a href="index.php?module=clientes&page=<?= $p ?>&search=<?= urlencode($search) ?>"
                       class="<?= $page === $p ? 'active' : '' ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
