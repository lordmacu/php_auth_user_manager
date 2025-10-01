<?php

namespace Core;

/**
 * View - Helper para renderizar vistas HTML
 */
class View
{
    /**
     * Renderizar una vista HTML
     */
    public static function render(string $view_name): void
    {
        // Remover extensión si la tiene
        $view_name = str_replace('.html', '', $view_name);
        
        // Construir ruta del archivo
        $file_path = __DIR__ . '/../../public/' . $view_name . '.html';
        
        if (!file_exists($file_path)) {
            http_response_code(404);
            echo "Vista no encontrada: {$view_name}.html";
            exit;
        }
        
        // Cambiar header a HTML
        header('Content-Type: text/html; charset=UTF-8');
        
        // Renderizar vista
        readfile($file_path);
        exit;
    }
}