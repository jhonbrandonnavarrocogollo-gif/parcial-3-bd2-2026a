<?php
$pageTitle  = 'Dashboard';
$module     = 'dashboard';
$breadcrumb = [['label' => 'Dashboard', 'active' => true]];
require __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-grid-1x2-fill me-2"></i>Dashboard</h1>
        <p class="page-subtitle">Resumen general del sistema</p>
    </div>
    <span class="badge badge-info-lg">
        <i class="bi bi-circle-fill text-success me-1" style="font-size:.5rem"></i>
        Sistema activo
    </span>
</div>

<!-- STAT CARDS -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['total_clientes']) ?></div>
                <div class="stat-label">Total Clientes</div>
            </div>
            <a href="index.php?module=clientes" class="stat-link">Ver todos <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-indigo">
            <div class="stat-icon"><i class="bi bi-table"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['total_mesas']) ?></div>
                <div class="stat-label">Total Mesas</div>
            </div>
            <a href="index.php?module=mesas" class="stat-link">Ver todas <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-teal">
            <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['total_reservas']) ?></div>
                <div class="stat-label">Total Reservas</div>
            </div>
            <a href="index.php?module=reservas" class="stat-link">Ver todas <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-amber">
            <div class="stat-icon"><i class="bi bi-calendar-day-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['reservas_hoy']) ?></div>
                <div class="stat-label">Reservas Hoy</div>
            </div>
            <a href="index.php?module=reservas" class="stat-link">Ver hoy <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['mesas_disponibles']) ?></div>
                <div class="stat-label">Mesas Disponibles</div>
            </div>
            <a href="index.php?module=mesas" class="stat-link">Ver mesas <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-pink">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['mesas_ocupadas'] ?? 0) ?></div>
                <div class="stat-label">Mesas Ocupadas Hoy</div>
            </div>
            <a href="index.php?module=mesas" class="stat-link">Ver mesas <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="bi bi-receipt-cutoff"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['ordenes_activas']) ?></div>
                <div class="stat-label">Órdenes Activas</div>
            </div>
            <a href="index.php?module=ordenes" class="stat-link">Ver órdenes <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
            <div class="stat-body">
                <div class="stat-value">$<?= number_format($stats['ventas_hoy'], 2) ?></div>
                <div class="stat-label">Ventas Hoy</div>
            </div>
            <a href="index.php?module=reportes" class="stat-link">Ver reportes <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-calendar3 me-2"></i>Reservas — Últimos 7 días</h6>
            </div>
            <div class="card-modern-body">
                <div class="chart-container">
                    <canvas id="chartReservas"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-cash-stack me-2"></i>Ventas — Últimos 7 días</h6>
            </div>
            <div class="card-modern-body">
                <div class="chart-container">
                    <canvas id="chartVentas"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-trophy me-2"></i>Mesas Más Utilizadas</h6>
            </div>
            <div class="card-modern-body">
                <div class="chart-container">
                    <canvas id="chartMesas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS -->
<div class="row g-4">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-modern-header">
                <h6><i class="bi bi-lightning-fill me-2"></i>Acciones Rápidas</h6>
            </div>
            <div class="card-modern-body">
                <div class="d-flex flex-wrap gap-3">
                    <a href="index.php?module=reservas&action=create" class="btn btn-primary-custom">
                        <i class="bi bi-plus-circle-fill me-2"></i>Nueva Reserva
                    </a>
                    <a href="index.php?module=clientes&action=create" class="btn btn-secondary-custom">
                        <i class="bi bi-person-plus-fill me-2"></i>Nuevo Cliente
                    </a>
                    <a href="index.php?module=ordenes&action=create" class="btn btn-success-custom">
                        <i class="bi bi-receipt me-2"></i>Nueva Orden
                    </a>
                    <a href="index.php?module=reportes" class="btn btn-info-custom">
                        <i class="bi bi-bar-chart me-2"></i>Ver Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Preparar datos para Chart.js
$labelsReservas = json_encode(array_column($reservasPorDia, 'dia'));
$dataReservas   = json_encode(array_map('intval', array_column($reservasPorDia, 'total')));
$labelsVentas   = json_encode(array_column($ventasPorDia, 'dia'));
$dataVentas     = json_encode(array_map('floatval', array_column($ventasPorDia, 'total_ventas')));
$labelsMesas    = json_encode(array_map(fn($m) => 'Mesa ' . $m['numero_mesa'], $mesasTop));
$dataMesas      = json_encode(array_map('intval', array_column($mesasTop, 'total_reservas')));

$extraJs = <<<JS
// Chart Reservas
(function(){
    const ctx = document.getElementById('chartReservas');
    if(!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {$labelsReservas},
            datasets:[{
                label:'Reservas',
                data:{$dataReservas},
                backgroundColor:'rgba(67,97,238,0.75)',
                borderColor:'rgba(67,97,238,1)',
                borderWidth:2,
                borderRadius:8,
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>'Reservas: '+c.raw}}},
            scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1}}}
        }
    });
})();
// Chart Ventas
(function(){
    const ctx = document.getElementById('chartVentas');
    if(!ctx) return;
    new Chart(ctx, {
        type:'line',
        data:{
            labels:{$labelsVentas},
            datasets:[{
                label:'Ventas ($)',
                data:{$dataVentas},
                borderColor:'rgba(76,201,160,1)',
                backgroundColor:'rgba(76,201,160,0.15)',
                tension:0.4,
                fill:true,
                pointBackgroundColor:'rgba(76,201,160,1)',
                pointRadius:5,
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{x:{grid:{display:false}},y:{beginAtZero:true}}
        }
    });
})();
// Chart Mesas
(function(){
    const ctx = document.getElementById('chartMesas');
    if(!ctx) return;
    new Chart(ctx, {
        type:'doughnut',
        data:{
            labels:{$labelsMesas},
            datasets:[{
                data:{$dataMesas},
                backgroundColor:['#4361ee','#2ecc71','#f39c12','#e74c3c','#9b59b6'],
                borderWidth:2, borderColor:'#fff'
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{position:'bottom', labels:{boxWidth:12, font:{size:11}}}}
        }
    });
})();
JS;
require __DIR__ . '/../layout/footer.php';
?>
