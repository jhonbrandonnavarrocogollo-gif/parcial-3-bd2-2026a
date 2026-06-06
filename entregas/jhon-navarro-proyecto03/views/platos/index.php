<?php
$pageTitle  = 'Platos & Bebidas';
$breadcrumb = [['label'=>'Platos & Bebidas','active'=>true,'url'=>'']];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-egg-fried me-2"></i>Catálogo del Menú</h1>
        <p class="page-subtitle">Administra platos y bebidas del restaurante</p>
    </div>
    <a href="index.php?module=platos&action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle-fill me-2"></i>Nuevo Plato
    </a>
</div>

<!-- FILTROS -->
<div class="card-modern mb-4">
    <div class="card-modern-body">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
            <input type="hidden" name="module" value="platos">
            <div class="flex-grow-1">
                <label class="form-label-custom">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Nombre o descripción..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div>
                <label class="form-label-custom">Categoría</label>
                <select name="categoria" class="form-select-custom" style="min-width:160px">
                    <option value="0">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_categoria'] ?>" <?= $catId == $cat['id_categoria'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary-custom">Filtrar</button>
            <?php if ($search || $catId): ?>
            <a href="index.php?module=platos" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- CATÁLOGO CARDS -->
<div class="row g-4">
<?php if (empty($platos)): ?>
    <div class="col-12">
        <div class="card-modern text-center py-5">
            <i class="bi bi-egg-fried display-4 text-muted mb-3 d-block"></i>
            <p class="text-muted">No se encontraron platos.</p>
        </div>
    </div>
<?php else: foreach ($platos as $p): ?>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="plato-card <?= $p['disponible'] ? '' : 'plato-card-disabled' ?>">
            <div class="plato-card-badge">
                <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($p['cat_nombre']) ?></span>
                <?php if (!$p['disponible']): ?>
                <span class="badge bg-danger-subtle text-danger ms-1">No disponible</span>
                <?php endif; ?>
            </div>
            <div class="plato-card-body">
                <div class="plato-icon"><i class="bi bi-egg-fried"></i></div>
                <h6 class="plato-name"><?= htmlspecialchars($p['nombre']) ?></h6>
                <p class="plato-desc"><?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción') ?></p>
                <div class="plato-price">$<?= number_format($p['precio'], 2) ?></div>
            </div>
            <div class="plato-card-footer">
                <a href="index.php?module=platos&action=edit&id=<?= $p['id_plato'] ?>"
                   class="btn btn-sm btn-primary-custom flex-grow-1">
                    <i class="bi bi-pencil-fill me-1"></i>Editar
                </a>
                <button class="btn btn-sm btn-danger-custom"
                        onclick="confirmDelete('index.php?module=platos&action=delete&id=<?= $p['id_plato'] ?>')">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>
    </div>
<?php endforeach; endif; ?>
</div>

<!-- PAGINACIÓN -->
<?php if ($pages > 1): ?>
<div class="d-flex justify-content-center mt-4">
    <ul class="pagination-custom">
        <?php for ($p = 1; $p <= $pages; $p++): ?>
        <li>
            <a href="index.php?module=platos&page=<?= $p ?>&search=<?= urlencode($search) ?>&categoria=<?= $catId ?>"
               class="<?= $page === $p ? 'active' : '' ?>"><?= $p ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../layout/footer.php'; ?>
