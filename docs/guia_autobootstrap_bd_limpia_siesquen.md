# 📘 Guía de Auto-Bootstrap en Base de Datos Limpia - Siesquen

> **Módulo:** Auto-Bootstrap Idempotente & Multi-Tenant Onboarding  
> **Proyecto:** Siesquen (`siesquen`)  
> **Ramas Asociadas:** `ecommerce/siesquen` y `ecommerce/siesquen-produccion`  
> **Fecha de Despliegue:** Noviembre 2026  

---

## 🎯 1. Propósito y Validación en Siesquen

Siesquen se utiliza como entorno de prueba y despliegue real del mecanismo **Auto-Bootstrap Idempotente** sobre una **base de datos 100% limpia (0 tablas)**:
1. **Sin volcados manuales:** No se exporta ni importa ningún archivo `.sql` desde phpMyAdmin.
2. **Cero SQL manual:** Se elimina la necesidad de ejecutar consultas manuales para asociar plantillas o crear usuarios.
3. **Migraciones aditivas sin errores:** Gracias al fix en migraciones históricas (commit `e8c22f3`), `migrate --force` ejecuta desde la migración 0001 hasta la última sin detenerse por columnas inexistentes.
4. **Construcción automática:** Al detectar que no hay ningún sitio registrado, el comando `store:bootstrap` crea:
   - Monedas: Soles (PEN - S/) y Dólares (USD - $).
   - Plantillas: `Ecomer`, `Tortas`, `Corporativa` con sus secciones.
   - Rol: `super_admin` con guardia `web`.
   - Usuario: Administrador con los datos configurados en `ADMIN_EMAIL` y `ADMIN_PASSWORD` del `.env`.
   - Tienda y Sitio: `Site` publicado con `APP_NAME="Siesquen"`, slug `siesquen` y plantilla universal `Ecomer`.

---

## ⚙️ 2. Variables de Entorno Requeridas en `public_html/.env`

Asegúrate de que el archivo `.env` en cPanel tenga definidas las siguientes variables:

```env
APP_NAME="Siesquen"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://siesquen.pe

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usuario_siesquen_db
DB_USERNAME=usuario_siesquen_user
DB_PASSWORD="PasswordActual"

# Credenciales de Administrador que el bootstrap creará
ADMIN_NAME="Administrador Siesquen"
ADMIN_EMAIL="admin@siesquen.pe"
ADMIN_PASSWORD="TuPasswordSeguro2026!"

# Token de seguridad para el Webhook post-deploy
DEPLOY_TOKEN="deploy_siesquen_8a1b2c3d4e5f67"
```

---

## 🚀 3. Procedimiento de Despliegue

### Paso 1: Vaciado de Tablas en phpMyAdmin (Completado)
Se eliminaron todas las tablas existentes en la base de datos de Siesquen. La BD permanece con 0 tablas y la conexión de MySQL se mantiene activa.

### Paso 2: Push a Producción
```bash
git push origin ecommerce/siesquen:ecommerce/siesquen-produccion
```

### Paso 3: Monitoreo CI/CD (GitHub Actions)
El workflow ejecutará automáticamente:
1. Gatekeeper tests.
2. Vite build (frontend compilado).
3. FTPS incremental upload.
4. Webhook remoto de post-deploy:
   - `php artisan migrate --force`
   - `php artisan store:bootstrap`
   - `optimize:clear` y refresco de cachés.

### Paso 4: Verificación
- Acceder a la URL principal y verificar respuesta HTTP 200 con la plantilla `Ecomer` cargada.
- Ingresar a `/admin` con `ADMIN_EMAIL` y `ADMIN_PASSWORD`.
