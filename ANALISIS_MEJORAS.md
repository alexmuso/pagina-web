# Análisis del proyecto `mis-propios-medios` y mejoras recomendadas

## Resumen ejecutivo
El proyecto funciona como una web de agendamiento con panel administrativo en PHP + MySQL. Sin embargo, hay riesgos importantes de **seguridad**, algunos **errores funcionales** y oportunidades claras de mejora en **mantenibilidad** y **UX**.

---

## Mejoras prioritarias (alto impacto)

### 1) Seguridad de autenticación (reemplazar `md5`)
- En `login.php` se compara la contraseña con `md5(...)`.
- `md5` no es seguro para contraseñas actuales.
- Recomendación:
  - Migrar a `password_hash()` para almacenar contraseñas.
  - Verificar login con `password_verify()`.

**Referencia:** `mis-propios-medios/Mis propios medios/login.php` (línea 7).

### 2) Control de acceso incompleto en páginas sensibles
- `admin.php` sí valida sesión (`$_SESSION['admin']`), pero `ver_citas.php` y `eliminar_cita.php` no.
- Cualquier usuario podría consultar/eliminar citas si conoce la URL.
- Recomendación:
  - Exigir sesión de admin en **todas** las rutas administrativas.
  - Bloquear acceso directo a acciones críticas.

**Referencia:**
- `mis-propios-medios/Mis propios medios/admin.php` (líneas 2–6).
- `mis-propios-medios/Mis propios medios/ver_citas.php` (no hay `session_start` ni validación).
- `mis-propios-medios/Mis propios medios/eliminar_cita.php` (no hay validación de sesión).

### 3) Borrado por GET y sin protección CSRF
- El borrado de citas se activa por URL (`?id=...`) vía GET.
- Esto facilita eliminaciones involuntarias o ataques CSRF.
- Recomendación:
  - Cambiar eliminación a POST.
  - Añadir token CSRF y validarlo en servidor.

**Referencia:**
- `mis-propios-medios/Mis propios medios/eliminar_cita.php` (líneas 4–9).
- `mis-propios-medios/Mis propios medios/admin.php` (línea 123).

### 4) Error funcional en rutas de eliminación
- En `admin.php` y `ver_citas.php` se usa `eliminar.php`, pero el archivo existente es `eliminar_cita.php`.
- Resultado probable: enlace roto para eliminar citas.
- Recomendación:
  - Unificar nombre de archivo/ruta en todas las vistas.

**Referencia:**
- `mis-propios-medios/Mis propios medios/admin.php` (línea 123).
- `mis-propios-medios/Mis propios medios/ver_citas.php` (línea 53).
- Archivo real: `mis-propios-medios/Mis propios medios/eliminar_cita.php`.

### 5) Riesgo de XSS en mensaje de confirmación
- En `recibir.php`, los valores de usuario (`$nombre`, `$apellido`) se imprimen sin escapar dentro del HTML de respuesta.
- Recomendación:
  - Escapar salida con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

**Referencia:** `mis-propios-medios/Mis propios medios/recibir.php` (línea 39).

### 6) Credenciales de base de datos embebidas
- `conexion.php` incluye host/usuario/password en texto plano.
- Recomendación:
  - Mover credenciales a variables de entorno (`.env`) y no versionar secretos.

**Referencia:** `mis-propios-medios/Mis propios medios/conexion.php` (líneas 2–5).

---

## Mejoras de calidad y mantenibilidad (impacto medio)

### 7) Validación de entrada insuficiente
- Aunque se usan consultas preparadas (bien), no hay validación robusta de formato/tamaño de entradas en backend.
- Recomendación:
  - Validar longitud mínima/máxima de `nombre`, `apellido`, `descripcion`.
  - Validar patrón de `documento`, `correo`, `telefono`.

**Referencia:** `mis-propios-medios/Mis propios medios/recibir.php` (líneas 5–11).

### 8) Duplicación de páginas HTML/PHP
- Existen versiones duplicadas (`index.html`/`index.php`, `formulario.html`/`formulario.php`) que pueden desincronizarse.
- Recomendación:
  - Mantener una sola versión activa por pantalla.
  - Si se queda PHP, retirar/copiar redirecciones de las `.html`.

**Referencia:**
- `mis-propios-medios/index.html`.
- `mis-propios-medios/formulario.html`.
- `mis-propios-medios/Mis propios medios/index.php`.
- `mis-propios-medios/Mis propios medios/formulario.php`.

### 9) Estilos inline y sin estructura reutilizable
- El CSS está embebido en cada archivo.
- Recomendación:
  - Extraer a archivos `css/` comunes para facilitar mantenimiento.

**Referencia:** estilos dentro de `index.php`, `formulario.php`, `login.php`, `admin.php`, `ver_citas.php`.

### 10) Flujo post-envío mejorable (PRG)
- Tras guardar cita se imprime HTML directamente.
- Recomendación:
  - Implementar patrón Post/Redirect/Get para evitar reenvíos al refrescar.

**Referencia:** `mis-propios-medios/Mis propios medios/recibir.php` (líneas 28–43).

---

## Mejoras de UX y accesibilidad (impacto medio/bajo)

### 11) Errores de ortografía y consistencia de textos
- Hay textos con errores y diferencias entre vistas (por ejemplo “Confeciones”, “Bienvedidos”).
- Recomendación:
  - Revisar microcopy para mejorar percepción profesional.

**Referencia:** `mis-propios-medios/index.html` (encabezado/banner).

### 12) Estructura semántica HTML mejorable
- En `formulario.php` se usa `<li>` fuera de una lista.
- Recomendación:
  - Reemplazar por `<p>` o contenedor semántico adecuado.

**Referencia:** `mis-propios-medios/Mis propios medios/formulario.php` (línea 80).

---

## Propuesta de plan incremental
1. **Semana 1 (seguridad crítica):** autenticación segura, protección de rutas admin, eliminar por POST + CSRF, corregir rutas rotas.
2. **Semana 2 (calidad):** validación backend, escape centralizado de salida, PRG en formularios.
3. **Semana 3 (mantenibilidad/UX):** consolidar páginas duplicadas, extraer CSS, limpieza de textos y semántica HTML.

---

## Nota positiva
- Se usa PDO con sentencias preparadas para inserciones y consultas, lo cual ya reduce bastante el riesgo de inyección SQL.
