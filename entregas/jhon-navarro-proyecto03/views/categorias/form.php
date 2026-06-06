<?php
$isEdit = isset($id) && $id > 0;
$pageTitle = $isEdit ? 'Editar Categoría' : 'Nueva Categoría';
$breadcrumb = [
    ['label'=>'Categorías','active'=>false,'url'=>'index.php?module=categorias'],
    ['label'=>$pageTitle,'active'=>true,'url'=>''],
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="bi bi-tag-fill me-2"></i><?= $pageTitle ?></h1></div>
    <a href="index.php?module=categorias" class="btn btn-outline-secondary-custom"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-tags me-2"></i>Datos de la Categoría</h6></div>
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
                                      placeholder="Descripción de la categoría..."><?= htmlspecialchars($data['descripcion'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save-fill me-2"></i><?= $isEdit ? 'Guardar' : 'Crear Categoría' ?>
                        </button>
                        <a href="index.php?module=categorias" class="btn btn-outline-secondary-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
