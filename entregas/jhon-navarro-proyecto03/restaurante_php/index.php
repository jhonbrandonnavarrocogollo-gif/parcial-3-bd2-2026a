<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>La Mesa — Sistema de Gestión</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>La Mesa</h1>
    <p>Sistema de gestión</p>
  </div>
  <nav>
    <div class="nav-section">Principal</div>
    <div class="nav-item active" onclick="showSection('dashboard', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
      </svg>
      Dashboard
    </div>

    <div class="nav-section">Operaciones</div>
    <div class="nav-item" onclick="showSection('reservas', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      Reservas
    </div>
    <div class="nav-item" onclick="showSection('ordenes', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
        <rect x="9" y="3" width="6" height="4" rx="1"/>
        <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
      </svg>
      Órdenes
    </div>

    <div class="nav-section">Catálogos</div>
    <div class="nav-item" onclick="showSection('mesas', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M3 6h18M3 18h18M12 6v12M6 6v12M18 6v12"/>
      </svg>
      Mesas
    </div>
    <div class="nav-item" onclick="showSection('clientes', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
      </svg>
      Clientes
    </div>
    <div class="nav-item" onclick="showSection('meseros', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
      </svg>
      Meseros
    </div>
    <div class="nav-item" onclick="showSection('carta', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
        <rect x="9" y="3" width="6" height="4" rx="1"/>
        <line x1="9" y1="12" x2="15" y2="12"/>
        <line x1="9" y1="16" x2="15" y2="16"/>
      </svg>
      Carta
    </div>

    <div class="nav-section">Análisis</div>
    <div class="nav-item" onclick="showSection('reportes', this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <line x1="18" y1="20" x2="18" y2="10"/>
        <line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="6"  y1="20" x2="6"  y2="14"/>
      </svg>
      Reportes
    </div>
  </nav>
</aside>

<!-- ══════════════════════════════════════════
     MAIN
