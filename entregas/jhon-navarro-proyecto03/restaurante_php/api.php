<?php
require_once __DIR__ . '/includes/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method   = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? '';
$id       = isset($_GET['id']) ? (int)$_GET['id'] : null;
$sub      = $_GET['sub'] ?? '';
$db       = getDB();

// ─── MESAS ────────────────────────────────────────────────
if ($resource === 'mesas') {
    if ($method === 'GET') {
        $r = $db->query("SELECT * FROM mesa ORDER BY numero_mesa");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST') {
        $d = getBody();
        $s = $db->prepare("INSERT INTO mesa (numero_mesa,capacidad,ubicacion,estado) VALUES (?,?,?,?)");
        $s->bind_param('iiss', $d['numero_mesa'], $d['capacidad'], $d['ubicacion'], $d['estado']);
        $s->execute();
        jsonResponse(['id' => $db->insert_id], 201);
    }
    if ($method === 'PUT' && $id) {
        $d = getBody();
        $s = $db->prepare("UPDATE mesa SET numero_mesa=?,capacidad=?,ubicacion=?,estado=? WHERE id_mesa=?");
        $s->bind_param('iissi', $d['numero_mesa'], $d['capacidad'], $d['ubicacion'], $d['estado'], $id);
        $s->execute();
        jsonResponse(['ok' => true]);
    }
    if ($method === 'DELETE' && $id) {
        $db->query("DELETE FROM mesa WHERE id_mesa=$id");
        jsonResponse(['ok' => true]);
    }
}

// ─── CLIENTES ─────────────────────────────────────────────
if ($resource === 'clientes') {
    if ($method === 'GET') {
        $r = $db->query("SELECT * FROM cliente ORDER BY apellido,nombre");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST') {
        $d = getBody();
        $s = $db->prepare("INSERT INTO cliente (nombre,apellido,telefono,email) VALUES (?,?,?,?)");
        $s->bind_param('ssss', $d['nombre'], $d['apellido'], $d['telefono'], $d['email']);
        $s->execute();
        jsonResponse(['id' => $db->insert_id], 201);
    }
    if ($method === 'PUT' && $id) {
        $d = getBody();
        $s = $db->prepare("UPDATE cliente SET nombre=?,apellido=?,telefono=?,email=? WHERE id_cliente=?");
        $s->bind_param('ssssi', $d['nombre'], $d['apellido'], $d['telefono'], $d['email'], $id);
        $s->execute();
        jsonResponse(['ok' => true]);
    }
    if ($method === 'DELETE' && $id) {
        $db->query("DELETE FROM cliente WHERE id_cliente=$id");
        jsonResponse(['ok' => true]);
    }
}

// ─── MESEROS ──────────────────────────────────────────────
if ($resource === 'meseros') {
    if ($method === 'GET') {
        $r = $db->query("SELECT * FROM mesero ORDER BY apellido,nombre");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST') {
        $d = getBody();
        $s = $db->prepare("INSERT INTO mesero (nombre,apellido,telefono) VALUES (?,?,?)");
        $s->bind_param('sss', $d['nombre'], $d['apellido'], $d['telefono']);
        $s->execute();
        jsonResponse(['id' => $db->insert_id], 201);
    }
    if ($method === 'PUT' && $id) {
        $d = getBody();
        $s = $db->prepare("UPDATE mesero SET nombre=?,apellido=?,telefono=? WHERE id_mesero=?");
        $s->bind_param('sssi', $d['nombre'], $d['apellido'], $d['telefono'], $id);
        $s->execute();
        jsonResponse(['ok' => true]);
    }
    if ($method === 'DELETE' && $id) {
        $db->query("DELETE FROM mesero WHERE id_mesero=$id");
        jsonResponse(['ok' => true]);
    }
}

// ─── CATEGORÍAS ───────────────────────────────────────────
if ($resource === 'categorias') {
    if ($method === 'GET') {
        $r = $db->query("SELECT * FROM categoria ORDER BY nombre");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST') {
        $d = getBody();
        $s = $db->prepare("INSERT INTO categoria (nombre,descripcion) VALUES (?,?)");
        $s->bind_param('ss', $d['nombre'], $d['descripcion']);
        $s->execute();
        jsonResponse(['id' => $db->insert_id], 201);
    }
    if ($method === 'DELETE' && $id) {
        $db->query("DELETE FROM categoria WHERE id_categoria=$id");
        jsonResponse(['ok' => true]);
    }
}

