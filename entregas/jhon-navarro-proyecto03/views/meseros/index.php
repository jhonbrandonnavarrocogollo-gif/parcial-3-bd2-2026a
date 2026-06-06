<?php
$pageTitle  = 'Meseros';
$module     = 'meseros';
$breadcrumb = [['label' => 'Meseros', 'active' => true]];
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-person-badge-fill me-2"></i>Gestión de Meseros</h1>
        <p class="page-subtitle">Administra el personal de servicio</p>
    </div>
    <a href="index.php?module=meseros&action=create" class="btn btn-primary-custom">
        <i class="bi bi-person-plus-fill me-2"></i>Nuevo Mesero
    </a>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <h6><i class="bi bi-list-ul me-2"></i>Listado de Meseros (<?= count($meseros) ?>)</h6>
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
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($meseros)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-person-badge display-6 d-block mb-2"></i>No hay meseros registrados.
                    </td></tr>
                <?php else: foreach ($meseros as $ms): ?>
                    <tr>
                        <td><span class="badge-id"><?= $ms['id_mesero'] ?></span></td>
                        <td class="fw-600"><?= htmlspecialchars($ms['nombre']) ?></td>
                        <td><?= htmlspecialchars($ms['apellido']) ?></td>
                        <td><?= htmlspecialchars($ms['telefono'] ?? '—') ?></td>
                        <td class="text-center">
                            <div class="action-btns">
                                <a href="index.php?module=meseros&action=edit&id=<?= $ms['id_mesero'] ?>"
                                   class="btn-action btn-edit" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="btn-action btn-delete"
                                        onclick="confirmDelete('index.php?module=meseros&action=delete&id=<?= $ms['id_mesero'] ?>')"
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
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
