/* ── API HELPER ──────────────────────────────────────────── */
const api = async (resource, method = 'GET', body = null, params = {}) => {
  const qs = new URLSearchParams({ resource, ...params }).toString();
  const res = await fetch(`api.php?${qs}`, {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: body ? JSON.stringify(body) : null
  });
  return res.json();
};

/* ── TOAST ───────────────────────────────────────────────── */
const toast = (msg, type = 'ok') => {
  const c = document.getElementById('toast-container');
  const t = document.createElement('div');
  t.className = `toast ${type === 'error' ? 'error' : type === 'success' ? 'success' : ''}`;
  t.innerHTML = `<span>${type === 'error' ? '✗' : '✓'}</span> ${msg}`;
  c.appendChild(t);
  requestAnimationFrame(() => { t.classList.add('show'); });
  setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3200);
};

/* ── BADGES & FORMATS ────────────────────────────────────── */
const badge = v => `<span class="badge ${v.replace(' ','_')}">${v.replace('_',' ')}</span>`;

const fmt = s => {
  if (!s) return '—';
  const d = new Date(s);
  return d.toLocaleString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const money = v => `$${parseFloat(v || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

/* ── CACHE ───────────────────────────────────────────────── */
let cache = { mesas: [], clientes: [], meseros: [], categorias: [], platos: [] };

/* ── MODAL ───────────────────────────────────────────────── */
let modalSave = null;
let editId    = null;

const openModal = (title, bodyHTML, onSave, large = false) => {
  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = bodyHTML;
  document.getElementById('modal').className = `modal ${large ? 'modal-lg' : ''}`;
  document.getElementById('modal-overlay').classList.add('open');
  modalSave = onSave;
};
const closeModal = () => {
  document.getElementById('modal-overlay').classList.remove('open');
  modalSave = null; editId = null;
};
const closeOrdenModal = () => document.getElementById('orden-overlay').classList.remove('open');

document.getElementById('modal-save').addEventListener('click', async () => {
  if (modalSave) {
    document.getElementById('modal-save').disabled = true;
    await modalSave();
    document.getElementById('modal-save').disabled = false;
    closeModal();
  }
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModal(); closeOrdenModal(); } });

/* ── NAVEGACIÓN ──────────────────────────────────────────── */
let currentSection = 'dashboard';

window.showSection = (name, el) => {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('sec-' + name).classList.add('active');
  if (el) el.classList.add('active');
  currentSection = name;

  const titles = {
    dashboard: 'Dashboard', mesas: 'Mesas', clientes: 'Clientes',
    meseros: 'Meseros', carta: 'Carta', reservas: 'Reservas',
    ordenes: 'Órdenes', reportes: 'Reportes'
  };
  document.getElementById('topbar-title').textContent = titles[name] || name;

  const addBtn = document.getElementById('btn-add');
  addBtn.style.display = ['mesas','clientes','meseros','carta','reservas','ordenes'].includes(name) ? 'flex' : 'none';

  loaders[name]?.();
};

window.handleAdd = () => adders[currentSection]?.();

const loaders = {
  dashboard: loadDashboard,
  mesas:     loadMesas,
  clientes:  loadClientes,
  meseros:   loadMeseros,
  carta:     loadCarta,
  reservas:  loadReservas,
  ordenes:   loadOrdenes,
  reportes:  loadReportes,
};
const adders = {
  mesas:    () => mesaModal(),
  clientes: () => clienteModal(),
  meseros:  () => meseroModal(),
  carta:    () => platoModal(),
  reservas: () => reservaModal(),
  ordenes:  () => ordenModal(),
};

/* ════════════════════════════════════════════════════════════
   DASHBOARD
════════════════════════════════════════════════════════════ */
async function loadDashboard() {
  const d = await api('dashboard');
  document.getElementById('s-mesas').textContent    = d.mesas;
  document.getElementById('s-clientes').textContent = d.clientes;
  document.getElementById('s-reservas').textContent = d.reservasH;
  document.getElementById('s-ordenes').textContent  = d.ordenesA;

  document.getElementById('dash-table').innerHTML = d.recientes.length
    ? d.recientes.map(r => `
      <tr>
        <td><strong>${r.cliente_nombre}</strong></td>
        <td>Mesa ${r.numero_mesa}</td>
        <td>${fmt(r.fecha_hora_inicio)}</td>
        <td>${r.num_personas}</td>
        <td>${badge(r.estado)}</td>
      </tr>`).join('')
    : `<tr><td colspan="5" class="empty"><p>Sin reservas</p></td></tr>`;
}

/* ════════════════════════════════════════════════════════════
   MESAS
════════════════════════════════════════════════════════════ */
async function loadMesas() {
  const data = await api('mesas');
  cache.mesas = data;
  document.getElementById('tb-mesas').innerHTML = data.length
    ? data.map(m => `
      <tr>
        <td>${m.id_mesa}</td>
        <td><strong>${m.numero_mesa}</strong></td>
        <td>${m.capacidad} personas</td>
        <td>${m.ubicacion}</td>
        <td>${badge(m.estado)}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="mesaModal(${m.id_mesa})">Editar</button>
          <button class="btn btn-danger btn-sm" onclick="deleteMesa(${m.id_mesa})">Eliminar</button>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="6" class="empty"><p>No hay mesas registradas</p></td></tr>`;
}

window.mesaModal = (id = null) => {
  editId = id;
  const m = id ? cache.mesas.find(x => x.id_mesa == id) : null;
  openModal(id ? 'Editar mesa' : 'Nueva mesa', `
    <div class="form-row">
      <div class="form-group">
        <label>Número de mesa</label>
        <input id="f-num" class="form-control" type="number" min="1" value="${m?.numero_mesa || ''}">
      </div>
      <div class="form-group">
        <label>Capacidad (personas)</label>
        <input id="f-cap" class="form-control" type="number" min="1" max="20" value="${m?.capacidad || ''}">
      </div>
    </div>
    <div class="form-group">
      <label>Ubicación</label>
      <input id="f-ubic" class="form-control" placeholder="ej: Interior, Terraza, Barra..." value="${m?.ubicacion || ''}">
    </div>
    <div class="form-group">
      <label>Estado</label>
      <select id="f-estado" class="form-control">
        ${['disponible','ocupada','reservada','mantenimiento'].map(e => `<option value="${e}" ${m?.estado===e?'selected':''}>${e.charAt(0).toUpperCase()+e.slice(1)}</option>`).join('')}
      </select>
    </div>`, async () => {
    const body = {
      numero_mesa: +document.getElementById('f-num').value,
      capacidad:   +document.getElementById('f-cap').value,
      ubicacion:    document.getElementById('f-ubic').value,
      estado:       document.getElementById('f-estado').value
    };
    if (editId) await api('mesas', 'PUT', body, { id: editId });
    else        await api('mesas', 'POST', body);
    toast(editId ? 'Mesa actualizada' : 'Mesa creada', 'success');
    loadMesas();
  });
};

window.deleteMesa = async id => {
  if (!confirm('¿Eliminar esta mesa?')) return;
  await api('mesas', 'DELETE', null, { id });
  toast('Mesa eliminada'); loadMesas();
};

/* ════════════════════════════════════════════════════════════
   CLIENTES
════════════════════════════════════════════════════════════ */
async function loadClientes() {
  const data = await api('clientes');
  cache.clientes = data;
  document.getElementById('tb-clientes').innerHTML = data.length
    ? data.map(c => `
      <tr>
        <td>${c.id_cliente}</td>
        <td><strong>${c.nombre} ${c.apellido}</strong></td>
        <td>${c.telefono || '—'}</td>
        <td>${c.email || '—'}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="clienteModal(${c.id_cliente})">Editar</button>
          <button class="btn btn-danger btn-sm" onclick="deleteCliente(${c.id_cliente})">Eliminar</button>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="5" class="empty"><p>No hay clientes registrados</p></td></tr>`;
}

window.clienteModal = (id = null) => {
  editId = id;
  const c = id ? cache.clientes.find(x => x.id_cliente == id) : null;
  openModal(id ? 'Editar cliente' : 'Nuevo cliente', `
    <div class="form-row">
      <div class="form-group"><label>Nombre</label><input id="f-nom" class="form-control" value="${c?.nombre||''}"></div>
      <div class="form-group"><label>Apellido</label><input id="f-ape" class="form-control" value="${c?.apellido||''}"></div>
    </div>
    <div class="form-group"><label>Teléfono</label><input id="f-tel" class="form-control" value="${c?.telefono||''}"></div>
    <div class="form-group"><label>Email</label><input id="f-email" class="form-control" type="email" value="${c?.email||''}"></div>
    `, async () => {
    const body = {
      nombre:   document.getElementById('f-nom').value,
      apellido: document.getElementById('f-ape').value,
      telefono: document.getElementById('f-tel').value,
      email:    document.getElementById('f-email').value
    };
    if (editId) await api('clientes', 'PUT', body, { id: editId });
    else        await api('clientes', 'POST', body);
    toast(editId ? 'Cliente actualizado' : 'Cliente creado', 'success');
    loadClientes();
  });
};

window.deleteCliente = async id => {
  if (!confirm('¿Eliminar este cliente?')) return;
  await api('clientes', 'DELETE', null, { id });
  toast('Cliente eliminado'); loadClientes();
};

/* ════════════════════════════════════════════════════════════
   MESEROS
════════════════════════════════════════════════════════════ */
async function loadMeseros() {
  const data = await api('meseros');
  cache.meseros = data;
  document.getElementById('tb-meseros').innerHTML = data.length
    ? data.map(m => `
      <tr>
        <td>${m.id_mesero}</td>
        <td><strong>${m.nombre} ${m.apellido}</strong></td>
        <td>${m.telefono || '—'}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="meseroModal(${m.id_mesero})">Editar</button>
          <button class="btn btn-danger btn-sm" onclick="deleteMesero(${m.id_mesero})">Eliminar</button>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="4" class="empty"><p>No hay meseros registrados</p></td></tr>`;
}

window.meseroModal = (id = null) => {
  editId = id;
  const m = id ? cache.meseros.find(x => x.id_mesero == id) : null;
  openModal(id ? 'Editar mesero' : 'Nuevo mesero', `
    <div class="form-row">
      <div class="form-group"><label>Nombre</label><input id="f-nom" class="form-control" value="${m?.nombre||''}"></div>
      <div class="form-group"><label>Apellido</label><input id="f-ape" class="form-control" value="${m?.apellido||''}"></div>
    </div>
    <div class="form-group"><label>Teléfono</label><input id="f-tel" class="form-control" value="${m?.telefono||''}"></div>
    `, async () => {
    const body = { nombre: document.getElementById('f-nom').value, apellido: document.getElementById('f-ape').value, telefono: document.getElementById('f-tel').value };
    if (editId) await api('meseros', 'PUT', body, { id: editId });
    else        await api('meseros', 'POST', body);
    toast(editId ? 'Mesero actualizado' : 'Mesero creado', 'success');
    loadMeseros();
  });
};

window.deleteMesero = async id => {
  if (!confirm('¿Eliminar este mesero?')) return;
  await api('meseros', 'DELETE', null, { id });
  toast('Mesero eliminado'); loadMeseros();
};

/* ════════════════════════════════════════════════════════════
   CARTA
════════════════════════════════════════════════════════════ */
async function loadCarta() {
  const [platos, cats] = await Promise.all([api('platos'), api('categorias')]);
  cache.platos = platos; cache.categorias = cats;
  document.getElementById('tb-platos').innerHTML = platos.length
    ? platos.map(p => `
      <tr>
        <td>${p.id_plato}</td>
        <td><strong>${p.nombre}</strong><br><small style="color:var(--muted)">${p.descripcion||''}</small></td>
        <td><span style="background:var(--surface3);padding:3px 9px;border-radius:4px;font-size:12px">${p.categoria_nombre}</span></td>
        <td style="font-family:var(--font-d);color:var(--accent)">${money(p.precio)}</td>
        <td>${p.disponible=='1'||p.disponible===true ? '<span style="color:#4ade80;font-size:18px">✓</span>' : '<span style="color:#f87171;font-size:18px">✗</span>'}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="platoModal(${p.id_plato})">Editar</button>
          <button class="btn btn-danger btn-sm" onclick="deletePlato(${p.id_plato})">Eliminar</button>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="6" class="empty"><p>No hay platos en la carta</p></td></tr>`;
}

window.categoriaModal = () => {
  openModal('Nueva categoría', `
    <div class="form-group"><label>Nombre</label><input id="f-nom" class="form-control" placeholder="ej: Entradas, Bebidas..."></div>
    <div class="form-group"><label>Descripción</label><input id="f-desc" class="form-control"></div>
    `, async () => {
    await api('categorias', 'POST', { nombre: document.getElementById('f-nom').value, descripcion: document.getElementById('f-desc').value });
    toast('Categoría creada', 'success'); loadCarta();
  });
};

window.platoModal = async (id = null) => {
  if (!cache.categorias.length) cache.categorias = await api('categorias');
  editId = id;
  const p = id ? cache.platos.find(x => x.id_plato == id) : null;
  openModal(id ? 'Editar plato' : 'Nuevo plato', `
    <div class="form-group"><label>Nombre del plato</label><input id="f-nom" class="form-control" value="${p?.nombre||''}"></div>
    <div class="form-group"><label>Descripción</label><textarea id="f-desc" class="form-control">${p?.descripcion||''}</textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Precio</label><input id="f-precio" class="form-control" type="number" step="0.01" min="0" value="${p?.precio||''}"></div>
      <div class="form-group"><label>Categoría</label>
        <select id="f-cat" class="form-control">
          ${cache.categorias.map(c=>`<option value="${c.id_categoria}" ${p?.id_categoria==c.id_categoria?'selected':''}>${c.nombre}</option>`).join('')}
        </select>
      </div>
    </div>
    <div class="form-group"><label>Disponible</label>
      <select id="f-disp" class="form-control">
        <option value="1" ${(p?.disponible=='1'||!p)?'selected':''}>Sí — disponible en carta</option>
        <option value="0" ${p?.disponible=='0'?'selected':''}>No — temporalmente no disponible</option>
      </select>
    </div>`, async () => {
    const body = { nombre: document.getElementById('f-nom').value, descripcion: document.getElementById('f-desc').value, precio: +document.getElementById('f-precio').value, id_categoria: +document.getElementById('f-cat').value, disponible: +document.getElementById('f-disp').value };
    if (editId) await api('platos', 'PUT', body, { id: editId });
    else        await api('platos', 'POST', body);
    toast(editId ? 'Plato actualizado' : 'Plato creado', 'success');
    loadCarta();
  });
};

window.deletePlato = async id => {
  if (!confirm('¿Eliminar este plato?')) return;
  await api('platos', 'DELETE', null, { id }); toast('Plato eliminado'); loadCarta();
};

/* ════════════════════════════════════════════════════════════
   RESERVAS
════════════════════════════════════════════════════════════ */
async function loadReservas() {
  const data = await api('reservas');
  document.getElementById('tb-reservas').innerHTML = data.length
    ? data.map(r => `
      <tr>
        <td>${r.id_reserva}</td>
        <td><strong>${r.cliente_nombre}</strong></td>
        <td>Mesa <strong>${r.numero_mesa}</strong></td>
        <td>${fmt(r.fecha_hora_inicio)}</td>
        <td>${fmt(r.fecha_hora_fin)}</td>
        <td>${r.num_personas}</td>
        <td>${badge(r.estado)}</td>
        <td>
          <button class="btn btn-ghost btn-sm" onclick="reservaModal(${r.id_reserva})">Editar</button>
          <button class="btn btn-danger btn-sm" onclick="deleteReserva(${r.id_reserva})">Eliminar</button>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="8" class="empty"><p>No hay reservas registradas</p></td></tr>`;
}

window.reservaModal = async (id = null) => {
  if (!cache.clientes.length) cache.clientes = await api('clientes');
  if (!cache.mesas.length)    cache.mesas    = await api('mesas');
  editId = id;
  let r = null;
  if (id) {
    const all = await api('reservas');
    r = all.find(x => x.id_reserva == id);
  }
  openModal(id ? 'Editar reserva' : 'Nueva reserva', `
    <div class="form-group"><label>Cliente</label>
      <select id="f-cli" class="form-control">
        ${cache.clientes.map(c=>`<option value="${c.id_cliente}" ${r?.id_cliente==c.id_cliente?'selected':''}>${c.nombre} ${c.apellido}</option>`).join('')}
      </select>
    </div>
    <div class="form-group"><label>Mesa</label>
      <select id="f-mesa" class="form-control">
        ${cache.mesas.map(m=>`<option value="${m.id_mesa}" ${r?.id_mesa==m.id_mesa?'selected':''}>Mesa ${m.numero_mesa} — ${m.ubicacion} (cap. ${m.capacidad})</option>`).join('')}
      </select>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Fecha y hora inicio</label><input id="f-ini" class="form-control" type="datetime-local" value="${r?.fecha_hora_inicio?.slice(0,16)||''}"></div>
      <div class="form-group"><label>Fecha y hora fin</label><input id="f-fin" class="form-control" type="datetime-local" value="${r?.fecha_hora_fin?.slice(0,16)||''}"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Número de personas</label><input id="f-per" class="form-control" type="number" min="1" value="${r?.num_personas||''}"></div>
      <div class="form-group"><label>Estado</label>
        <select id="f-estado" class="form-control">
          ${['pendiente','confirmada','cancelada','completada'].map(e=>`<option value="${e}" ${r?.estado===e?'selected':''}>${e.charAt(0).toUpperCase()+e.slice(1)}</option>`).join('')}
        </select>
      </div>
    </div>
    <div class="form-group"><label>Notas (opcional)</label><textarea id="f-notas" class="form-control">${r?.notas||''}</textarea></div>
    `, async () => {
    const body = {
      id_cliente: +document.getElementById('f-cli').value,
      id_mesa:    +document.getElementById('f-mesa').value,
      fecha_hora_inicio: document.getElementById('f-ini').value,
      fecha_hora_fin:    document.getElementById('f-fin').value,
      num_personas: +document.getElementById('f-per').value,
      estado:  document.getElementById('f-estado').value,
      notas:   document.getElementById('f-notas').value
    };
    const res = editId
      ? await api('reservas', 'PUT', body, { id: editId })
      : await api('reservas', 'POST', body);
    if (res.error) { toast(res.error, 'error'); throw new Error(res.error); }
    toast(editId ? 'Reserva actualizada' : 'Reserva creada', 'success');
    loadReservas();
  });
};

window.deleteReserva = async id => {
  if (!confirm('¿Eliminar esta reserva?')) return;
  await api('reservas', 'DELETE', null, { id }); toast('Reserva eliminada'); loadReservas();
};

/* ════════════════════════════════════════════════════════════
   ÓRDENES
════════════════════════════════════════════════════════════ */
async function loadOrdenes() {
  const data = await api('ordenes');
  document.getElementById('tb-ordenes').innerHTML = data.length
    ? data.map(o => `
      <tr>
        <td>#${o.id_orden}</td>
        <td>Mesa <strong>${o.numero_mesa}</strong></td>
        <td>${o.mesero_nombre || '—'}</td>
        <td>${fmt(o.fecha_hora)}</td>
        <td>${badge(o.estado)}</td>
        <td style="font-family:var(--font-d);color:var(--accent)">${money(o.total)}</td>
        <td style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
          <button class="btn btn-secondary btn-sm" onclick="verOrden(${o.id_orden})">Ver orden</button>
          <select class="form-control" style="padding:4px 8px;font-size:12px;width:auto" onchange="cambiarEstado(${o.id_orden},this.value)">
            ${['recibida','en_cocina','servida','pagada','cancelada'].map(e=>`<option value="${e}" ${o.estado===e?'selected':''}>${e.replace('_',' ')}</option>`).join('')}
          </select>
        </td>
      </tr>`).join('')
    : `<tr><td colspan="7" class="empty"><p>No hay órdenes registradas</p></td></tr>`;
}

window.ordenModal = async () => {
  if (!cache.mesas.length)   cache.mesas   = await api('mesas');
  if (!cache.meseros.length) cache.meseros = await api('meseros');
  openModal('Nueva orden', `
    <div class="form-group"><label>Mesa</label>
      <select id="f-mesa" class="form-control">
        ${cache.mesas.map(m=>`<option value="${m.id_mesa}">Mesa ${m.numero_mesa} — ${m.ubicacion}</option>`).join('')}
      </select>
    </div>
    <div class="form-group"><label>Mesero</label>
      <select id="f-mesero" class="form-control">
        <option value="">— Sin asignar —</option>
        ${cache.meseros.map(m=>`<option value="${m.id_mesero}">${m.nombre} ${m.apellido}</option>`).join('')}
      </select>
    </div>`, async () => {
    await api('ordenes', 'POST', { id_mesa: +document.getElementById('f-mesa').value, id_mesero: document.getElementById('f-mesero').value || null });
    toast('Orden creada', 'success'); loadOrdenes();
  });
};

window.cambiarEstado = async (id, estado) => {
  await api('ordenes', 'PUT', { estado }, { id, sub: 'estado' });
  toast('Estado actualizado');
};

window.verOrden = async id => {
  if (!cache.platos.length) cache.platos = await api('platos');
  const detalle = await api('ordenes', 'GET', null, { id, sub: 'detalle' });
  const total   = detalle.reduce((s, d) => s + d.cantidad * d.precio_unitario, 0);

  document.getElementById('orden-title').textContent = `Orden #${id}`;
  document.getElementById('orden-body').innerHTML = `
    <div class="order-items">
      ${detalle.length ? detalle.map(d => `
        <div class="order-item">
          <span class="name">${d.plato_nombre}</span>
          <span class="qty">×${d.cantidad}</span>
          <span class="price">${money(d.cantidad * d.precio_unitario)}</span>
          <button class="btn btn-danger btn-xs" onclick="quitarItem(${d.id_detalle},${id})">✕</button>
        </div>`).join('') : '<p style="color:var(--muted);text-align:center;padding:16px 0">Sin ítems aún</p>'}
    </div>
    <div class="order-total">Total de la orden: <strong>${money(total)}</strong></div>
    <hr class="divider" style="margin:16px 0">
    <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:12px;font-weight:600">Agregar ítem</p>
    <div class="form-row">
      <div class="form-group"><label>Plato</label>
        <select id="nuevo-plato" class="form-control">
          ${cache.platos.filter(p=>p.disponible=='1').map(p=>`<option value="${p.id_plato}">${p.nombre} — ${money(p.precio)}</option>`).join('')}
        </select>
      </div>
      <div class="form-group"><label>Cantidad</label>
        <input id="nuevo-cant" class="form-control" type="number" value="1" min="1">
      </div>
    </div>
    <button class="btn btn-primary" style="margin-top:4px" onclick="agregarItem(${id})">+ Agregar ítem</button>`;

  document.getElementById('orden-overlay').classList.add('open');
};

