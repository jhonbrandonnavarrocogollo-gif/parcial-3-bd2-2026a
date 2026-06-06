<?php
$pageTitle  = 'Reportes';
$module     = 'reportes';
$breadcrumb = [['label' => 'Reportes', 'active' => true]];
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-bar-chart-fill me-2"></i>Reportes Estadísticos</h1>
        <p class="page-subtitle">Análisis de ocupación, reservas y ventas — últimos 30 días</p>
    </div>
</div>

<!-- REPORTE 1: Ocupación diaria -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-calendar-week me-2"></i>Reporte 1 — Ocupación Diaria</h6>
            </div>
            <div class="card-modern-body">
                <div class="chart-container">
                    <canvas id="chartOcupacion"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-table me-2"></i>Detalle de Ocupación</h6>
            </div>
            <div class="card-modern-body p-0" style="max-height:340px;overflow-y:auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th class="text-center">Reservas</th>
                            <th class="text-center">Mesas usadas</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ocupacion)): ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">Sin datos.</td></tr>
                    <?php else: foreach (array_slice($ocupacion, 0, 10) as $o): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($o['dia'])) ?></td>
                            <td class="text-center"><?= $o['total_reservas'] ?></td>
                            <td class="text-center"><?= $o['mesas_usadas'] ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- REPORTE 2: Mesas más reservadas -->
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-trophy-fill me-2"></i>Reporte 2 — Mesas Más Reservadas</h6>
            </div>
            <div class="card-modern-body">
                <?php if (empty($mesasTop)): ?>
                    <p class="text-muted text-center py-4">Sin datos de reservas.</p>
                <?php else: foreach ($mesasTop as $i => $m): ?>
                    <?php
                    $rankClass = match($i) { 0 => 'gold', 1 => 'silver', 2 => 'bronze', default => '' };
                    ?>
                    <div class="report-rank">
                        <div class="rank-num <?= $rankClass ?>"><?= $i + 1 ?></div>
                        <div class="flex-grow-1">
                            <div class="fw-600">Mesa <?= $m['numero_mesa'] ?></div>
                            <small class="text-muted"><?= htmlspecialchars($m['ubicacion']) ?></small>
                        </div>
                        <div class="text-end">
                            <div class="fw-700 text-primary"><?= $m['total_reservas'] ?></div>
                            <small class="text-muted">reservas</small>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-bar-chart me-2"></i>Ranking de Mesas</h6>
            </div>
            <div class="card-modern-body">
                <div class="chart-container">
                    <canvas id="chartMesasTop"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REPORTE 3: Ventas por fecha -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-cash-stack me-2"></i>Reporte 3 — Ventas por Fecha</h6>
            </div>
            <div class="card-modern-body">
                <div class="chart-container">
                    <canvas id="chartVentas"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-receipt me-2"></i>Detalle de Ventas</h6>
            </div>
            <div class="card-modern-body p-0" style="max-height:340px;overflow-y:auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th class="text-center">Órdenes</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ventas)): ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">Sin ventas registradas.</td></tr>
                    <?php else: foreach (array_slice($ventas, 0, 10) as $v): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($v['dia'])) ?></td>
                            <td class="text-center"><?= $v['num_ordenes'] ?></td>
                            <td class="text-end fw-600 text-success">$<?= number_format($v['total_ventas'], 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- REPORTE 4: Productos más vendidos -->
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-basket-fill me-2"></i>Reporte 4 — Productos Más Vendidos</h6>
            </div>
            <div class="card-modern-body p-0" style="max-height:400px;overflow-y:auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($productosTop)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Sin ventas de productos.</td></tr>
                    <?php else: foreach ($productosTop as $i => $p): ?>
                        <tr>
                            <td><span class="badge-id"><?= $i + 1 ?></span></td>
                            <td class="fw-600"><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($p['categoria']) ?></span></td>
                            <td class="text-center fw-700"><?= $p['total_vendido'] ?></td>
                            <td class="text-end text-success">$<?= number_format($p['ingresos'], 2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <h6><i class="bi bi-pie-chart me-2"></i>Distribución de Ventas por Producto</h6>
            </div>
            <div class="card-modern-body">
                <div class="chart-container">
                    <canvas id="chartProductos"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Preparar datos para gráficos (invertir para orden cronológico)
$ocupacionAsc  = array_reverse($ocupacion);
$labelsOcup    = json_encode(array_map(fn($o) => date('d/m', strtotime($o['dia'])), $ocupacionAsc));
$dataReservas  = json_encode(array_map('intval', array_column($ocupacionAsc, 'total_reservas')));
$dataMesasUsad = json_encode(array_map('intval', array_column($ocupacionAsc, 'mesas_usadas')));

$labelsMesasTop = json_encode(array_map(fn($m) => 'Mesa ' . $m['numero_mesa'], $mesasTop));
$dataMesasTop   = json_encode(array_map('intval', array_column($mesasTop, 'total_reservas')));

$ventasAsc     = array_reverse($ventas);
$labelsVentas  = json_encode(array_map(fn($v) => date('d/m', strtotime($v['dia'])), $ventasAsc));
$dataVentas    = json_encode(array_map('floatval', array_column($ventasAsc, 'total_ventas')));

$labelsProd    = json_encode(array_column($productosTop, 'nombre'));
$dataProd      = json_encode(array_map('intval', array_column($productosTop, 'total_vendido')));

$extraJs = <<<JS
(function(){
    const colors = ['#4361ee','#2ecc71','#f39c12','#e74c3c','#3498db','#9b59b6','#1abc9c','#e67e22','#34495e','#e91e63'];

    // Reporte 1 — Ocupación
    new Chart(document.getElementById('chartOcupacion'), {
        type: 'bar',
        data: {
            labels: {$labelsOcup},
            datasets: [
                { label: 'Reservas', data: {$dataReservas}, backgroundColor: 'rgba(67,97,238,0.75)', borderRadius: 6 },
                { label: 'Mesas usadas', data: {$dataMesasUsad}, backgroundColor: 'rgba(46,204,113,0.75)', borderRadius: 6 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Reporte 2 — Mesas top
    new Chart(document.getElementById('chartMesasTop'), {
        type: 'bar',
        data: {
            labels: {$labelsMesasTop},
            datasets: [{ label: 'Reservas', data: {$dataMesasTop}, backgroundColor: colors, borderRadius: 8 }]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Reporte 3 — Ventas
    new Chart(document.getElementById('chartVentas'), {
        type: 'line',
        data: {
            labels: {$labelsVentas},
            datasets: [{
                label: 'Ventas (\$)', data: {$dataVentas},
                borderColor: '#2ecc71', backgroundColor: 'rgba(46,204,113,0.15)',
                tension: 0.4, fill: true, pointRadius: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
        }
    });

    // Reporte 4 — Productos
    new Chart(document.getElementById('chartProductos'), {
        type: 'doughnut',
        data: {
            labels: {$labelsProd},
            datasets: [{ data: {$dataProd}, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } }
        }
    });
})();
JS;

require __DIR__ . '/../layout/footer.php';
?>
