<?php
$pageTitle  = 'Menú';
$module     = 'menu';
$breadcrumb = [['label' => 'Menú', 'active' => true]];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-journal-richtext me-2"></i>Menú del Restaurante</h1>
        <p class="page-subtitle">Explora nuestros platos y bebidas disponibles</p>
    </div>
</div>

<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <input type="hidden" name="module" value="menu">
            <div class="flex-grow-1">
                <label class="form-label-custom">Buscar</label>
                <input type="text" name="search" class="form-control-custom" placeholder="Nombre o descripción..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>
            <div>
                <label class="form-label-custom">Categoría</label>
                <select name="categoria" class="form-select-custom">
                    <option value="0">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= $catId == $cat['id_categoria'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary-custom">Filtrar</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($platos)): ?>
    <div class="col-12 text-center text-muted py-5">No hay platos disponibles.</div>
    <?php else: foreach ($platos as $p): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card-modern h-100 menu-card">
            <div class="card-modern-body">
                <span class="badge bg-primary-subtle text-primary mb-2"><?= htmlspecialchars($p['cat_nombre']) ?></span>
                <h5 class="fw-600 mb-2"><?= htmlspecialchars($p['nombre']) ?></h5>
                <p class="text-muted small mb-3"><?= htmlspecialchars($p['descripcion'] ?? '') ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fs-5 fw-700 text-success">$<?= number_format($p['precio'], 0) ?></span>
                    <span class="badge bg-success-subtle text-success">Disponible</span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>

<?php if ($pages > 1): ?>
<nav class="mt-4">
    <ul class="pagination-custom justify-content-center">
        <?php for ($p = 1; $p <= $pages; $p++): ?>
        <li><a href="index.php?module=menu&page=<?= $p ?>&search=<?= urlencode($search) ?>&categoria=<?= $catId ?>"
               class="<?= $page === $p ? 'active' : '' ?>"><?= $p ?></a></li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
