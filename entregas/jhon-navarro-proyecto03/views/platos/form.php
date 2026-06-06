<?php
$isEdit     = isset($id) && $id > 0;
$pageTitle  = $isEdit ? 'Editar Plato' : 'Nuevo Plato';
$breadcrumb = [
    ['label'=>'Platos','active'=>false,'url'=>'index.php?module=platos'],
    ['label'=>$pageTitle,'active'=>true,'url'=>''],
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="bi bi-egg-fried me-2"></i><?= $pageTitle ?></h1></div>
    <a href="index.php?module=platos" class="btn btn-outline-secondary-custom"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-egg-fried me-2"></i>Datos del Plato</h6></div>
            <div class="card-modern-body">
                <form method="POST">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label-custom">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre"
                                   class="form-control-custom <?= !empty($errors['nombre']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($data['nombre'] ?? '') ?>" required>
                            <?php if (!empty($errors['nombre'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['nombre'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Descripción</label>
                            <textarea name="descripcion" class="form-control-custom" rows="3"
                                      placeholder="Descripción del plato..."><?= htmlspecialchars($data['descripcion'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Precio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio" step="0.01" min="0"
                                       class="form-control <?= !empty($errors['precio']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($data['precio'] ?? '0.00') ?>" required>
                            </div>
                            <?php if (!empty($errors['precio'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['precio'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Categoría <span class="text-danger">*</span></label>
                            <select name="id_categoria"
                                    class="form-select-custom <?= !empty($errors['id_categoria']) ? 'is-invalid' : '' ?>">
                                <option value="">— Seleccionar —</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>"
                                    <?= ($data['id_categoria'] ?? 0) == $cat['id_categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['id_categoria'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['id_categoria'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="disponible" id="disponible"
                                       <?= ($data['disponible'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-500" for="disponible">
                                    Disponible en menú
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save-fill me-2"></i><?= $isEdit ? 'Guardar Cambios' : 'Crear Plato' ?>
                        </button>
                        <a href="index.php?module=platos" class="btn btn-outline-secondary-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
