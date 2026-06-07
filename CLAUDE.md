# Instituto EDCSST — Documentación del Sistema

## Descripción General

Sistema web para el **Instituto EDCSST** (Entidad de Certificación en Seguridad Social y Trabajo). Permite la gestión de capacitados, cursos, certificados y mensajes de contacto, con un panel administrativo para empleados y un sitio público para visitantes.

**Stack tecnológico:**
- PHP 8.3 + Laravel 13.8
- MySQL
- Tailwind CSS + Alpine.js + Vite
- Autenticación: Laravel Breeze (sesiones)
- reCAPTCHA v3 en formularios públicos
- Almacenamiento local para PDFs (`storage/app/certificados/`)

**Repositorio / Producción:** deploy en Railway (ver `.env` → `APP_URL`)

---

## Arquitectura General

```
Sitio público (/*)          → Controllers/Public/
Panel admin (/admin/*)      → Controllers/Admin/   [requiere: auth + activo]
Autenticación (/login, ...) → Controllers/Auth/    [Breeze, sin modificar]
```

---

## Modelos y Base de Datos

### Diagrama de relaciones

```
users
 └── certificados (emitido_por)

categorias
 └── cursos
      └── certificados
           ├── capacitados (capacitado_id)
           └── users       (emitido_por)

mensajes            [independiente — formulario de contacto]
configuracion_sitio [singleton — fila única con id=1]
```

### `users` — Empleados del instituto

| Campo              | Tipo     | Descripción                                   |
|--------------------|----------|-----------------------------------------------|
| `id`               | PK       |                                               |
| `name`             | string   |                                               |
| `email`            | string   | único                                         |
| `password`         | hashed   |                                               |
| `rol`              | enum     | `admin` \| `capacitador`                      |
| `activo`           | boolean  | Si es `false`, no puede hacer login           |
| `email_verified_at`| datetime | nullable                                      |

**Métodos clave:** `isAdmin()`, `isCapacitador()`, `scopeActivos()`

### `categorias` — Agrupaciones de cursos

| Campo       | Tipo    | Descripción                    |
|-------------|---------|--------------------------------|
| `nombre`    | string  |                                |
| `slug`      | string  | único, auto-generado           |
| `descripcion`| text   | nullable                       |
| `activo`    | boolean |                                |

**Métodos clave:** `scopeActivas()`. Slug se genera automáticamente en `booted()`.

### `cursos` — Programas de capacitación

| Campo               | Tipo    | Descripción                                |
|---------------------|---------|--------------------------------------------|
| `categoria_id`      | FK      | → categorias (restrictOnDelete)            |
| `nombre`            | string  |                                            |
| `slug`              | string  | único, auto-generado                       |
| `descripcion_corta` | text    |                                            |
| `duracion`          | string  | ej: "40 horas"                             |
| `intensidad_horaria`| integer | Horas oficiales, se copia al certificado   |
| `imagen`            | string  | nullable, ruta en storage                  |
| `destacado`         | boolean | Si aparece en el home del sitio público    |
| `activo`            | boolean |                                            |

**Métodos clave:** `scopeActivos()`, `scopeDestacados()`, `getImagenUrlAttribute()` (fallback a imagen default)

### `capacitados` — Personas que reciben certificados

| Campo              | Tipo    | Descripción                                         |
|--------------------|---------|-----------------------------------------------------|
| `nombre_completo`  | string  |                                                     |
| `documento`        | string  | único (cédula, pasaporte, etc.)                     |
| `correo`           | string  | nullable                                            |
| `telefono`         | string  | nullable                                            |
| `horas_capacitadas`| integer | Total acumulado, se recalcula automáticamente        |

**Métodos clave:** `recalcularHorasCapacitadas()` (llamado desde eventos del Certificado), `porDocumento($doc)`

### `certificados` — Documentos emitidos

| Campo               | Tipo    | Descripción                                              |
|---------------------|---------|----------------------------------------------------------|
| `capacitado_id`     | FK      | → capacitados (cascadeOnDelete)                          |
| `curso_id`          | FK      | → cursos (restrictOnDelete)                              |
| `emitido_por`       | FK      | → users (nullOnDelete), nullable                         |
| `codigo_unico`      | string  | único, formato `EDCSST-{AÑO}-{ID_5_DIGITOS}`            |
| `fecha_emision`     | date    |                                                          |
| `fecha_vencimiento` | date    | nullable, = fecha_emision + 1 año (calculado al guardar)|
| `intensidad_horaria`| integer | Copiado del curso al emitir                              |
| `archivo_pdf`       | string  | nullable, ruta en `storage/app/certificados/`            |
| `activo`            | boolean | Permite invalidar sin eliminar                           |

