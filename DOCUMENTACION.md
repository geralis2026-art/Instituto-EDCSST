# Documentación del Proyecto — Instituto EDCSST

Registro de cambios, mejoras y configuraciones realizadas en el sistema, para entrega al cliente.

---

## Descripción General del Sistema

Sistema web para el Instituto EDCSST que permite gestionar capacitados, cursos y certificados, con un panel administrativo para empleados y un sitio público para visitantes.

### Sitio público
- **Inicio**: presenta los cursos destacados.
- **Nosotros**: información institucional.
- **Cursos**: catálogo de cursos organizado por categorías.
- **Contacto**: formulario con protección reCAPTCHA y límite de envíos (3 por minuto).
- **Consulta de certificados**: el capacitado busca sus certificados por documento o código único, y descarga el PDF mediante un enlace seguro válido por 30 minutos.
- **Verificación de autenticidad**: cualquier tercero (empresa, entidad) puede ingresar el código de un certificado y confirmar si es válido y vigente.

### Panel administrativo (`/admin`)
Acceso solo para empleados autenticados y activos. Existen dos roles:

- **Administrador**: acceso completo — gestión de capacitados, cursos, categorías, certificados, mensajes de contacto y usuarios del sistema.
- **Capacitador**: puede consultar capacitados y crear/ver certificados, sin permisos de edición/eliminación ni acceso a configuración general.

> Los usuarios nuevos se crean inactivos por defecto; un administrador debe activarlos antes de que puedan ingresar.

### Emisión de certificados
1. Se selecciona el capacitado, el curso, la fecha de emisión y se carga el PDF del certificado.
2. El sistema genera automáticamente un código único (formato `EDCSST-AÑO-00001`).
3. Calcula la fecha de vencimiento (1 año después de la emisión).
4. Actualiza automáticamente el total de horas capacitadas de la persona.

### Funcionalidades pendientes (a futuro)
Estas opciones existen como "próximamente" en el sistema y no están activas todavía:
- Importación masiva de capacitados desde Excel (con plantilla descargable).
- Generación masiva de certificados.

---

## Seguridad Implementada

- **Tokens de seguridad (CSRF)** en todos los formularios.
- **Límites de intentos** (rate limiting): login (5 por IP/correo), formulario de contacto (3/min), consulta y verificación de certificados (10/min).
- **reCAPTCHA v2** en el formulario de contacto público.
- **Cabeceras de seguridad** (CSP, HSTS, protección contra clickjacking) en todas las páginas.
- **Roles y permisos** para proteger el panel administrativo.
- **Enlaces firmados y temporales** para la descarga de certificados en PDF.
- **Validación de archivos**: solo se aceptan PDFs de máximo 10 MB.

---

## 2026-06-10

### Documentación del código
- Se agregaron comentarios explicativos en el código del backend (modelos, controladores, validaciones, middleware y rutas) para facilitar el mantenimiento futuro y la entrega de documentación técnica al cliente.

---

## 2026-06-09 / 2026-06-10

### Seguridad y estilos (CSP)
- Se corrigió la política de seguridad de contenido (CSP) que bloqueaba estilos de la aplicación.
- Se agregó la fuente Figtree y se ajustaron las cabeceras de seguridad para que los estilos carguen correctamente sin afectar la protección del sitio.

### Formulario de contacto (reCAPTCHA)
- Se solucionó un error que impedía el envío del formulario de contacto en producción.
- Causa: las claves de reCAPTCHA configuradas eran inválidas. Se generaron nuevas claves (tipo "casilla de verificación") y se actualizaron en el servidor.

### Limpieza automática de PDFs vencidos
- Se creó un proceso automático que, una vez al año después del vencimiento de un certificado (2 años desde su emisión), elimina únicamente el archivo PDF del servidor para liberar espacio.
- El registro del certificado se conserva siempre: sigue siendo verificable y cuenta para las horas capacitadas de la persona.
- **Pendiente:** configurar en el panel de Hostinger (hPanel → Avanzado → Cron Jobs) la tarea programada que ejecuta este proceso diariamente.

### Backup
- Se realizó respaldo completo (base de datos, certificados PDF e imágenes) antes de aplicar los cambios anteriores.
