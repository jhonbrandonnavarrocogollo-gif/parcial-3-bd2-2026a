<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Categoria.php';

class MenuController {
    private Plato $platoModel;
    private Categoria $catModel;

    public function __construct() {
        Auth::requireRole('cliente');
        $this->platoModel = new Plato();
        $this->catModel   = new Categoria();
    }

    public function index(): void {
        $search  = trim($_GET['search'] ?? '');
        $catId   = (int)($_GET['categoria'] ?? 0);
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $limit   = 12;
        $offset  = ($page - 1) * $limit;
        $platos  = $this->platoModel->getDisponibles($limit, $offset, $search, $catId);
        $total   = $this->platoModel->countDisponibles($search, $catId);
        $pages   = ceil($total / $limit);
        $categorias = $this->catModel->getAllSimple();
        require __DIR__ . '/../views/menu/index.php';
    }
}
