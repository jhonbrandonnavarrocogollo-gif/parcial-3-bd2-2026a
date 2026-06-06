<?php
$isEdit     = isset($id) && $id > 0;
$pageTitle  = $isEdit ? 'Editar Mesa' : 'Nueva Mesa';
$breadcrumb = [
    ['label'=>'Mesas','active'=>false,'url'=>'index.php?module=mesas'],
    ['label'=>$pageTitle,'active'=>true,'url'=>''],
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-<?= $isEdit ? 'pencil-fill' : 'plus-circle-fill' ?> me-2"></i><?= $pageTitle ?>
        </h1>
    </div>
    <a href="index.php?module=mesas" class="btn btn-outline-secondary-custom">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-table me-2"></i>Datos de la Mesa</h6>
            </div>
            <div class="card-modern-body">
                <form method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Número de Mesa <span class="text-danger">*</span></label>
                            <input type="number" name="numero_mesa" min="1"
                                   class="form-control-custom <?= !empty($errors['numero_mesa']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($data['numero_mesa'] ?? '') ?>" required>
                            <?php if (!empty($errors['numero_mesa'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['numero_mesa'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Capacidad <span class="text-danger">*</span></label>
                            <input type="number" name="capacidad" min="1" max="20"
                                   class="form-control-custom <?= !empty($errors['capacidad']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($data['capacidad'] ?? '') ?>" required>
                            <?php if (!empty($errors['capacidad'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['capacidad'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Ubicación <span class="text-danger">*</span></label>
                            <input type="text" name="ubicacion"
                                   class="form-control-custom <?= !empty($errors['ubicacion']) ? 'is-invalid' : '' ?>"
                                   placeholder="Ej: Salón principal, Terraza, VIP..."
                                   value="<?= htmlspecialchars($data['ubicacion'] ?? '') ?>" required>
                            <?php if (!empty($errors['ubicacion'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['ubicacion'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="form-select-custom <?= !empty($errors['estado']) ? 'is-invalid' : '' ?>">
                                <?php foreach (['disponible','ocupada','reservada','mantenimiento'] as $est): ?>
                                <option value="<?= $est ?>" <?= ($data['estado'] ?? 'disponible') === $est ? 'selected' : '' ?>>
                                    <?= ucfirst($est) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['estado'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['estado'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save-fill me-2"></i><?= $isEdit ? 'Guardar Cambios' : 'Crear Mesa' ?>
                        </button>
                        <a href="index.php?module=mesas" class="btn btn-outline-secondary-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
