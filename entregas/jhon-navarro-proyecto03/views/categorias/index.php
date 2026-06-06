<?php
$pageTitle  = 'Categorías';
$breadcrumb = [['label'=>'Categorías','active'=>true,'url'=>'']];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-tags-fill me-2"></i>Gestión de Categorías</h1>
        <p class="page-subtitle">Organiza los platos por categorías del menú</p>
    </div>
    <a href="index.php?module=categorias&action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle-fill me-2"></i>Nueva Categoría
    </a>
</div>
<div class="row g-4">
    <?php if (empty($categorias)): ?>
    <div class="col-12">
        <div class="card-modern text-center py-5">
            <i class="bi bi-tags display-4 text-muted mb-3 d-block"></i>
            <p class="text-muted">No hay categorías registradas.</p>
        </div>
    </div>
    <?php else: foreach ($categorias as $cat): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card-modern category-card">
            <div class="card-modern-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="category-icon">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-700 mb-1"><?= htmlspecialchars($cat['nombre']) ?></h6>
                        <p class="text-muted small mb-0">
                            <?= htmlspecialchars($cat['descripcion'] ?? 'Sin descripción') ?>
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="index.php?module=categorias&action=edit&id=<?= $cat['id_categoria'] ?>"
                       class="btn btn-sm btn-primary-custom flex-grow-1">
                        <i class="bi bi-pencil-fill me-1"></i>Editar
                    </a>
                    <button class="btn btn-sm btn-danger-custom"
                            onclick="confirmDelete('index.php?module=categorias&action=delete&id=<?= $cat['id_categoria'] ?>')">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
