<?php
$pageTitle  = 'Nueva Orden';
$breadcrumb = [
    ['label'=>'Órdenes','active'=>false,'url'=>'index.php?module=ordenes'],
    ['label'=>'Nueva Orden','active'=>true,'url'=>''],
];
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="bi bi-receipt me-2"></i>Nueva Orden de Consumo</h1></div>
    <a href="index.php?module=ordenes" class="btn btn-outline-secondary-custom"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>

<?php if (!empty($errors['items'])): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $errors['items'] ?></div>
<?php endif; ?>

<form method="POST" id="formOrden">
<div class="row g-4">
    <!-- Columna izquierda: datos generales -->
    <div class="col-lg-4">
        <div class="card-modern mb-4">
            <div class="card-modern-header"><h6><i class="bi bi-info-circle me-2"></i>Datos de la Orden</h6></div>
            <div class="card-modern-body">
                <div class="mb-3">
                    <label class="form-label-custom">Mesa <span class="text-danger">*</span></label>
                    <select name="id_mesa" class="form-select-custom <?= !empty($errors['id_mesa']) ? 'is-invalid' : '' ?>" required>
                        <option value="">— Seleccionar mesa —</option>
                        <?php foreach ($mesas as $m): ?>
                        <option value="<?= $m['id_mesa'] ?>"><?= 'Mesa ' . $m['numero_mesa'] . ' (' . htmlspecialchars($m['ubicacion']) . ')' ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['id_mesa'])): ?>
                    <div class="invalid-feedback d-block"><?= $errors['id_mesa'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Mesero</label>
                    <select name="id_mesero" class="form-select-custom">
                        <option value="">— Sin asignar —</option>
                        <?php foreach ($meseros as $ms): ?>
                        <option value="<?= $ms['id_mesero'] ?>"><?= htmlspecialchars($ms['nombre'] . ' ' . $ms['apellido']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label-custom">Reserva (opcional)</label>
                    <select name="id_reserva" class="form-select-custom">
                        <option value="">— Sin reserva —</option>
                        <?php foreach ($reservas as $r): ?>
                        <option value="<?= $r['id_reserva'] ?>">
                            #<?= $r['id_reserva'] ?> — <?= htmlspecialchars($r['cli_nombre'] . ' ' . $r['cli_apellido']) ?>
                            (Mesa <?= $r['numero_mesa'] ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label-custom">Estado Inicial</label>
                    <select name="estado" class="form-select-custom">
                        <option value="recibida">Recibida</option>
                        <option value="en_cocina">En cocina</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- TOTALES -->
        <div class="card-modern">
            <div class="card-modern-header"><h6><i class="bi bi-calculator me-2"></i>Totales</h6></div>
            <div class="card-modern-body">
                <table class="w-100">
                    <tr>
                        <td class="text-muted">Subtotal:</td>
                        <td class="text-end fw-600" id="lblSubtotal">$0.00</td>
                    </tr>
                    <tr>
                        <td class="text-muted">IVA (0%):</td>
                        <td class="text-end">$0.00</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-700 pt-2">TOTAL:</td>
                        <td class="text-end fw-700 pt-2 text-success fs-5" id="lblTotal">$0.00</td>
                    </tr>
                </table>
                <button type="submit" class="btn btn-primary-custom w-100 mt-3">
                    <i class="bi bi-save-fill me-2"></i>Guardar Orden
                </button>
            </div>
        </div>
    </div>

    <!-- Columna derecha: items -->
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-modern-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-basket me-2"></i>Productos de la Orden</h6>
                <button type="button" class="btn btn-sm btn-success-custom" id="btnAgregarItem">
                    <i class="bi bi-plus-lg me-1"></i>Agregar Producto
                </button>
            </div>
            <div class="card-modern-body p-0">
                <div class="table-responsive">
                    <table class="table-modern" id="tablaItems">
                        <thead>
                            <tr>
                                <th style="min-width:200px">Plato</th>
                                <th style="width:90px">Cantidad</th>
                                <th style="width:120px">Precio Unit.</th>
                                <th style="width:120px">Subtotal</th>
                                <th style="width:50px">Notas</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Fila inicial -->
                            <tr class="item-row">
                                <td>
                                    <select name="plato_id[]" class="form-select-custom select-plato">
                                        <option value="">— Seleccionar —</option>
                                        <?php foreach ($platos as $pl): ?>
                                        <option value="<?= $pl['id_plato'] ?>" data-precio="<?= $pl['precio'] ?>">
                                            <?= htmlspecialchars($pl['nombre']) ?> (<?= htmlspecialchars($pl['cat_nombre']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="cantidad[]" value="1" min="1" class="form-control-custom input-cantidad"></td>
                                <td><input type="number" name="precio_unit[]" value="0.00" step="0.01" min="0" class="form-control-custom input-precio"></td>
                                <td><input type="text" class="form-control-custom input-subtotal" value="$0.00" readonly></td>
                                <td><input type="text" name="notas_item[]" class="form-control-custom" placeholder="..."></td>
                                <td>
                                    <button type="button" class="btn-action btn-delete btn-remove-row">
                                        <i class="bi bi-dash-circle-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<?php
$platosJson = json_encode(array_column($platos, 'precio', 'id_plato'));
$extraJs = <<<JS
const preciosPlatos = {$platosJson};

function calcularFila(row){
    const cant   = parseFloat(row.querySelector('.input-cantidad').value) || 0;
    const precio = parseFloat(row.querySelector('.input-precio').value)   || 0;
    const sub    = cant * precio;
    row.querySelector('.input-subtotal').value = '\$' + sub.toFixed(2);
    return sub;
}

function calcularTotal(){
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => { total += calcularFila(row); });
    document.getElementById('lblSubtotal').textContent = '\$' + total.toFixed(2);
    document.getElementById('lblTotal').textContent    = '\$' + total.toFixed(2);
}

function filaTemplate(){
    const row = document.querySelector('.item-row').cloneNode(true);
    row.querySelector('.select-plato').value = '';
    row.querySelector('.input-cantidad').value = '1';
    row.querySelector('.input-precio').value = '0.00';
    row.querySelector('.input-subtotal').value = '\$0.00';
    row.querySelector('input[name="notas_item[]"]').value = '';
    return row;
}

document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('btnAgregarItem').addEventListener('click', function(){
        const row = filaTemplate();
        document.getElementById('itemsBody').appendChild(row);
        bindFila(row);
    });

    function bindFila(row){
        row.querySelector('.select-plato').addEventListener('change', function(){
            const precio = preciosPlatos[this.value] || 0;
            row.querySelector('.input-precio').value = parseFloat(precio).toFixed(2);
            calcularTotal();
        });
        row.querySelector('.input-cantidad').addEventListener('input', calcularTotal);
        row.querySelector('.input-precio').addEventListener('input', calcularTotal);
        row.querySelector('.btn-remove-row').addEventListener('click', function(){
            if(document.querySelectorAll('.item-row').length > 1){
                row.remove(); calcularTotal();
            }
        });
    }

    document.querySelectorAll('.item-row').forEach(bindFila);
    calcularTotal();
});
JS;
require __DIR__ . '/../layout/footer.php';
?>
