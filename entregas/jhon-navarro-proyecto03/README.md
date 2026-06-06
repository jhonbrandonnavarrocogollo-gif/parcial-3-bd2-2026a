# RestaurantePro — Sistema de Gestión de Reservas

Aplicativo web completo para la gestión de reservas, mesas, clientes, menú y órdenes de un restaurante. Desarrollado con **PHP 8 puro**, **MySQL**, **Bootstrap 5** y arquitectura **MVC**.

---

## Requisitos

| Componente | Versión mínima |
|---|---|
| PHP | 8.0+ |
| MySQL / MariaDB | 5.7+ / 10.4+ |
| Apache (XAMPP) | 8.0+ |
| Navegador moderno | Chrome, Firefox, Edge |

Extensiones PHP requeridas: `pdo`, `pdo_mysql`.

---

## Instalación en XAMPP

### 1. Copiar el proyecto

Copia la carpeta del proyecto dentro de `htdocs`:

```
C:\xampp\htdocs\jhon-navarro-proyecto03\
```

### 2. Iniciar servicios

Abre el **Panel de Control de XAMPP** e inicia:
- **Apache**
- **MySQL**

### 3. Importar la base de datos

1. Abre [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Crea la base de datos `reservas_restaurante` (o importa directamente el archivo SQL)
3. Ve a la pestaña **Importar**
4. Selecciona el archivo `database.sql` del proyecto
5. Haz clic en **Continuar**

El script crea todas las tablas y carga datos de ejemplo para pruebas.

### 4. Configurar la conexión

El archivo `config/database.php` ya viene configurado para XAMPP por defecto:

```php
host     = localhost
database = reservas_restaurante
user     = root
password = (vacío)
```

Si tu configuración es diferente, edita las constantes `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS`.

### 5. Ejecutar el proyecto

Abre en el navegador:

```
http://localhost/jhon-navarro-proyecto03/
```

---

## Estructura de Carpetas

```
jhon-navarro-proyecto03/
├── config/
│   └── database.php          # Conexión PDO a MySQL
├── controllers/
│   ├── ClienteController.php
│   ├── MesaController.php
│   ├── ReservaController.php
│   ├── CategoriaController.php
│   ├── PlatoController.php
│   ├── OrdenController.php
│   ├── MeseroController.php
│   └── ReporteController.php
├── models/
│   ├── Cliente.php
│   ├── Mesa.php
│   ├── Reserva.php
│   ├── Categoria.php
│   ├── Plato.php
│   ├── Orden.php
│   ├── DetalleOrden.php
│   └── Mesero.php
├── views/
│   ├── layout/               # Header y footer compartidos
│   ├── dashboard/
│   ├── clientes/
│   ├── mesas/
│   ├── reservas/
│   ├── categorias/
│   ├── platos/
│   ├── ordenes/
│   ├── meseros/
│   └── reportes/
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── index.php                 # Front controller
├── database.sql              # Script de base de datos
└── README.md
```

---

## Módulos del Sistema

| Módulo | Funcionalidades |
|---|---|
| **Dashboard** | Estadísticas generales, gráficos de reservas, ventas y mesas |
| **Clientes** | CRUD con validación de email y duplicados |
| **Mesas** | CRUD con estados: disponible, ocupada, reservada, mantenimiento |
| **Reservas** | CRUD, calendario semanal, disponibilidad, validación de conflictos |
| **Categorías** | CRUD de categorías del menú |
| **Platos & Bebidas** | Catálogo visual con filtros por categoría |
| **Órdenes** | Creación con productos dinámicos, cálculo de totales, estados |
| **Meseros** | CRUD del personal de servicio |
| **Reportes** | 4 reportes con gráficos Chart.js |

---

## Validación de Conflictos de Reservas

Al crear o editar una reserva, el sistema verifica automáticamente que la mesa no tenga otra reserva activa en el mismo horario:

- **Backend:** método `tieneConflicto()` en el modelo `Reserva`
- **Frontend:** verificación AJAX en tiempo real al cambiar mesa u horario

Mensaje mostrado: *"Ya existe una reserva para esa mesa en ese horario."*

---

## Estados de Órdenes

| Estado | Color |
|---|---|
| Recibida | Azul |
| En cocina | Amarillo |
| Servida | Índigo |
| Pagada | Verde |
| Cancelada | Rojo |

---

## Reportes Disponibles

1. **Ocupación diaria** — Reservas y mesas utilizadas por fecha
2. **Mesas más reservadas** — Ranking descendente
3. **Ventas por fecha** — Total vendido por día
4. **Productos más vendidos** — Cantidad e ingresos por producto

Todos incluyen gráficos interactivos con **Chart.js**.

---

## Seguridad Implementada

- Conexión **PDO** con modo de excepciones
- **Prepared Statements** en todas las consultas
- Sanitización de entradas con `htmlspecialchars()` y `trim()`
- Validación de formularios en servidor
- Protección contra inyección SQL
- Filtrado de parámetros GET en el front controller

---

## Capturas Sugeridas para la Entrega

1. Dashboard con tarjetas estadísticas y gráficos
2. Listado de mesas con estados y paginación
3. Formulario de nueva reserva con alerta de conflicto
4. Calendario de reservas y disponibilidad de mesas
5. Catálogo visual de platos con filtros
6. Formulario de orden con productos dinámicos
7. Detalle de orden con cambio de estado
8. Módulo de reportes con gráficos

---

## Tecnologías Utilizadas

- PHP 8 (sin frameworks)
- MySQL con PDO
- HTML5 / CSS3
- JavaScript (vanilla)
- Bootstrap 5.3
- Bootstrap Icons 1.11
- Chart.js 4.4
- Google Fonts (Inter)

---

## Autor

**Jhon Navarro** — Proyecto 03 — Base de Datos II — 2026A