// ─── PLATOS ───────────────────────────────────────────────
if ($resource === 'platos') {
    if ($method === 'GET') {
        $r = $db->query("SELECT p.*,c.nombre AS categoria_nombre FROM plato p JOIN categoria c ON p.id_categoria=c.id_categoria ORDER BY c.nombre,p.nombre");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST') {
        $d = getBody();
        $disp = (int)($d['disponible'] ?? 1);
        $s = $db->prepare("INSERT INTO plato (nombre,descripcion,precio,disponible,id_categoria) VALUES (?,?,?,?,?)");
        $s->bind_param('ssdii', $d['nombre'], $d['descripcion'], $d['precio'], $disp, $d['id_categoria']);
        $s->execute();
        jsonResponse(['id' => $db->insert_id], 201);
    }
    if ($method === 'PUT' && $id) {
        $d = getBody();
        $disp = (int)($d['disponible'] ?? 1);
        $s = $db->prepare("UPDATE plato SET nombre=?,descripcion=?,precio=?,disponible=?,id_categoria=? WHERE id_plato=?");
        $s->bind_param('ssdiis', $d['nombre'], $d['descripcion'], $d['precio'], $disp, $d['id_categoria'], $id);
        $s->execute();
        jsonResponse(['ok' => true]);
    }
    if ($method === 'DELETE' && $id) {
        $db->query("DELETE FROM plato WHERE id_plato=$id");
        jsonResponse(['ok' => true]);
    }
}

// ─── RESERVAS ─────────────────────────────────────────────
if ($resource === 'reservas') {
    if ($method === 'GET') {
        $r = $db->query("SELECT r.*,CONCAT(c.nombre,' ',c.apellido) AS cliente_nombre,m.numero_mesa FROM reserva r JOIN cliente c ON r.id_cliente=c.id_cliente JOIN mesa m ON r.id_mesa=m.id_mesa ORDER BY r.fecha_hora_inicio DESC");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST') {
        $d = getBody();
        $notas = $d['notas'] ?? '';
        $estado = $d['estado'] ?? 'pendiente';
        $s = $db->prepare("INSERT INTO reserva (fecha_hora_inicio,fecha_hora_fin,num_personas,estado,notas,id_cliente,id_mesa) VALUES (?,?,?,?,?,?,?)");
        $s->bind_param('ssissii', $d['fecha_hora_inicio'], $d['fecha_hora_fin'], $d['num_personas'], $estado, $notas, $d['id_cliente'], $d['id_mesa']);
        if (!$s->execute()) jsonResponse(['error' => $db->error], 400);
        jsonResponse(['id' => $db->insert_id], 201);
    }
    if ($method === 'PUT' && $id) {
        $d = getBody();
        $notas = $d['notas'] ?? '';
        $s = $db->prepare("UPDATE reserva SET fecha_hora_inicio=?,fecha_hora_fin=?,num_personas=?,estado=?,notas=?,id_cliente=?,id_mesa=? WHERE id_reserva=?");
        $s->bind_param('ssissiii', $d['fecha_hora_inicio'], $d['fecha_hora_fin'], $d['num_personas'], $d['estado'], $notas, $d['id_cliente'], $d['id_mesa'], $id);
        if (!$s->execute()) jsonResponse(['error' => $db->error], 400);
        jsonResponse(['ok' => true]);
    }
    if ($method === 'DELETE' && $id) {
        $db->query("DELETE FROM reserva WHERE id_reserva=$id");
        jsonResponse(['ok' => true]);
    }
}

