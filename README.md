# Sistema de Autenticación con Roles y Permisos

Este sistema de autenticación y manejo de usuarios y roles esta desarrollado en **PHP puro** aplicando DDD y **SOLID**
Se utilizo DDD como estrategia en arquitectura  para garantizar escalabilidad y claridad en la lógica de negocio, manteniendo independiente  la logica y si en un futuro se quiere cambiar la base de datos, por ejemplo en MYSQL o PostgresSQL no sera necesario hacer grandes cambios.

Adicional se realizo con esta arquitectura para mostrar el conocimiento en la estructuración del proyecto en sus diferentes partes.

Como convension de codigo se utiliz¿o snake_case para variables y camelCase para metodos

---

## ⚙️ Características

* Autenticación con **JWT** (JSON Web Tokens)
* Sistema de **roles y permisos** (muchos a muchos)
* CRUD completo de **usuarios** y **roles**
* Arquitectura **DDD** con capas bien definidas
* Principios **SOLID** aplicados
* **Seguridad**: prepared statements, bcrypt, sanitización XSS
* Frontend responsivo con **Bootstrap 5**
* **Sin frameworks** ni ORMs (PHP puro)

---

## 👥 Roles del Sistema

### 🔑 SuperAdmin

* Se puede realizar todas las acciones de los cruds de  usuarios y roles.
* Gestión completa de roles y permisos
* CRUD de usuarios por interfaz grafica

### ⚙️ Admin

* CRUD de usuarios
* No tiene permisos para editar, crear o borrar Roles

### 👤 User

* No puede gestionar nada, solo ver su perfil

---

## 🛠️ Instalación

### Requisitos

* PHP 7.4+ o 8.0+
* MySQL 5.7+
* Apache con `mod_rewrite` habilitado
* Extensiones PHP: `PDO`, `pdo_mysql`, `mbstring`

### Paso 1: Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd proyecto
```

### Paso 2: Configurar el entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` con tus credenciales:

```ini
DB_HOST=localhost
DB_PORT=3306
DB_NAME=auth_system
DB_USER=root
DB_PASSWORD=tu_password

JWT_SECRET=clave_segura
JWT_EXPIRATION=3600
```

Generar `JWT_SECRET` seguro:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

### Paso 3: Crear la base de datos

```bash
mysql -u root -p < database/schema.sql
```

O desde phpMyAdmin:

1. Crear base de datos `auth_system`
2. Importar `database/schema.sql`

### Paso 4: Configurar Apache

**Opción 1: Virtual Host (Recomendado)**

```apache
<VirtualHost *:80>
    ServerName auth-system.local
    DocumentRoot "/ruta/al/proyecto/public"
    
    <Directory "/ruta/al/proyecto/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Agregar al hosts

```
127.0.0.1 auth-system.local
```

### Acceder al sistema

* `http://auth-system.local`

Los .htaccess estan configurados para que se pueda acceder facilmente siguiendo las reglas de las url
hay uno en el root que redirecciona al de  la carpeta public
---

## 👤 Usuarios de Prueba

| Email                                             | Contraseña | Rol        |
| ------------------------------------------------- | ---------- | ---------- |
| [superadmin@mail.com](mailto:superadmin@mail.com) | 123456     | SuperAdmin |
| [admin@mail.com](mailto:admin@mail.com)           | 123456     | Admin      |
| [user@mail.com](mailto:user@mail.com)             | 123456     | User       |

---

## 📂 Estructura del Proyecto