══════════════════════════════════════════ -->
<main class="main">
  <div class="topbar">
    <h2 id="topbar-title">Dashboard</h2>
    <div class="topbar-actions">
      <!-- Botón extra para carta: nueva categoría -->
      <button id="btn-cat" class="btn btn-ghost" style="display:none" onclick="categoriaModal()">+ Categoría</button>
      <button id="btn-add" class="btn btn-primary" style="display:none" onclick="handleAdd()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nuevo
      </button>
    </div>
  </div>

  <div class="content">

    <!-- ── DASHBOARD ─────────────────────────── -->
    <div class="section active" id="sec-dashboard">
      <div class="stats-grid">
        <div class="stat-card">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 6h18M3 18h18M12 6v12"/></svg>
          <div class="label">Mesas totales</div>
          <div class="value" id="s-mesas">—</div>
          <div class="sub">en el restaurante</div>
        </div>
        <div class="stat-card">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <div class="label">Reservas hoy</div>
          <div class="value" id="s-reservas">—</div>
          <div class="sub">del día actual</div>
        </div>
        <div class="stat-card">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
          <div class="label">Órdenes activas</div>
          <div class="value" id="s-ordenes">—</div>
          <div class="sub">en curso</div>
        </div>
        <div class="stat-card">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
          <div class="label">Clientes</div>
          <div class="value" id="s-clientes">—</div>
          <div class="sub">registrados</div>
        </div>
      </div>

      <div class="table-card">
        <div class="table-card-header"><h3>Reservas recientes</h3></div>
        <table>
          <thead><tr><th>Cliente</th><th>Mesa</th><th>Inicio</th><th>Personas</th><th>Estado</th></tr></thead>
          <tbody id="dash-table"></tbody>
        </table>
      </div>
    </div>

    <!-- ── MESAS ──────────────────────────────── -->
    <div class="section" id="sec-mesas">
      <div class="table-card">
        <div class="table-card-header"><h3>Mesas del restaurante</h3></div>
        <table>
          <thead><tr><th>ID</th><th>N°</th><th>Capacidad</th><th>Ubicación</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody id="tb-mesas"></tbody>
        </table>
      </div>
    </div>

    <!-- ── CLIENTES ───────────────────────────── -->
    <div class="section" id="sec-clientes">
      <div class="table-card">
        <div class="table-card-header"><h3>Clientes registrados</h3></div>
        <table>
          <thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr></thead>
          <tbody id="tb-clientes"></tbody>
        </table>
      </div>
    </div>

    <!-- ── MESEROS ────────────────────────────── -->
    <div class="section" id="sec-meseros">
      <div class="table-card">
        <div class="table-card-header"><h3>Personal de sala</h3></div>
        <table>
          <thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Acciones</th></tr></thead>
          <tbody id="tb-meseros"></tbody>
        </table>
      </div>
    </div>

    <!-- ── CARTA ──────────────────────────────── -->
    <div class="section" id="sec-carta">
      <div class="table-card">
        <div class="table-card-header"><h3>Carta del restaurante</h3></div>
        <table>
          <thead><tr><th>ID</th><th>Plato</th><th>Categoría</th><th>Precio</th><th>Disp.</th><th>Acciones</th></tr></thead>
          <tbody id="tb-platos"></tbody>
        </table>
      </div>
    </div>

    <!-- ── RESERVAS ───────────────────────────── -->
    <div class="section" id="sec-reservas">
      <div class="table-card">
        <div class="table-card-header"><h3>Reservas</h3></div>
        <table>
          <thead><tr><th>ID</th><th>Cliente</th><th>Mesa</th><th>Inicio</th><th>Fin</th><th>Personas</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody id="tb-reservas"></tbody>
        </table>
      </div>
    </div>

    <!-- ── ÓRDENES ────────────────────────────── -->
    <div class="section" id="sec-ordenes">
      <div class="table-card">
        <div class="table-card-header"><h3>Órdenes de consumo</h3></div>
        <table>
          <thead><tr><th>Orden</th><th>Mesa</th><th>Mesero</th><th>Fecha</th><th>Estado</th><th>Total</th><th>Acciones</th></tr></thead>
          <tbody id="tb-ordenes"></tbody>
        </table>
      </div>
    </div>

    <!-- ── REPORTES ───────────────────────────── -->
    <div class="section" id="sec-reportes">
      <div class="report-grid">
        <div class="table-card">
          <div class="table-card-header"><h3>Ocupación por día</h3></div>
          <table>
            <thead><tr><th>Día</th><th>Total</th><th>Completadas</th><th>Canceladas</th><th>Confirmadas</th></tr></thead>
            <tbody id="tb-ocupacion"></tbody>
          </table>
        </div>
        <div class="table-card">
          <div class="table-card-header"><h3>Mesas más reservadas</h3></div>
          <table>
            <thead><tr><th>#</th><th>Mesa</th><th>Ubicación</th><th>Reservas</th><th>Completadas</th></tr></thead>
            <tbody id="tb-mesas"></tbody>
          </table>
        </div>
      </div>
    </div>

  </div><!-- /content -->
</main>

<!-- ══════════════════════════════════════════
     MODAL GENÉRICO
══════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-overlay" onclick="if(event.target===this)closeModal()">
  <div class="modal" id="modal">
    <div class="modal-head">
      <h3 id="modal-title">—</h3>
      <button class="btn btn-ghost btn-sm" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" id="modal-body"></div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-primary" id="modal-save">Guardar</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL ORDEN
══════════════════════════════════════════ -->
<div class="modal-overlay" id="orden-overlay" onclick="if(event.target===this)closeOrdenModal()">
  <div class="modal modal-lg">
    <div class="modal-head">
      <h3 id="orden-title">Orden</h3>
      <button class="btn btn-ghost btn-sm" onclick="closeOrdenModal()">✕</button>
    </div>
    <div class="modal-body" id="orden-body"></div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="closeOrdenModal()">Cerrar</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     TOASTS
══════════════════════════════════════════ -->
<div class="toast-container" id="toast-container"></div>

<script src="assets/js/app.js"></script>
<script>
  // Mostrar botón de categoría solo en carta
  const _origShow = window.showSection;
  window.showSection = (name, el) => {
    _origShow(name, el);
    document.getElementById('btn-cat').style.display = name === 'carta' ? 'flex' : 'none';
  };
</script>
</body>
</html>
