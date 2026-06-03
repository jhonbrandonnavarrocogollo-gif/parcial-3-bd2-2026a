# Sistema de Reservas de Restaurante — PHP + MariaDB

## Requisitos
- XAMPP con Apache y MariaDB corriendo
- PHP 7.4 o superior (incluido en XAMPP)
- Base de datos `reservas_restaurante` con las tablas creadas

## Instalación

1. **Copia la carpeta** `restaurante_php` dentro de:
   ```
   C:\xampp\htdocs\restaurante_php
   ```

2. **Configura la base de datos** en `includes/db.php`:
   ```php
   define('DB_PASS', '');  // Cambia si tienes contraseña en XAMPP
   ```

3. **Abre en el navegador**:
   ```
   http://localhost/restaurante_php/
   ```

## Estructura
```
restaurante_php/
├── index.php          ← Página principal
├── api.php            ← Backend: maneja todas las peticiones
├── includes/
│   └── db.php         ← Configuración de la base de datos
└── assets/
    ├── css/style.css  ← Estilos
    └── js/app.js      ← Lógica del frontend
```

## Funcionalidades
- Dashboard con estadísticas en tiempo real
- Gestión de mesas, clientes y meseros
- Reservas con validación de conflicto de horario
- Órdenes con ítems de la carta y cálculo automático de total
- Carta del restaurante con categorías y precios
- Reportes de ocupación y mesas más reservadas
