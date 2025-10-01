protectRoute();

// Variables globales
let currentUser = null;
let allRoles = [];
let allPermissions = [];
let userModal = null;
let roleModal = null;

// Elementos del DOM
const userName = document.getElementById('user-name');
const userRole = document.getElementById('user-role');
const btnLogout = document.getElementById('btn-logout');
const rolesTabBtn = document.getElementById('roles-tab-btn');
const btnNewUser = document.getElementById('btn-new-user');
const btnNewRole = document.getElementById('btn-new-role');

// Event Listeners
document.addEventListener('DOMContentLoaded', init);

btnLogout.addEventListener('click', logout);
btnNewUser.addEventListener('click', () => openUserModal());
btnNewRole.addEventListener('click', () => openRoleModal());
document.getElementById('btn-save-user').addEventListener('click', saveUser);
document.getElementById('btn-save-role').addEventListener('click', saveRole);

// Event listener para cambio de tabs
document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', (e) => {
        const target = e.target.getAttribute('data-bs-target');
        if (target === '#roles-tab') {
            loadRolesSection();
        }
    });
});

/**
 * Inicializar dashboard
 */
async function init() {
    currentUser = getUser();
    
    if (!currentUser) {
        logout();
        return;
    }
    
    userName.textContent = currentUser.name;
    userRole.textContent = currentUser.role_name;
    
    // Mostrar tab de roles si es SuperAdmin
    if (isSuperAdmin()) {
        rolesTabBtn.style.display = 'block';
    }
    
    // Mostrar botón crear usuario según permisos
    if (isAdmin() || isSuperAdmin()) {
        btnNewUser.style.display = 'block';
    }
    
    // Inicializar modales
    userModal = new bootstrap.Modal(document.getElementById('userModal'));
    roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
    
    // Cargar datos iniciales
    await loadRoles();
    loadUsers();
}

/**
 * Mostrar sección específica
 */
function showSection(section) {
    // Ocultar todas las secciones
    document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
    
    // Remover active de todos los links
    document.querySelectorAll('[data-section]').forEach(link => {
        link.classList.remove('active');
    });
    
    // Mostrar sección seleccionada
    document.getElementById(`section-${section}`).style.display = 'block';
    
    // Marcar link como activo
    document.querySelector(`[data-section="${section}"]`).classList.add('active');
    
    // Cargar datos según la sección
    if (section === 'users') {
        loadUsers();
    } else if (section === 'roles') {
        loadRolesSection();
    }
}

// ============================================
// USUARIOS
// ============================================

/**
 * Cargar lista de usuarios
 */
async function loadUsers() {
    const loadingUsers = document.getElementById('loading-users');
    const usersTableContainer = document.getElementById('users-table-container');
    const usersTbody = document.getElementById('users-tbody');
    
    loadingUsers.style.display = 'block';
    usersTableContainer.style.display = 'none';
    
    const result = await fetchAPI('/users');
    
    if (result.ok) {
        const users = result.data.users;
        
        usersTbody.innerHTML = '';
        
        users.forEach(user => {
            const row = createUserRow(user);
            usersTbody.innerHTML += row;
        });
        
        loadingUsers.style.display = 'none';
        usersTableContainer.style.display = 'block';
        
    } else {
        showError('Error al cargar usuarios');
        loadingUsers.style.display = 'none';
    }
}

/**
 * Crear fila de usuario para la tabla
 */
