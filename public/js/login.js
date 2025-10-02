/**
 * Login - Manejo de autenticación
 */

// Constantes
const API_URL = '/api';

// Elementos del DOM
const loginForm = document.getElementById('login-form');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const btnLogin = document.getElementById('btn-login');
const alertError = document.getElementById('alert-error');
const errorMessage = document.getElementById('error-message');

// Event Listeners
loginForm.addEventListener('submit', handleLogin);

// Verificar si ya está logueado
window.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    if (token) {
        window.location.href = '/dashboard';
    }
});

/**
 * Manejar el login
 */
async function handleLogin(e) {
    e.preventDefault();
    
    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();
    
    // Validaciones básicas
    if (!email || !password) {
        showError('Por favor completa todos los campos');
        return;
    }
    
    // Deshabilitar botón y mostrar loading
    btnLogin.disabled = true;
    btnLogin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
    hideError();
    
    try {
        // Hacer petición al API
        const response = await fetch(`${API_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Login exitoso
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            
            // Redirigir al dashboard
            window.location.href = '/dashboard';
        } else {
            // Error en el login
            showError(data.error || 'Credenciales inválidas');
        }
        
    } catch (error) {
        showError('Error de conexión. Por favor intenta nuevamente.');
        console.error('Error:', error);
    } finally {
        // Restaurar botón
        btnLogin.disabled = false;
        btnLogin.innerHTML = '<i class="fas fa-sign-in-alt"></i> Iniciar Sesión';
    }
}

/**
 * Mostrar error
 */
function showError(message) {
    errorMessage.textContent = message;
    alertError.classList.remove('d-none');
}

/**
 * Ocultar error
 */
function hideError() {
    alertError.classList.add('d-none');
}