window.agregarItem = async id => {
  await api('ordenes', 'POST', { id_plato: +document.getElementById('nuevo-plato').value, cantidad: +document.getElementById('nuevo-cant').value }, { id, sub: 'detalle' });
  toast('Ítem agregado', 'success');
  verOrden(id); loadOrdenes();
};

window.quitarItem = async (did, oid) => {
  await api('detalle', 'DELETE', null, { id: did });
  toast('Ítem eliminado'); verOrden(oid); loadOrdenes();
};

/* ════════════════════════════════════════════════════════════
   REPORTES
════════════════════════════════════════════════════════════ */
async function loadReportes() {
  const [ocup, mesas] = await Promise.all([
    api('reportes', 'GET', null, { sub: 'ocupacion' }),
    api('reportes', 'GET', null, { sub: 'mesas' })
  ]);
  document.getElementById('tb-ocupacion').innerHTML = ocup.length
    ? ocup.map(r => `<tr><td>${r.dia}</td><td>${r.total_reservas}</td><td style="color:#4ade80">${r.completadas}</td><td style="color:#f87171">${r.canceladas}</td><td style="color:#fbbf24">${r.confirmadas}</td></tr>`).join('')
    : `<tr><td colspan="5" class="empty"><p>Sin datos</p></td></tr>`;
  document.getElementById('tb-mesas').innerHTML = mesas.length
    ? mesas.map((m,i) => `<tr><td>${i===0?'🥇':i===1?'🥈':i===2?'🥉':i+1}</td><td>Mesa <strong>${m.numero_mesa}</strong></td><td>${m.ubicacion}</td><td>${m.total_reservas}</td><td style="color:#a78bfa">${m.reservas_completadas}</td></tr>`).join('')
    : `<tr><td colspan="5" class="empty"><p>Sin datos</p></td></tr>`;
}

/* ── INIT ────────────────────────────────────────────────── */
loadDashboard();
