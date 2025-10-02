<?php

namespace Presentation\Controllers;

use Core\View;

/**
 * PageController - Controlador de páginas HTML
 * 
 * Renderiza las vistas del frontend
 */
class PageController
{
    /**
     * Mostrar página de login
     * GET /
     * GET /login
     */
    public function login(): void
    {
        View::render('/views/login.html');
    }
    
    /**
     * Mostrar dashboard (requiere estar logueado)
     * GET /dashboard
     */
    public function dashboard(): void
    {
        View::render('/views/dashboard.html');
    }
    
 
}