// ─── ÓRDENES ──────────────────────────────────────────────
if ($resource === 'ordenes') {
    if ($method === 'GET' && !$id) {
        $r = $db->query("SELECT o.*,m.numero_mesa,CONCAT(ms.nombre,' ',ms.apellido) AS mesero_nombre FROM orden o JOIN mesa m ON o.id_mesa=m.id_mesa LEFT JOIN mesero ms ON o.id_mesero=ms.id_mesero ORDER BY o.fecha_hora DESC");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST' && !$id) {
        $d = getBody();
        $idRes  = $d['id_reserva']  ? (int)$d['id_reserva']  : null;
        $idMes  = $d['id_mesero']   ? (int)$d['id_mesero']   : null;
        $estado = $d['estado'] ?? 'recibida';
        $s = $db->prepare("INSERT INTO orden (id_mesa,id_reserva,id_mesero,estado) VALUES (?,?,?,?)");
        $s->bind_param('iiis', $d['id_mesa'], $idRes, $idMes, $estado);
        $s->execute();
        jsonResponse(['id' => $db->insert_id], 201);
    }
    if ($method === 'PUT' && $id && $sub === 'estado') {
        $d = getBody();
        $s = $db->prepare("UPDATE orden SET estado=? WHERE id_orden=?");
        $s->bind_param('si', $d['estado'], $id);
        $s->execute();
        jsonResponse(['ok' => true]);
    }
    if ($method === 'GET' && $id && $sub === 'detalle') {
        $r = $db->query("SELECT d.*,p.nombre AS plato_nombre FROM detalle_orden d JOIN plato p ON d.id_plato=p.id_plato WHERE d.id_orden=$id");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($method === 'POST' && $id && $sub === 'detalle') {
        $d   = getBody();
        $row = $db->query("SELECT precio FROM plato WHERE id_plato={$d['id_plato']}")->fetch_assoc();
        $precio = $row['precio'];
        $notas  = $d['notas'] ?? '';
        $s = $db->prepare("INSERT INTO detalle_orden (id_orden,id_plato,cantidad,precio_unitario,notas) VALUES (?,?,?,?,?)");
        $s->bind_param('iiids', $id, $d['id_plato'], $d['cantidad'], $precio, $notas);
        $s->execute();
        jsonResponse(['ok' => true], 201);
    }
}

// ─── DETALLE (eliminar) ───────────────────────────────────
if ($resource === 'detalle' && $method === 'DELETE' && $id) {
    $db->query("DELETE FROM detalle_orden WHERE id_detalle=$id");
    jsonResponse(['ok' => true]);
}

// ─── REPORTES ─────────────────────────────────────────────
if ($resource === 'reportes') {
    if ($sub === 'ocupacion') {
        $r = $db->query("SELECT * FROM vw_ocupacion_por_dia LIMIT 30");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
    if ($sub === 'mesas') {
        $r = $db->query("SELECT * FROM vw_mesas_mas_reservadas");
        jsonResponse($r->fetch_all(MYSQLI_ASSOC));
    }
}

// ─── DASHBOARD ────────────────────────────────────────────
if ($resource === 'dashboard') {
    $mesas     = $db->query("SELECT COUNT(*) AS n FROM mesa")->fetch_assoc()['n'];
    $clientes  = $db->query("SELECT COUNT(*) AS n FROM cliente")->fetch_assoc()['n'];
    $reservasH = $db->query("SELECT COUNT(*) AS n FROM reserva WHERE DATE(fecha_hora_inicio)=CURDATE()")->fetch_assoc()['n'];
    $ordenesA  = $db->query("SELECT COUNT(*) AS n FROM orden WHERE estado NOT IN ('pagada','cancelada')")->fetch_assoc()['n'];
    $recientes = $db->query("SELECT r.*,CONCAT(c.nombre,' ',c.apellido) AS cliente_nombre,m.numero_mesa FROM reserva r JOIN cliente c ON r.id_cliente=c.id_cliente JOIN mesa m ON r.id_mesa=m.id_mesa ORDER BY r.fecha_hora_inicio DESC LIMIT 8");
    jsonResponse([
        'mesas'     => $mesas,
        'clientes'  => $clientes,
        'reservasH' => $reservasH,
        'ordenesA'  => $ordenesA,
        'recientes' => $recientes->fetch_all(MYSQLI_ASSOC),
    ]);
}

http_response_code(404);
echo json_encode(['error' => 'Recurso no encontrado']);
