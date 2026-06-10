<?php
$pageTitle  = 'Editar Mis Datos';
$module     = 'perfil';
$breadcrumb = [
    ['label' => 'Mis Datos', 'active' => false, 'url' => 'index.php?module=perfil'],
    ['label' => 'Editar', 'active' => true, 'url' => ''],
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-pencil-fill me-2"></i>Editar Mis Datos</h1>
    </div>
    <a href="index.php?module=perfil" class="btn btn-outline-secondary-custom"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-body">
                <form method="POST" novalidate>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control-custom <?= !empty($errors['nombre']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($data['nombre'] ?? '') ?>" required>
                            <?php if (!empty($errors['nombre'])): ?><div class="invalid-feedback d-block"><?= $errors['nombre'] ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" class="form-control-custom <?= !empty($errors['apellido']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($data['apellido'] ?? '') ?>" required>
                            <?php if (!empty($errors['apellido'])): ?><div class="invalid-feedback d-block"><?= $errors['apellido'] ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Teléfono</label>
                            <input type="text" name="telefono" class="form-control-custom"
                                   value="<?= htmlspecialchars($data['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Correo <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control-custom <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                            <?php if (!empty($errors['email'])): ?><div class="invalid-feedback d-block"><?= $errors['email'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save-fill me-2"></i>Guardar</button>
                        <a href="index.php?module=perfil" class="btn btn-outline-secondary-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