```
project-root/
├── public/                    # Punto de entrada público
│   ├── index.php             # Router principal
│   ├── login.html            # Vista de login
│   ├── dashboard.html        # Vista del dashboard
│   ├── .htaccess             # Configuración Apache
│   └── js/
│       ├── app.js           # Utilidades comunes
│       ├── login.js         # Lógica del login
│       └── dashboard.js     # Lógica del dashboard
│
├── src/
│   ├── bootstrap.php         # Inicialización de la app
│   ├── routes.php            # Definición de rutas
│   │
│   ├── Config/               # Configuración
│   │   ├── Database.php      # Singleton PDO
│   │   └── config.php        # Constantes globales
│   │
│   ├── Core/                 # Núcleo del framework
│   │   ├── Router.php        # Enrutador
│   │   ├── Request.php       # Wrapper de peticiones
│   │   ├── Response.php      # Respuestas JSON
│   │   ├── Controller.php    # Controlador base
│   │   └── View.php          # Helper para vistas
│   │
│   ├── Domain/               # Capa de dominio (DDD)
│   │   ├── User/
│   │   │   ├── User.php               # Entidad
│   │   │   └── IUserRepository.php    # Interface
│   │   ├── Role/
│   │   │   ├── Role.php
│   │   │   └── IRoleRepository.php
│   │   └── Permission/
│   │       └── Permission.php
│   │
│   ├── Infrastructure/       # Implementaciones técnicas
│   │   ├── Repositories/
│   │   │   ├── UserRepository.php     # Implementación con PDO
│   │   │   └── RoleRepository.php
│   │   └── Security/
│   │       ├── JWT.php                # Manejo de tokens
│   │       └── Hash.php               # Bcrypt wrapper
│   │
│   ├── Application/          # Casos de uso
│   │   ├── Auth/
│   │   │   └── AuthService.php
│   │   ├── Users/
│   │   │   ├── UserService.php
│   │   │   └── UserValidator.php
│   │   └── Roles/
│   │       ├── RoleService.php
│   │       ├── RoleValidator.php
│   │       └── PermissionChecker.php
│   │
│   └── Presentation/         # Capa de presentación
│       ├── Controllers/
│       │   ├── AuthController.php
│       │   ├── UserController.php
│       │   ├── RoleController.php
│       │   └── PageController.php
│       └── Middleware/
│           └── AuthMiddleware.php
│
├── database/
│   └── schema.sql            # Script de base de datos
│
├── .env                      # Configuración (no subir a Git)
├── .env.example              # Template de configuración
├── .gitignore
└── README.md
```

---

## 🔌 API Endpoints

### Autenticación

```http
POST /api/login
Content-Type: application/json
```

**Body**

```json
{
  "email": "user@mail.com",
  "password": "123456"
}
```

**Response**

```json
{
  "token": "eyJhbGciOiJIUzI1NiIsIn...",
  "user": {
    "id": 1,
    "name": "Usuario",
    "email": "user@mail.com",
    "role_id": 3,
    "role_name": "User"
  }
}
```

### Usuarios (requiere autenticación)

* `GET    /api/users`           — Listar usuarios
* `GET    /api/users/:id`       — Ver usuario
* `POST   /api/users`           — Crear usuario
* `PUT    /api/users/:id`       — Actualizar usuario
* `DELETE /api/users/:id`       — Eliminar usuario

### Roles (requiere autenticación — solo SuperAdmin)

* `GET    /api/roles`           — Listar roles
* `GET    /api/roles/:id`       — Ver rol
* `POST   /api/roles`           — Crear rol
* `PUT    /api/roles/:id`       — Actualizar rol
* `DELETE /api/roles/:id`       — Eliminar rol
* `GET    /api/permissions`     — Listar permisos disponibles

**Ejemplo de petición autenticada (JavaScript):**

```javascript
fetch('/api/users', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer eyJhbGciOiJIUzI1NiIsIn...',
    'Content-Type': 'application/json'
  }
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

##   Decisiones de Diseño

### 1) Arquitectura DDD (Domain-Driven Design)

Se implementó una arquitectura en capas para separar responsabilidades:

**Domain (Dominio)**

* Contiene las entidades e interfaces
* No depende de ninguna otra capa
* Representa la lógica de negocio pura

**Infrastructure (Infraestructura)**

* Implementaciones concretas de los repositorios
* Manejo de JWT y hash de contraseñas
* Acceso a base de datos con PDO

**Application (Aplicación)**

* Casos de uso y servicios
* Orquestación de la lógica de negocio
* Validadores de datos

**Presentation (Presentación)**

* Controladores que manejan HTTP
* Middleware de autenticación
* Respuestas JSON estandarizadas

> **Ventaja:** Código mantenible, testeable y escalable. Cada capa tiene responsabilidades claras.

### 2) Principios SOLID

**S - Single Responsibility Principle**
Cada clase tiene una única responsabilidad:

* `UserRepository`: Acceso a datos de usuarios
* `UserValidator`: Validación de datos
* `UserService`: Lógica de negocio de usuarios
* `AuthMiddleware`: Autenticación

**O - Open/Closed Principle**
Abierto para extensión pero cerrado para modificación:

* Cambiar de MySQL a PostgreSQL ⇒ solo cambias el *Repository*
* Agregar otro tipo de autenticación ⇒ implementas la *interface*

**L - Liskov Substitution Principle**
Implementaciones sustituibles sin romper el código:

* `IUserRepository` puede tener múltiples implementaciones (MySQL/PostgreSQL/etc.)

**I - Interface Segregation Principle**
Interfaces pequeñas y específicas:

* `IUserRepository` solo métodos de usuarios
* `IRoleRepository` solo métodos de roles

**D - Dependency Inversion Principle**
Depender de abstracciones, no de implementaciones concretas:

```php
class UserService {
    private IUserRepository $user_repository; // Interface, no implementación
    
