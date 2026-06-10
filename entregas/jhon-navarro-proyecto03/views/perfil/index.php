<?php
$pageTitle  = 'Mis Datos';
$module     = 'perfil';
$breadcrumb = [['label' => 'Mis Datos', 'active' => true]];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-person-fill me-2"></i>Mis Datos Personales</h1>
        <p class="page-subtitle">Consulta tu información de cuenta</p>
    </div>
    <a href="index.php?module=perfil&action=edit" class="btn btn-primary-custom">
        <i class="bi bi-pencil-fill me-2"></i>Editar Datos
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-person-vcard me-2"></i>Información Personal</h6></div>
            <div class="card-modern-body">
                <table class="w-100 info-table">
                    <tr><td class="text-muted">Nombre:</td><td class="fw-600"><?= htmlspecialchars($data['nombre']) ?></td></tr>
                    <tr><td class="text-muted">Apellido:</td><td class="fw-600"><?= htmlspecialchars($data['apellido']) ?></td></tr>
                    <tr><td class="text-muted">Correo:</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
                    <tr><td class="text-muted">Teléfono:</td><td><?= htmlspecialchars($data['telefono'] ?? '—') ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