**Métodos clave:** `generarCodigoUnico($id)`, `porCodigo($codigo)`, `isVencido()`, `scopeActivos()`, `scopeVigentes()`, `scopeVencidos()`

**Evento importante:** `booted()` — al crear, actualizar o eliminar un certificado, recalcula automáticamente `horas_capacitadas` del capacitado asociado.

### `mensajes` — Formulario de contacto público

| Campo            | Tipo   | Descripción                              |
|------------------|--------|------------------------------------------|
| `nombre`         | string |                                          |
| `correo`         | string |                                          |
| `mensaje`        | text   |                                          |
| `estado`         | enum   | `nuevo` \| `leido` \| `respondido`       |
| `notas_internas` | text   | nullable, solo visible para admins       |
| `ip`             | string | nullable, IP del remitente               |

**Métodos clave:** `marcarComoLeido()`, `marcarComoRespondido()`, `scopeNuevos()`

### `configuracion_sitio` — Datos globales del sitio

Tabla singleton (siempre id=1). Se accede vía `ConfiguracionSitio::obtener()`.

Contiene: nombre del instituto, descripción, teléfono, correo, dirección, WhatsApp, Facebook, Instagram, logo y plantilla de certificado.

---

## Sistema de Roles y Acceso

Existen dos roles de usuario:

| Rol           | Acceso                                                      |
|---------------|-------------------------------------------------------------|
| `admin`       | Todo el panel: CRUD completo, mensajes, usuarios, cursos    |
| `capacitador` | Solo lectura de capacitados + crear/ver certificados        |

**Middleware aplicado a `/admin`:**

1. `auth` — usuario autenticado (Breeze)
2. `activo` (`EnsureUserIsActivo`) — usuario con `activo = true`
3. `admin` (`EnsureUserIsAdmin`) — solo para rutas de CRUD completo

> Los usuarios nuevos se crean **inactivos** por defecto. Un admin debe activarlos.

---

## Rutas Principales

### Públicas (sin login)

| Método | URL                            | Descripción                                   |
|--------|--------------------------------|-----------------------------------------------|
| GET    | `/`                            | Home con cursos destacados                    |
| GET    | `/nosotros`                    | Página "Sobre nosotros"                       |
| GET    | `/cursos`                      | Catálogo de cursos por categoría              |
| GET/POST | `/contacto`                  | Formulario de contacto (reCAPTCHA, 3/min)     |
| GET/POST | `/consulta`                  | Búsqueda de certificados (10/min)             |
| GET    | `/consulta/descargar/{cert}`   | Descarga PDF (URL firmada, 30 min)            |
| GET/POST | `/verificar`                 | Verificación de autenticidad (10/min)         |

### Panel Admin (requiere login + activo)

| URL                                    | Acceso        | Descripción                    |
|----------------------------------------|---------------|--------------------------------|
| `/admin`                               | todos         | Redirige a capacitados         |
| `/admin/capacitados`                   | todos         | Lista capacitados              |
| `/admin/capacitados/{id}`              | todos         | Ver detalle                    |
| `/admin/certificados` (index/create/show) | todos      | Gestión certificados           |
| `/admin/certificados/{id}/pdf`         | todos         | Ver PDF                        |
| `/admin/capacitados` (create/edit/destroy) | solo admin | CRUD completo                  |
| `/admin/certificados` (edit/destroy/toggle) | solo admin | Editar/eliminar certificados  |
| `/admin/cursos`                        | solo admin    | CRUD cursos                    |
| `/admin/categorias`                    | solo admin    | CRUD categorías                |
| `/admin/mensajes`                      | solo admin    | Bandeja de contacto            |
| `/admin/usuarios`                      | solo admin    | Gestión de empleados           |

---

## Flujos de Negocio Principales

### Emisión de un certificado

1. Admin/capacitador va a `/admin/certificados/create`
2. Selecciona capacitado, curso, fecha de emisión y carga el PDF
3. El sistema calcula automáticamente:
   - `codigo_unico` = `EDCSST-{AÑO}-{ID_5_DIGITOS}` (se genera post-insert)
   - `fecha_vencimiento` = fecha_emision + 1 año
   - `intensidad_horaria` copiada del curso
4. El PDF se guarda en `storage/app/certificados/`
5. El evento `saved` del modelo recalcula `horas_capacitadas` del capacitado

### Consulta pública de certificados