    public function __construct(IUserRepository $user_repository) {
        $this->user_repository = $user_repository;
    }
}
```

### 3) Seguridad Implementada

**SQL Injection**
Todos los queries usan prepared statements:

```php
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $this->connection->prepare($query);
$stmt->execute([$email]);
```

**XSS (Cross-Site Scripting)**

* Sanitización de inputs en el backend
* Escape de HTML en el frontend con `escapeHtml()`
* Headers de seguridad configurados

**Autenticación**

* Passwords hasheados con **bcrypt**
* Tokens **JWT** con firma **HS256**
* Middleware que valida tokens en cada petición protegida

**Validaciones**

* Validación de email con `filter_var`
* Longitud mínima de passwords
* Verificación de unicidad de emails
* Type casting de parámetros: `(int)$id`

### 4) Evitar N+1 Queries

Todos los endpoints que devuelven listas usan **JOINs**:

```php
// UserRepository - Un solo query trae usuarios CON su rol
$query = "
    SELECT u.*, r.role_name
    FROM users u
    INNER JOIN roles r ON u.role_id = r.id
";
```

* Sin JOIN ⇒ 1 query para usuarios + N queries para rol de cada usuario = **1 + N queries**
* Con JOIN ⇒ **1 query** total

### 5) Sin Frameworks

* Demuestra comprensión profunda de PHP
* Capacidad de crear arquitectura desde cero
* Código más ligero y directo

### 6) Frontend Simple

* **Bootstrap 5** para UI rápida
* **JavaScript vanilla** (sin jQuery ni React)
* **Fetch API** para consumir la API
* **LocalStorage** para el token JWT

---

## 🐘 Problema PHP 7 vs PHP 8.1+

### El problema

El siguiente código funciona en PHP 7.x pero genera un error en PHP 8.1+:

```php
class Usuario {
    public string $nombre;
    
    public function getNombre(): string {
        return $this->nombre;
    }
}

$usuario = new Usuario();
echo $usuario->getNombre(); // Error en PHP 8.1+
```

**Error en PHP 8.1+:**

```
Fatal error: Uncaught Error: Typed property Usuario::$nombre must not be accessed before initialization
```

### ¿Por qué ocurre?

En PHP 7 las propiedades tipadas sin valor inicial se tomaban como null y no pasaba nada, era como si el lenguaje lo dejara pasar aunque no tuviera sentido. El problema es que eso podía causar errores escondidos porque se decia que una propiedad era de cierto tipo, pero en realidad estaba vacía. A partir de PHP 8.1 esto cambió: ahora esas propiedades tienen que estar inicializadas sí o sí antes de usarse. Ya no existe esa especie de valor por defecto que las convertía en null automáticamente. Esto obliga a escribir codigo mas claro y estricto y sigue las praticas de tipar cosigo y los retornos

### Soluciones

**Solución 1:**

```php
class Usuario {
    public string $nombre = '';
    public function getNombre(): string { return $this->nombre; }
}
```

**Solución 2:**

```php
class Usuario {
    public ?string $nombre = null;
    public function getNombre(): ?string { return $this->nombre; }
}
```

**Solución 3**

```php
class Usuario {
    public string $nombre;
    public function __construct(string $nombre = '') { $this->nombre = $nombre; }
    public function getNombre(): string { return $this->nombre; }
}
```

---

## 🧪 Testing

### Probar la API con cURL

```bash
# Login
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"superadmin@mail.com","password":"123456"}'

# Listar usuarios (con token)
curl -X GET http://localhost/api/users \
  -H "Authorization: Bearer TU_TOKEN_AQUI"

# Crear usuario
curl -X POST http://localhost/api/users \
  -H "Authorization: Bearer TU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{"name":"Nuevo Usuario","email":"nuevo@mail.com","password":"123456","role_id":3}'
```

### Probar con Postman

1. Importar la colección de endpoints
2. Crear variable `{{token}}` con el JWT
3. Configurar header: `Authorization: Bearer {{token}}`
 