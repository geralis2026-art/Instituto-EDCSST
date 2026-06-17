# Documentación del Proyecto — Instituto EDCSST

Documentación funcional y técnica del sistema web del Instituto EDCSST (Entidad de Certificación en Seguridad Social y Trabajo), preparada para la entrega al cliente.

**Versión del documento:** 1.2 — Última actualización: 2026-06-10

---

## Índice

1. [Descripción general](#descripción-general)
2. [Sitio público](#sitio-público)
3. [Panel administrativo](#panel-administrativo)
4. [Roles y permisos](#roles-y-permisos)
5. [Flujos de negocio principales](#flujos-de-negocio-principales)
6. [Información que maneja el sistema](#información-que-maneja-el-sistema)
7. [Seguridad implementada](#seguridad-implementada)
8. [Mantenimiento y tareas automáticas](#mantenimiento-y-tareas-automáticas)
9. [Requisitos y compatibilidad](#requisitos-y-compatibilidad)
10. [Funcionalidades pendientes / a futuro](#funcionalidades-pendientes--a-futuro)
11. [Accesos y datos técnicos](#accesos-y-datos-técnicos)
12. [Registro de cambios](#registro-de-cambios)

---

## Descripción General

El sistema permite al Instituto EDCSST:

- Publicar su catálogo de cursos en un sitio web público.
- Recibir mensajes de contacto de posibles estudiantes o empresas.
- Registrar a las personas capacitadas (estudiantes) y los cursos que toman.
- Emitir certificados en PDF con un código único de validación.
- Permitir que cualquier persona consulte y descargue sus certificados, y que terceros (empresas, entidades) verifiquen su autenticidad.
- Gestionar todo lo anterior desde un panel administrativo privado, con distintos niveles de acceso para el personal del instituto.

El sistema está dividido en dos partes:

- **Sitio público**: accesible para cualquier visitante, sin necesidad de iniciar sesión.
- **Panel administrativo** (`/admin`): solo para empleados del instituto, con inicio de sesión.

---

## Sitio Público

| Sección | Descripción |
|---|---|
| **Inicio** | Presenta la información general del instituto y hasta 4 cursos destacados. |
| **Nosotros** | Información institucional (misión, descripción, etc.). |
| **Cursos** | Catálogo completo de cursos, organizado por categorías. Cada curso se muestra en una tarjeta compacta (nombre, imagen y duración) que **se voltea al hacer clic/tap** para mostrar la descripción completa, la intensidad horaria y un botón para solicitar información. |
| **Contacto** | Formulario para que cualquier visitante envíe un mensaje al instituto. Protegido con reCAPTCHA y limitado a 3 envíos por minuto por visitante para evitar spam. |
| **Consulta de certificados** | El capacitado busca sus certificados ingresando su número de documento o el código único del certificado. Si los encuentra, puede descargar el PDF mediante un enlace seguro y temporal (válido 30 minutos). Solo se pueden descargar certificados activos y vigentes (no vencidos). |
| **Verificación de autenticidad** | Cualquier tercero (empresa, entidad gubernamental, etc.) puede ingresar el código de un certificado (ej: `EDCSST-2026-00001`) y ver sus datos: persona, curso, fecha de emisión y si está vigente o vencido. No requiere descargar nada. |

---

## Panel Administrativo

Acceso en `/admin`, solo para empleados con cuenta activa. Incluye:

| Módulo | Descripción |
|---|---|
| **Dashboard (inicio)** | Resumen general: total de capacitados, certificados activos, cursos, categorías, mensajes nuevos, horas capacitadas totales, gráfica de certificados emitidos por mes (últimos 12 meses), últimos certificados emitidos, top 5 capacitados por horas, cursos más solicitados y mensajes recientes. |
| **Capacitados** | Listado, búsqueda y ficha de cada persona registrada, con su historial de certificados y horas acumuladas. |
| **Certificados** | Listado con búsqueda por código o capacitado y filtro por curso. Permite ver el detalle, el PDF, crear nuevos certificados y (solo administradores) editarlos, eliminarlos o activarlos/desactivarlos. |
| **Cursos** | Catálogo interno de cursos: nombre, descripción, duración, intensidad horaria, imagen, categoría, si está destacado (aparece en el home) y si está activo. |
| **Categorías** | Agrupaciones de cursos (ej: "Alturas", "Espacios confinados"). |
| **Mensajes** | Bandeja de entrada de los mensajes enviados desde el formulario de contacto público. Permite marcarlos como leídos/respondidos y agregar notas internas. |
| **Usuarios** | Gestión de las cuentas del personal del instituto (crear, activar/desactivar, eliminar, asignar rol). |

---

## Roles y Permisos

El sistema maneja dos tipos de usuario (empleados):

### Administrador
Acceso completo a todo el panel:
- CRUD completo de capacitados, cursos, categorías y certificados.
- Activar/desactivar certificados sin eliminarlos.
- Gestión de la bandeja de mensajes.
- Gestión de usuarios del sistema (crear, activar, desactivar, eliminar).

### Capacitador
Acceso limitado, pensado para el personal que dicta los cursos:
- Consultar el listado y la ficha de capacitados (solo lectura).
- Crear nuevos certificados y consultar los existentes.
- **No puede**: editar/eliminar capacitados o certificados, gestionar cursos, categorías, mensajes ni usuarios.

### Reglas generales de acceso
- Para entrar al panel se requiere estar autenticado **y** tener la cuenta marcada como **activa**.
- Las cuentas nuevas se crean **inactivas** por defecto — un administrador debe activarlas manualmente desde "Usuarios".
- Si un administrador desactiva a un usuario que tiene una sesión abierta, esa sesión se cierra automáticamente en su siguiente acción.
- Un usuario no puede desactivarse ni eliminarse a sí mismo (para evitar quedarse sin acceso por error).
- **Recuperación de contraseña**: cualquier empleado puede solicitar el restablecimiento de su contraseña desde la pantalla de inicio de sesión ("¿Olvidaste tu contraseña?"); el sistema envía un enlace temporal de recuperación al correo registrado.

---

## Flujos de Negocio Principales

### Emisión de un certificado
1. El administrador o capacitador entra a "Certificados → Nuevo".
2. Selecciona el capacitado (o lo busca por documento/nombre), el curso, la fecha de emisión y carga el PDF del certificado (máximo 10 MB, solo PDF).
3. Indica la **modalidad** en que se realizó la capacitación: virtual o presencial.
4. El sistema calcula automáticamente:
   - Un **código único** con formato `EDCSST-AÑO-00001` (correlativo).
   - La **fecha de vencimiento**: un año después de la fecha de emisión.
   - La **intensidad horaria**, copiada del curso seleccionado (queda fija en el certificado aunque el curso cambie después).
5. El total de **horas capacitadas** de la persona se recalcula automáticamente sumando todos sus certificados activos.

### Consulta pública de certificados
1. El capacitado entra a "Consulta de certificados" en el sitio público.
2. Busca por su número de documento o por el código del certificado.
3. Si hay resultados, el sistema genera enlaces de descarga **temporales** (válidos 30 minutos) para los certificados que estén activos y no vencidos.

### Verificación de autenticidad
1. Un tercero entra a "Verificar certificado".
2. Ingresa el código del certificado.
3. El sistema muestra los datos del certificado (persona, curso, fechas) e indica si está **vigente** o **vencido**.

### Vencimiento de certificados
- Un certificado vence automáticamente **1 año** después de su fecha de emisión.
- Un certificado vencido:
  - **No se puede descargar** desde la consulta pública.
  - **Sigue siendo verificable** en "Verificar certificado" (se indica que está vencido).
  - **Sigue contando** para las horas capacitadas de la persona y queda visible en el panel admin para fines de auditoría.
- El registro **nunca se elimina automáticamente** de la base de datos.

---

## Información que maneja el sistema

| Entidad | Qué representa |
|---|---|
| **Capacitados** | Personas que han recibido cursos (nombre, documento, correo, teléfono, RH, horas acumuladas). |
| **Cursos** | Programas de capacitación ofrecidos (nombre, descripción, duración, intensidad horaria, imagen, categoría). |
| **Categorías** | Agrupaciones temáticas de cursos. |
| **Certificados** | Documentos emitidos: capacitado, curso, código único, fechas de emisión/vencimiento, intensidad horaria, modalidad (virtual/presencial), PDF, estado (activo/inactivo). |
| **Usuarios** | Empleados del instituto con acceso al panel (admin / capacitador). |
| **Mensajes** | Mensajes recibidos por el formulario de contacto público. |
| **Configuración del sitio** | Datos institucionales: nombre, descripción, teléfono, correo, dirección, WhatsApp, redes sociales, logo y plantilla de certificado. |

---

## Seguridad Implementada

- **Tokens CSRF** en todos los formularios para prevenir falsificación de solicitudes.
- **Límites de intentos (rate limiting)**:
  - Login: 5 intentos por IP y por correo.
  - Formulario de contacto: 3 envíos por minuto.
  - Consulta y verificación de certificados: 10 solicitudes por minuto.
- **reCAPTCHA v2** en el formulario de contacto público, validado contra el servidor de Google.
- **Cabeceras de seguridad** en todas las respuestas del sitio: política de seguridad de contenido (CSP), HSTS (HTTPS forzado en producción), protección contra clickjacking, y otras.
- **Roles y permisos** estrictos en el panel administrativo, validados en cada solicitud.
- **Enlaces firmados y temporales** para la descarga de PDFs desde el sitio público (no son URLs públicas permanentes).
- **Validación de archivos**: solo se aceptan PDFs (certificados) e imágenes en formatos permitidos (cursos), con límite de tamaño.
- **Datos sensibles protegidos**: las credenciales de base de datos, claves de reCAPTCHA, etc. se guardan en el archivo de configuración del servidor (`.env`), nunca en el código fuente.

---

## Mantenimiento y Tareas Automáticas

- **Limpieza de PDFs vencidos**: una tarea programada (`certificados:limpiar-pdfs-vencidos`) revisa diariamente los certificados vencidos hace más de 1 año (es decir, 2 años desde su emisión) y elimina **únicamente el archivo PDF** del servidor para liberar espacio. El registro del certificado se conserva siempre — sigue siendo verificable y cuenta para las horas capacitadas. La tarea se ejecuta mediante el programador de Laravel (`schedule:run`), configurado en el servidor como tarea cron diaria.
- **Respaldos (backups)**: se realizan copias de seguridad periódicas de la base de datos, certificados PDF e imágenes del sitio mediante un script automático en el servidor, complementadas con copias locales de respaldo.

---

## Requisitos y Compatibilidad

- **Navegadores soportados**: versiones recientes de Chrome, Edge, Firefox y Safari (escritorio y móvil).
- **Diseño responsivo**: tanto el sitio público como el panel administrativo se adaptan a computadores, tablets y celulares.
- **Conexión a internet**: requerida para el uso del sitio público y del panel administrativo (no funciona sin conexión).
- **Cuenta de correo**: necesaria para la recuperación de contraseñas y, opcionalmente, para notificaciones del sistema.

---

## Funcionalidades Pendientes / A Futuro

Estas opciones existen como referencia en el sistema pero **aún no están activas** (muestran un mensaje de "próximamente"):

- **Importación masiva de capacitados** desde un archivo Excel, junto con una plantilla descargable.
- **Generación masiva de certificados** para varios capacitados a la vez.

> Estas funcionalidades forman parte de una segunda fase del proyecto, a definir con el cliente.

---

## Accesos y Datos Técnicos

- **Tecnología**: PHP / Laravel, base de datos MySQL, Tailwind CSS.
- **Hospedaje (producción)**: Hostinger.
- **Sitio web**: `https://institutoedcsst.com`
- **Acceso al panel administrativo**: `https://institutoedcsst.com/admin` (requiere usuario y contraseña creados por un administrador).
- **Tareas programadas**: ejecutadas mediante el programador de tareas (cron) de Hostinger, que invoca periódicamente el programador interno de Laravel.
- **Respaldo de archivos**: certificados en PDF e imágenes se almacenan en el servidor, fuera del acceso público directo, y se sirven mediante enlaces controlados por el sistema.

---

## Registro de Cambios

### 2026-06-10 — Galería con vista ampliada en "Nosotros"

**Página "Nosotros" (`/nosotros`)**
- Se mejoró la sección "Nuestro portafolio de cursos": título y etiqueta con el mismo estilo dorado del resto del sitio, animaciones de aparición y efecto de zoom al pasar el mouse sobre las imágenes.
- Al hacer clic en cualquier imagen del portafolio, se abre una vista ampliada (lightbox) a pantalla completa. Se cierra haciendo clic fuera de la imagen, con el botón "X" o con la tecla Escape.
- Se agregó la regla `[x-cloak]` en `resources/css/app.css` para evitar parpadeos de elementos controlados por Alpine.js mientras la página carga.

### 2026-06-10 — Tarjetas de cursos con efecto flip y ajuste de CSP

**Catálogo de cursos (`/cursos`)**
- Se rediseñaron las tarjetas de curso: ahora muestran solo nombre, imagen y duración, y al hacer clic/tap se voltean (efecto 3D) para mostrar la descripción completa, la intensidad horaria y un botón "Solicitar información".
- Implementado con Alpine.js (ya incluido en el proyecto) y CSS, sin dependencias nuevas. Funciona igual en escritorio y móvil (interacción por clic/tap, no por hover).

**Ajuste de seguridad (CSP)**
- Se agregó `'unsafe-eval'` a la directiva `script-src` de la Content Security Policy (`app/Http/Middleware/SecurityHeaders.php`). Es requerido por Alpine.js para evaluar sus directivas (`x-data`, `:class`, `@click`, etc.). Sin este cambio, las interacciones con Alpine (incluyendo menús desplegables existentes) no se ejecutaban.

**Mantenimiento de assets**
- Se eliminó un archivo `public/hot` obsoleto (residuo de una sesión de desarrollo con `npm run dev`) que hacía que el sitio intentara cargar los assets desde el servidor de Vite en lugar de los archivos compilados, y se recompilaron los assets de producción (`npm run build`).

### 2026-06-10 — Documentación del código
- Se agregaron comentarios explicativos en el código del backend (modelos, controladores, validaciones, middleware y rutas) para facilitar el mantenimiento futuro y la entrega de documentación técnica al cliente.

### 2026-06-09 / 2026-06-10 — Seguridad, estilos y mantenimiento

**Seguridad y estilos (CSP)**
- Se corrigió la política de seguridad de contenido (CSP) que bloqueaba estilos de la aplicación.
- Se agregó la fuente Figtree y se ajustaron las cabeceras de seguridad para que los estilos carguen correctamente sin afectar la protección del sitio.

**Formulario de contacto (reCAPTCHA)**
- Se solucionó un error que impedía el envío del formulario de contacto en producción.
- Causa: las claves de reCAPTCHA configuradas eran inválidas. Se generaron nuevas claves (tipo "casilla de verificación") y se actualizaron en el servidor.

**Limpieza automática de PDFs vencidos**
- Se creó un proceso automático que, una vez al año después del vencimiento de un certificado (2 años desde su emisión), elimina únicamente el archivo PDF del servidor para liberar espacio.
- El registro del certificado se conserva siempre: sigue siendo verificable y cuenta para las horas capacitadas de la persona.
- Se configuró en Hostinger (hPanel → Avanzado → Cron Jobs) la tarea programada que ejecuta este proceso diariamente. **Implementado y activo en producción.**

**Backup**
- Se realizó respaldo completo (base de datos, certificados PDF e imágenes) antes de aplicar los cambios anteriores.

### 2026-06-16 — Correcciones de seguridad (auditoría interna)

**Path traversal en generación de PDF (`App\Services\CertificadoPdfService`)**
- Se corrigió una vulnerabilidad potencial: la ruta de la plantilla de certificado (almacenada en BD) se usaba directamente en `file_exists()` y `setSourceFile()` sin validar que apuntara dentro del directorio permitido. Un valor malicioso podría haber revelado existencia de archivos del sistema o permitido leer archivos arbitrarios del servidor.
- Solución: se usa `realpath()` para resolver la ruta canónica y se verifica con `str_starts_with()` que esté estrictamente dentro de `storage/app/public/` antes de usarla.

**Mensaje de éxito incorrecto al editar certificado (`CertificadoController::update`)**
- El mensaje de confirmación siempre decía "PDF regenerado correctamente" aunque el usuario hubiera subido un PDF manual (en cuyo caso no se regenera, sino que se almacena el archivo subido).
- Solución: el mensaje se condiciona según si se subió un archivo — "Certificado actualizado correctamente." cuando hay PDF manual, "Certificado actualizado y PDF regenerado correctamente." cuando se regeneró desde la plantilla.

**Validación contradictoria en certificados masivos (`CertificadoController::generarMasivos`)**
- La validación aplicaba `required_with` y `nullable` simultáneamente sobre los campos de todas las filas (incluidas y no incluidas), generando comportamiento indefinido: si una fila no estaba marcada, sus campos eran "requeridos" pero también "nulos aceptables".
- Solución: se filtra primero el array de solicitudes a solo las filas marcadas con `incluir`, se retorna temprano si ninguna fue seleccionada, y se valida únicamente ese subconjunto con reglas limpias (`required` sin `nullable` en campos obligatorios) usando `Validator::make()` para no alterar el request original. Los mensajes de error ahora son específicos y orientados al usuario.

**Botones destructivos ocultos para capacitadores (`admin/certificados/show.blade.php`)**
- Los botones "Editar", "Desactivar/Reactivar" y "Eliminar" eran visibles para usuarios con rol `capacitador`, quienes recibían un error 403 al pulsarlos. Las rutas correspondientes ya estaban protegidas por middleware, pero la vista no reflejaba la restricción.
- Solución: los tres botones se agruparon bajo un único `@if(auth()->user()->isAdmin())`, dejando visible solo "Ver PDF" para todos los roles autenticados.

**Desacoplamiento transacción DB / escritura de PDF en certificados masivos (`GeneracionMasivaCertificadosService`)**
- La generación del PDF ocurría dentro de `DB::transaction()`. Si `$solicitud->update()` fallaba y hacía rollback, el PDF ya estaba escrito en disco como archivo huérfano (sin certificado asociado en BD).
- Solución: se divide el proceso en dos fases. Fase 1 (dentro de la transacción): crear el certificado y marcar la solicitud como procesada de forma atómica. Fase 2 (fuera de la transacción): generar y guardar el PDF. Si la fase 2 falla, el certificado queda sin PDF pero `verPdf()` lo regenera al vuelo.

**Optimización de visualización de PDF (`CertificadoController::verPdf`)**
- `verPdf()` regeneraba el PDF con FPDI (carga de fuentes + plantilla) en cada request, lo que era costoso en CPU y podía ser abusado por clicks repetidos.
- Solución: si el certificado ya tiene `archivo_pdf` almacenado en disco, se sirve directamente desde el disco. Solo se regenera cuando no existe archivo (ej: certificados muy antiguos sin PDF guardado).

**Race condition en código único de certificados (`CertificadoController::store`)**
- Se corrigió una condición de carrera: al crear un certificado sin código manual, el sistema creaba el registro con un UUID temporal y luego lo sobreescribía con el código `EDCSST-YYYY-00001`. Si `saveQuietly()` fallaba (error de memoria, timeout, excepción al generar PDF), el certificado quedaba persistido en BD con el UUID, violando el formato oficial.
- Solución: se envuelve `Certificado::create()` + generación de código + `saveQuietly()` en una `DB::transaction()`, de modo que cualquier fallo revierte el registro completo y la BD queda limpia.

### 2026-06-15 — Generación de PDF sobre la plantilla oficial (overlay)

- Se cambió la forma de generar el PDF del certificado: en lugar de construir el diseño completo con dompdf, ahora se usa la plantilla oficial del instituto (`storage/app/public/plantillas/certificado.pdf`, registrada en `configuracion_sitio.plantilla_certificado`) como fondo y se escriben encima, con FPDI/FPDF, los datos variables de cada certificado: nombre, documento, curso, modalidad, intensidad horaria, fechas de emisión/vencimiento y código único.
- Las coordenadas de cada campo sobre la plantilla están centralizadas en `config/certificado_plantilla.php` para poder ajustarlas sin tocar el código del servicio.
- `App\Services\CertificadoPdfService` mantiene como respaldo la plantilla Blade anterior (`resources/views/pdf/certificado.blade.php` con dompdf) para el caso en que no haya plantilla configurada o el archivo no exista.
- No se implementó código QR de verificación (queda pendiente para una fase futura).