1. El capacitado entra a `/consulta`
2. Puede buscar por **documento** (cédula) o por **código único**
3. Si encuentra resultados, el sistema genera URLs firmadas (válidas 30 min) para descarga
4. Solo se pueden descargar certificados activos y no vencidos

### Verificación de autenticidad

1. Tercero (empresa, institución) entra a `/verificar`
2. Ingresa el código del certificado (ej: `EDCSST-2026-00001`)
3. El sistema muestra los datos del certificado, capacitado, curso y si está vigente o vencido

---

## Seguridad

| Mecanismo                 | Dónde aplica                                    |
|---------------------------|-------------------------------------------------|
| CSRF tokens               | Todos los formularios POST/PATCH/DELETE         |
| Rate limiting             | Login (5/IP, 5/email), contacto (3/min), consulta/verificación (10/min) |
| reCAPTCHA v3              | Formulario de contacto público                  |
| CSP, HSTS, X-Frame-Options| Todos los requests (middleware `SecurityHeaders`) |
| Roles + middleware        | Rutas admin protegidas por `activo` y `admin`   |
| URLs firmadas             | Descarga de PDFs desde el sitio público         |
| Validación de archivos    | Solo PDFs, máx 10 MB                           |

---

## Variables de Entorno Clave

```env
APP_NAME=
APP_ENV=                  # local | production
APP_URL=

DB_CONNECTION=mysql
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

RECAPTCHA_SITE_KEY=       # Google reCAPTCHA v3
RECAPTCHA_SECRET_KEY=

MAIL_*                    # Para recuperación de contraseña
```

---

## Funcionalidades Pendientes (TODO)

Estas acciones están como placeholders en el código y devuelven un mensaje de "próximamente":

- `CapacitadoController::descargarPlantilla()` — Plantilla Excel para importación masiva
- `CapacitadoController::importar()` — Importación masiva de capacitados desde Excel
- `CertificadoController::masivosForm()` — Formulario de generación masiva de certificados
- `CertificadoController::generarMasivos()` — Generación masiva de certificados

---

## Convenciones del Proyecto

- **Español** en todo: nombres de modelos, variables, vistas, mensajes de error, comentarios
- **Soft delete no usado** — se usa el campo `activo` para desactivar sin eliminar
- **Slugs automáticos** — generados desde el nombre en `booted()` de Categoria y Curso
- **Códigos únicos** — formato `EDCSST-YYYY-00001`, generados post-insert para tener el ID disponible
- **PDF storage** — disco `local` (no `public`), se sirven a través de controladores con validaciones
- **Paginación** — 15 registros por página en listas admin, 20 en mensajes

---

## Estructura de Directorios

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # DashboardController, CapacitadoController, CertificadoController,
│   │   │                   # CursoController, CategoriaController, MensajeController, UsuarioController
│   │   ├── Auth/           # Breeze — no modificar
│   │   ├── Public/         # HomeController, CatalogoController, ContactoController,
│   │   │                   # ConsultaCertificadoController, VerificacionController
│   │   └── Controller.php
│   ├── Middleware/
│   │   ├── EnsureUserIsActivo.php   # Bloquea usuarios inactivos
│   │   ├── EnsureUserIsAdmin.php    # Restringe a rol admin
│   │   └── SecurityHeaders.php     # CSP, HSTS, X-Frame-Options
│   └── Requests/           # CapacitadoRequest, CursoRequest, CategoriaRequest, CertificadoRequest
├── Models/                 # User, Categoria, Curso, Capacitado, Certificado, Mensaje, ConfiguracionSitio
└── Providers/
    └── AppServiceProvider.php  # Rate limiters

database/migrations/        # 11 migraciones en orden cronológico
resources/views/
├── admin/                  # Vistas del panel (dashboard, capacitados, cursos, etc.)
├── public/                 # Vistas del sitio (home, catalogo, consulta, verificar, contacto)
├── auth/                   # Breeze (login, reset-password, etc.)
├── layouts/                # app.blade.php, admin.blade.php, public.blade.php, guest.blade.php
└── components/             # Componentes Blade reutilizables
routes/
├── web.php                 # Todas las rutas (públicas + admin + perfil)
└── auth.php                # Rutas Breeze
storage/app/certificados/   # PDFs almacenados (no públicos)
```

---

## Comandos Útiles

```bash
# Instalar dependencias
composer install
npm install

# Compilar assets
npm run dev       # desarrollo
npm run build     # producción

# Base de datos
php artisan migrate
php artisan migrate:fresh --seed   # reset + seeders

# Servidor local (Laragon o artisan)
php artisan serve
```
