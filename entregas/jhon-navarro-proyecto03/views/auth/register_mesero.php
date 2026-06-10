<?php
$pageTitle = 'Registro de Mesero';
$authPage  = true;
require __DIR__ . '/../layout/auth_header.php';
?>
<div class="auth-wrapper">
    <div class="auth-card auth-card-wide">
        <div class="auth-brand text-center mb-4">
            <div class="auth-icon"><i class="bi bi-person-badge-fill"></i></div>
            <h1>Registro de mesero</h1>
            <p class="text-muted">Crea una cuenta de mesero con autorización del administrador</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="row g-3">
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
                <div class="col-md-6">
                    <label class="form-label-custom">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control-custom <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" required>
                    <?php if (!empty($errors['password'])): ?><div class="invalid-feedback d-block"><?= $errors['password'] ?></div><?php endif; ?>
                    <small class="text-muted">Mín. 8 caracteres, mayúscula, minúscula y número.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Confirmar contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirm" class="form-control-custom <?= !empty($errors['password_confirm']) ? 'is-invalid' : '' ?>" required>
                    <?php if (!empty($errors['password_confirm'])): ?><div class="invalid-feedback d-block"><?= $errors['password_confirm'] ?></div><?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label-custom">Contraseña de administrador <span class="text-danger">*</span></label>
                    <input type="password" name="admin_password" class="form-control-custom <?= !empty($errors['admin_password']) ? 'is-invalid' : '' ?>" required>
                    <?php if (!empty($errors['admin_password'])): ?><div class="invalid-feedback d-block"><?= $errors['admin_password'] ?></div><?php endif; ?>
                    <small class="text-muted">Solo el administrador puede autorizar nuevos meseros.</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 mt-4 mb-3">
                <i class="bi bi-person-badge-fill me-2"></i>Registrar mesero
            </button>
            <p class="text-center mb-0 text-muted">
                <a href="index.php?module=auth&action=login">Volver al inicio de sesión</a>
            </p>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/auth_footer.php'; ?>
