<?php

/**
 * Configuración global de la aplicación
 */

// Configuración JWT
define('JWT_SECRET', env('JWT_SECRET', 'change_this_secret_key'));
define('JWT_EXPIRATION', 3600); // 1 hora

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 6);

// Roles del sistema
define('ROLE_SUPER_ADMIN', 'SuperAdmin');
define('ROLE_ADMIN', 'Admin');
define('ROLE_USER', 'User');