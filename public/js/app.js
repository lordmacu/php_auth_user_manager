/**
 * App.js - Utilidades comunes y configuración
 */

// Configuración de la API
const API_URL = '/api';

/**
 * Verificar si el usuario está autenticado
 */
function isAuthenticated() {
    return localStorage.getItem('token') !== null;
}

/**
 * Obtener el token del localStorage
 */
function getToken() {
    return localStorage.getItem('token');
}

/**
 * Obtener los datos del usuario del localStorage
 */
function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

/**
 * Cerrar sesión
 */
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/';
}

/**
 * Verificar si el usuario es SuperAdmin
 */
function isSuperAdmin() {
    const user = getUser();
    return user && user.role_name === 'SuperAdmin';
}

/**
 * Verificar si el usuario es Admin
 */
function isAdmin() {
    const user = getUser();
    return user && (user.role_name === 'Admin' || user.role_name === 'SuperAdmin');
}

/**
 * Hacer petición a la API con autenticación
 */
async function fetchAPI(endpoint, options = {}) {
    const token = getToken();
    
    // Configuración por defecto
    const config = {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        ...options
    };
    
    // Si hay un body y no es FormData, convertirlo a JSON
    if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
        config.body = JSON.stringify(config.body);
    }
    
    try {
        const response = await fetch(`${API_URL}${endpoint}`, config);
        const data = await response.json();
        
        // Si el token expiró, cerrar sesión
        if (response.status === 401) {
            logout();
            return null;
        }
        
        return {
            ok: response.ok,
            status: response.status,
            data: data
        };
        
    } catch (error) {
        console.error('Error en fetchAPI:', error);
        return {
            ok: false,
            status: 500,
            data: { error: 'Error de conexión' }
        };
    }
}

/**
 * Mostrar alerta de éxito con SweetAlert (si quieres usar, sino Toast de Bootstrap)
 */
function showSuccess(message) {
    // Usando Bootstrap Toast
    showToast(message, 'success');
}

/**
 * Mostrar alerta de error
 */
function showError(message) {
    showToast(message, 'danger');
}

/**
 * Mostrar Toast de Bootstrap
 */
function showToast(message, type = 'info') {
    // Crear toast dinámicamente
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : 
                    type === 'danger' ? 'bg-danger' : 
                    type === 'warning' ? 'bg-warning' : 'bg-info';
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    // Remover del DOM después de ocultarse
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

/**
 * Crear contenedor de toasts si no existe
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

/**
 * Confirmar acción (usando confirm nativo, puedes cambiar a SweetAlert)
 */
function confirmAction(message) {
    return confirm(message);
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    return date.toLocaleDateString('es-ES', options);
}

/**
 * Escapar HTML para prevenir XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Validar email
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Deshabilitar botón con loading
 */
function setButtonLoading(button, loading = true) {
    if (loading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || button.innerHTML;
    }
}

/**
 * Proteger ruta - redirigir a login si no está autenticado
 */
function protectRoute() {
    if (!isAuthenticated()) {
        window.location.href = '/login';
    }
}