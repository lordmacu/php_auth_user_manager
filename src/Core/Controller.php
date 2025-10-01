<?php

namespace Core;

/**
 * Controller - Controlador base
 * 
 * Clase base para todos los controladores
 * Proporciona acceso común a Request y Response
 */
class Controller
{
    protected Request $request;
    
    public function __construct()
    {
        $this->request = new Request();
    }
}