<?php
$pageTitle = 'Iniciar Sesión';
$authPage  = true;
require __DIR__ . '/../layout/auth_header.php';
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-brand text-center mb-4">
            <div class="auth-icon"><i class="bi bi-cup-hot-fill"></i></div>
            <h1>RestaurantePro</h1>
            <p class="text-muted">Inicia sesión para continuar</p>
        </div>

        <?php if (!empty($_GET['success']) && $_GET['success'] === 'mesero_registered'): ?>
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            Mesero registrado correctamente. Ya puedes iniciar sesión.
        </div>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label-custom">Correo electrónico</label>
                <input type="email" name="email" class="form-control-custom"
                       value="<?= htmlspecialchars($email ?? '') ?>" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label-custom">Contraseña</label>
                <input type="password" name="password" class="form-control-custom" required>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
            <p class="text-center mb-1 text-muted">
                ¿No tienes cuenta?
                <a href="index.php?module=auth&action=register">Regístrate como cliente</a>
            </p>
            <p class="text-center mb-0 text-muted">
                ¿Eres mesero?
                <a href="index.php?module=auth&action=register_mesero">Registro de mesero</a>
            </p>
        </form>

        <div class="auth-demo mt-4 p-3 rounded">
            <small class="text-muted d-block mb-2"><strong>Demo meseros:</strong></small>
            <small class="text-muted d-block">pedro.ramirez@restaurante.com / 1234</small>
            <small class="text-muted d-block mt-2"><strong>Demo clientes:</strong></small>
            <small class="text-muted">maria.garcia@email.com / 1234</small>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/auth_footer.php'; ?>
