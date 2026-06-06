/**
 * RestaurantePro — App JavaScript
 */
(function () {
    'use strict';

    // ─── Sidebar toggle (mobile) ───────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('sidebarToggle');

    if (toggle && sidebar) {
        let overlay = document.querySelector('.sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        }

        toggle.addEventListener('click', function () {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });

        overlay.addEventListener('click', closeSidebar);

        window.addEventListener('resize', function () {
            if (window.innerWidth > 991) closeSidebar();
        });
    }

    // ─── Auto-dismiss alerts after 5s ────────────────────────
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });
})();

/**
 * Confirmar eliminación
 */
function confirmDelete(url) {
    if (confirm('¿Está seguro de que desea eliminar este registro? Esta acción no se puede deshacer.')) {
        window.location.href = url;
    }
}

/**
 * Confirmar acción genérica (cancelar reserva, etc.)
 */
function confirmAction(url, message) {
    if (confirm(message || '¿Está seguro de realizar esta acción?')) {
        window.location.href = url;
    }
}