function createUserRow(user) {
    const canEdit = isAdmin() || isSuperAdmin() || currentUser.user_id === user.id;
    const canDelete = (isAdmin() || isSuperAdmin()) && currentUser.user_id !== user.id;
    
    return `
        <tr>
            <td>${user.id}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td><span class="badge bg-primary">${escapeHtml(user.role_name)}</span></td>
            <td class="table-actions">
                ${canEdit ? `
                    <button class="btn btn-sm btn-warning" onclick="editUser(${user.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                ` : ''}
                ${canDelete ? `
                    <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                ` : ''}
            </td>
        </tr>
    `;
}

/**
 * Abrir modal de usuario (crear/editar)
 */
async function openUserModal(userId = null) {
    const modalTitle = document.getElementById('userModalTitle');
    const userIdInput = document.getElementById('user-id');
    const userNameInput = document.getElementById('user-name-input');
    const userEmailInput = document.getElementById('user-email-input');
    const userPasswordInput = document.getElementById('user-password-input');
    const userRoleSelect = document.getElementById('user-role-select');
    const formErrors = document.getElementById('user-form-errors');
    
    // Limpiar formulario
    userIdInput.value = '';
    userNameInput.value = '';
    userEmailInput.value = '';
    userPasswordInput.value = '';
    userPasswordInput.required = true;
    formErrors.classList.add('d-none');
    
    // Llenar select de roles
    userRoleSelect.innerHTML = '';
    allRoles.forEach(role => {
        userRoleSelect.innerHTML += `<option value="${role.id}">${role.role_name}</option>`;
    });
    
    if (userId) {
        // Modo edición
        modalTitle.textContent = 'Editar Usuario';
        userPasswordInput.required = false;
        
        // Cargar datos del usuario
        const result = await fetchAPI(`/users/${userId}`);
        
        if (result.ok) {
            const user = result.data.user;
            userIdInput.value = user.id;
            userNameInput.value = user.name;
            userEmailInput.value = user.email;
            userRoleSelect.value = user.role_id;
        }
    } else {
        // Modo creación
        modalTitle.textContent = 'Nuevo Usuario';
        userPasswordInput.required = true;
    }
    
    userModal.show();
}

/**
 * Guardar usuario (crear o actualizar)
 */
async function saveUser() {
    const userId = document.getElementById('user-id').value;
    const name = document.getElementById('user-name-input').value.trim();
    const email = document.getElementById('user-email-input').value.trim();
    const password = document.getElementById('user-password-input').value;
    const roleId = document.getElementById('user-role-select').value;
    const btnSave = document.getElementById('btn-save-user');
    const formErrors = document.getElementById('user-form-errors');
    
    // Validaciones básicas
    if (!name || !email || !roleId) {
        formErrors.textContent = 'Por favor completa todos los campos requeridos';
        formErrors.classList.remove('d-none');
        return;
    }
    
    if (!userId && !password) {
        formErrors.textContent = 'La contraseña es requerida';
        formErrors.classList.remove('d-none');
        return;
    }
    
    if (!isValidEmail(email)) {
        formErrors.textContent = 'Email inválido';
        formErrors.classList.remove('d-none');
        return;
    }
    
    formErrors.classList.add('d-none');
    setButtonLoading(btnSave, true);
    
    const data = {
        name,
        email,
        role_id: parseInt(roleId)
    };
    
    if (password) {
        data.password = password;
    }
    
    let result;
    
    if (userId) {
        // Actualizar
        result = await fetchAPI(`/users/${userId}`, {
            method: 'PUT',
            body: data
        });
    } else {
        // Crear
        result = await fetchAPI('/users', {
            method: 'POST',
            body: data
        });
    }
    
    setButtonLoading(btnSave, false);
    
    if (result.ok) {
        userModal.hide();
        showSuccess(userId ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente');
        loadUsers();
    } else {
        const errors = result.data.errors || [result.data.error || 'Error al guardar usuario'];
        formErrors.innerHTML = errors.join('<br>');
        formErrors.classList.remove('d-none');
    }
}

/**
 * Editar usuario
 */
window.editUser = function(userId) {
    openUserModal(userId);
};

/**
 * Eliminar usuario
 */
window.deleteUser = async function(userId) {
    if (!confirmAction('¿Estás seguro de eliminar este usuario?')) {
        return;
    }
    
    const result = await fetchAPI(`/users/${userId}`, {
        method: 'DELETE'
    });
    
    if (result.ok) {
        showSuccess('Usuario eliminado exitosamente');
        loadUsers();
    } else {
        showError(result.data.error || 'Error al eliminar usuario');
    }
};

// ============================================
// ROLES
// ============================================

/**
 * Cargar roles para el select
 */
async function loadRoles() {
    const result = await fetchAPI('/roles');
    
    if (result.ok) {
        allRoles = result.data.roles;
    }
}

/**
 * Cargar sección de roles
 */
async function loadRolesSection() {
    const loadingRoles = document.getElementById('loading-roles');
    const rolesTableContainer = document.getElementById('roles-table-container');
    const rolesTbody = document.getElementById('roles-tbody');
    
    loadingRoles.style.display = 'block';
    rolesTableContainer.style.display = 'none';
    
    // Cargar permisos disponibles
    await loadPermissions();
    
    const result = await fetchAPI('/roles');
    
    if (result.ok) {
        const roles = result.data.roles;
        
        rolesTbody.innerHTML = '';
        
        roles.forEach(role => {
            const row = createRoleRow(role);
            rolesTbody.innerHTML += row;
        });
        
        loadingRoles.style.display = 'none';
        rolesTableContainer.style.display = 'block';
        
    } else {
        showError('Error al cargar roles');
        loadingRoles.style.display = 'none';
    }
}

/**
 * Crear fila de rol para la tabla
 */
function createRoleRow(role) {
    const permissionsList = role.permissions.map(p => p.permission_name).join(', ') || 'Sin permisos';
    
    return `
        <tr>
            <td>${role.id}</td>
            <td><strong>${escapeHtml(role.role_name)}</strong></td>
            <td><small>${escapeHtml(permissionsList)}</small></td>
            <td class="table-actions">
                <button class="btn btn-sm btn-warning" onclick="editRole(${role.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteRole(${role.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

/**
 * Cargar permisos disponibles
 */
async function loadPermissions() {
    const result = await fetchAPI('/permissions');
    
    if (result.ok) {
        allPermissions = result.data.permissions;
    }
}

/**
 * Abrir modal de rol (crear/editar)
 */
async function openRoleModal(roleId = null) {
    const modalTitle = document.getElementById('roleModalTitle');
    const roleIdInput = document.getElementById('role-id');
    const roleNameInput = document.getElementById('role-name-input');
    const permissionsContainer = document.getElementById('permissions-container');
    const formErrors = document.getElementById('role-form-errors');
    
    // Limpiar formulario
    roleIdInput.value = '';
    roleNameInput.value = '';
    permissionsContainer.innerHTML = '';
    formErrors.classList.add('d-none');
    
    // Crear checkboxes de permisos
    allPermissions.forEach(permission => {
        permissionsContainer.innerHTML += `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${permission.id}" 
                       id="permission-${permission.id}">
                <label class="form-check-label" for="permission-${permission.id}">
                    ${permission.permission_name}
                </label>
            </div>
        `;
    });
    
    if (roleId) {
        // Modo edición
        modalTitle.textContent = 'Editar Rol';
        
        // Cargar datos del rol
        const result = await fetchAPI(`/roles/${roleId}`);
        
        if (result.ok) {
            const role = result.data.role;
            roleIdInput.value = role.id;
            roleNameInput.value = role.role_name;
            
            // Marcar permisos actuales
            role.permissions.forEach(permission => {
                const checkbox = document.getElementById(`permission-${permission.id}`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    } else {
        // Modo creación
        modalTitle.textContent = 'Nuevo Rol';
    }
    
    roleModal.show();
}

/**
 * Guardar rol (crear o actualizar)
 */
async function saveRole() {
    const roleId = document.getElementById('role-id').value;
    const roleName = document.getElementById('role-name-input').value.trim();
    const btnSave = document.getElementById('btn-save-role');
    const formErrors = document.getElementById('role-form-errors');
    
    // Obtener permisos seleccionados
    const selectedPermissions = [];
    document.querySelectorAll('#permissions-container input[type="checkbox"]:checked').forEach(checkbox => {
        selectedPermissions.push(parseInt(checkbox.value));
    });
    
    // Validaciones
    if (!roleName) {
        formErrors.textContent = 'El nombre del rol es requerido';
        formErrors.classList.remove('d-none');
        return;
    }
    
    formErrors.classList.add('d-none');
    setButtonLoading(btnSave, true);
    
    const data = {
        role_name: roleName,
        permissions: selectedPermissions
    };
    
    let result;
    
    if (roleId) {
        // Actualizar
        result = await fetchAPI(`/roles/${roleId}`, {
            method: 'PUT',
            body: data
        });
    } else {
        // Crear
        result = await fetchAPI('/roles', {
            method: 'POST',
            body: data
        });
    }
    
    setButtonLoading(btnSave, false);
    
    if (result.ok) {
        roleModal.hide();
        showSuccess(roleId ? 'Rol actualizado exitosamente' : 'Rol creado exitosamente');
        loadRolesSection();
        loadRoles(); // Recargar roles para el select de usuarios
    } else {
        const errors = result.data.errors || [result.data.error || 'Error al guardar rol'];
        formErrors.innerHTML = errors.join('<br>');
        formErrors.classList.remove('d-none');
    }
}

/**
 * Editar rol
 */
window.editRole = function(roleId) {
    openRoleModal(roleId);
};

/**
 * Eliminar rol
 */
window.deleteRole = async function(roleId) {
    if (!confirmAction('¿Estás seguro de eliminar este rol?')) {
        return;
    }
    
    const result = await fetchAPI(`/roles/${roleId}`, {
        method: 'DELETE'
    });
    
    if (result.ok) {
        showSuccess('Rol eliminado exitosamente');
        loadRolesSection();
        loadRoles(); // Recargar roles para el select de usuarios
    } else {
        showError(result.data.error || 'Error al eliminar rol');
    }
};