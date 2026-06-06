<?php
$isEdit     = isset($id) && $id > 0;
$pageTitle  = $isEdit ? 'Editar Reserva' : 'Nueva Reserva';
$excludeId  = $isEdit ? $id : 0;
$breadcrumb = [
    ['label'=>'Reservas','active'=>false,'url'=>'index.php?module=reservas'],
    ['label'=>$pageTitle,'active'=>true,'url'=>''],
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar-plus-fill me-2"></i><?= $pageTitle ?></h1>
    </div>
    <a href="index.php?module=reservas" class="btn btn-outline-secondary-custom">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<?php if (!empty($errors['conflicto'])): ?>
<div class="alert alert-danger d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
    <strong><?= $errors['conflicto'] ?></strong>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-calendar3 me-2"></i>Datos de la Reserva</h6>
            </div>
            <div class="card-modern-body">
                <form method="POST" id="formReserva" novalidate>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Cliente <span class="text-danger">*</span></label>
                            <select name="id_cliente" class="form-select-custom <?= !empty($errors['id_cliente']) ? 'is-invalid' : '' ?>" required>
                                <option value="">— Seleccionar cliente —</option>
                                <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>"
                                    <?= ($data['id_cliente'] ?? 0) == $c['id_cliente'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['id_cliente'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['id_cliente'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Mesa <span class="text-danger">*</span></label>
                            <select name="id_mesa" id="id_mesa" class="form-select-custom <?= !empty($errors['id_mesa']) ? 'is-invalid' : '' ?>" required>
                                <option value="">— Seleccionar mesa —</option>
                                <?php foreach ($mesas as $m): ?>
                                <option value="<?= $m['id_mesa'] ?>"
                                    <?= ($data['id_mesa'] ?? 0) == $m['id_mesa'] ? 'selected' : '' ?>>
                                    Mesa <?= $m['numero_mesa'] ?> (<?= $m['capacidad'] ?> personas — <?= htmlspecialchars($m['ubicacion']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($errors['id_mesa'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['id_mesa'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Fecha/Hora Inicio <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha_hora_inicio" id="fechaInicio"
                                   class="form-control-custom <?= !empty($errors['fecha_hora_inicio']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars(str_replace(' ','T',$data['fecha_hora_inicio'] ?? '')) ?>" required>
                            <?php if (!empty($errors['fecha_hora_inicio'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['fecha_hora_inicio'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Fecha/Hora Fin <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha_hora_fin" id="fechaFin"
                                   class="form-control-custom <?= !empty($errors['fecha_hora_fin']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars(str_replace(' ','T',$data['fecha_hora_fin'] ?? '')) ?>" required>
                            <?php if (!empty($errors['fecha_hora_fin'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['fecha_hora_fin'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">N° Personas <span class="text-danger">*</span></label>
                            <input type="number" name="num_personas" min="1" max="50"
                                   class="form-control-custom <?= !empty($errors['num_personas']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($data['num_personas'] ?? 1) ?>" required>
                            <?php if (!empty($errors['num_personas'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['num_personas'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">Estado</label>
                            <select name="estado" class="form-select-custom">
                                <?php foreach (['pendiente','confirmada','cancelada','completada'] as $est): ?>
                                <option value="<?= $est ?>" <?= ($data['estado'] ?? 'pendiente') === $est ? 'selected' : '' ?>>
                                    <?= ucfirst($est) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Notas</label>
                            <textarea name="notas" class="form-control-custom" rows="2"
                                      placeholder="Observaciones adicionales..."><?= htmlspecialchars($data['notas'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Alerta de conflicto AJAX -->
                    <div id="conflictoAlert" class="alert alert-danger d-none mt-3 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                        <span>Ya existe una reserva para esa mesa en ese horario.</span>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary-custom" id="btnGuardar">
                            <i class="bi bi-save-fill me-2"></i><?= $isEdit ? 'Guardar Cambios' : 'Crear Reserva' ?>
                        </button>
                        <a href="index.php?module=reservas" class="btn btn-outline-secondary-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$extraJs = "
const excludeId = " . $excludeId . ";
document.addEventListener('DOMContentLoaded', function(){
    const mesa    = document.getElementById('id_mesa');
    const inicio  = document.getElementById('fechaInicio');
    const fin     = document.getElementById('fechaFin');
    const alerta  = document.getElementById('conflictoAlert');
    const btnGuardar = document.getElementById('btnGuardar');

    function checkConflicto(){
        if(!mesa.value || !inicio.value || !fin.value) return;
        const fd = new FormData();
        fd.append('id_mesa',  mesa.value);
        fd.append('inicio',   inicio.value.replace('T',' '));
        fd.append('fin',      fin.value.replace('T',' '));
        fd.append('exclude',  excludeId);
        fetch('index.php?module=reservas&action=checkConflicto', {method:'POST', body:fd})
            .then(r=>r.json())
            .then(d=>{
                if(d.conflicto){
                    alerta.classList.remove('d-none');
                    btnGuardar.disabled = true;
                } else {
                    alerta.classList.add('d-none');
                    btnGuardar.disabled = false;
                }
            });
    }
    mesa.addEventListener('change', checkConflicto);
    inicio.addEventListener('change', checkConflicto);
    fin.addEventListener('change', checkConflicto);
});
";
require __DIR__ . '/../layout/footer.php';
?>
