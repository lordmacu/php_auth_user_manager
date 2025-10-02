<?php

namespace Presentation\Controllers;

use Core\Controller;
use Core\Response;
use Application\Auth\AuthService;
use Infrastructure\Repositories\UserRepository;

/**
 * AuthController - Controlador de autenticación
 * 
 * Maneja las peticiones relacionadas con login
 */
class AuthController extends Controller
{
    private AuthService $auth_service;

    public function __construct()
    {
        parent::__construct();

        $user_repository = new UserRepository();
        $this->auth_service = new AuthService($user_repository);
    }

    /**
     * Login - Autenticar usuario
     * POST /api/login
     */
    public function login(): void
    {
        $email = $this->request->get('email');
        $password = $this->request->get('password');

        if (empty($email) || empty($password)) {
            Response::badRequest(['El email y contraseña son requeridos']);
        }

        $result = $this->auth_service->login($email, $password);

        if (!$result) {
            Response::unauthorized('Credenciales inválidas');
        }

        Response::success([
            'token' => $result['token'],
            'user' => $result['user']
        ]);
    }
}
