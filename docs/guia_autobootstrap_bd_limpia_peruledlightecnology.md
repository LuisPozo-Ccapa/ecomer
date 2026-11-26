# 📘 Guía de Auto-Bootstrap en Base de Datos Limpia y Despliegue de Peru Led Light Tecnology

> **Módulo:** Auto-Bootstrap Idempotente & Multi-Tenant Onboarding  
> **Proyecto en Standby:** Peru Led Light Tecnology (`peruledlightecnology`)  
> **Ramas Asociadas:** `ecommerce/peruledlightecnology` y `ecommerce/peruledlightecnology-produccion`  
> **Fecha de Implementación:** Noviembre 2026  

---

## 🎯 1. Problema Resuelto y Propósito

### El Problema Anterior
Para crear o desplegar una nueva tienda en un nuevo servidor cPanel, el flujo requería:
1. Crear una base de datos en cPanel.
2. **Exportar manualmente la base de datos de una tienda existente** (ej. Magnus o Lismercorp) desde phpMyAdmin.
3. **Importar ese archivo `.sql`** en la nueva base de datos.
4. **Desventajas:** La nueva tienda nacía "sucia", arrastrando productos, categorías, imágenes que no existían físicamente en el nuevo servidor (generando errores 404 en imágenes) y credenciales ajenas. Si solo se ejecutaban las migraciones en blanco, la web fallaba con HTTP 404 porque no existía ningún `Site` ni usuario administrador.

### La Solución Arquitectónica
Se implementó un sistema de **Auto-Bootstrap Idempotente** que permite desplegar en **bases de datos 100% vacías**:
- En el despliegue, tras ejecutar `php artisan migrate --force`, el sistema verifica si la base de datos tiene alguna tienda configurada.
- **Si está vacía:** Siembra de inmediato las monedas oficiales (PEN y USD), las plantillas (`Ecomer`, `Tortas`, `Corporativa`), los roles y permisos de Filament Shield (`super_admin`), el usuario administrador leído del `.env`, y publica el `Site` inicial con el `APP_NAME` del `.env` vinculado a la plantilla universal `Ecomer`.
- **Si ya existe una tienda:** El comando se auto-omite de forma segura en milisegundos (`idempotente`), protegiendo la información de cualquier sobreescritura.

---

## 🛠️ 2. Componentes Técnicos Implementados

### 2.1. Comando Artisan `store:bootstrap`
- **Ubicación:** `app/Console/Commands/BootstrapStoreCommand.php`
- **Firma:** `php artisan store:bootstrap {--force : Forzar inicialización}`
- **Responsabilidad:**
  1. Comprueba si `Site::withoutTrashed()->exists()`. Si existe y no se usa `--force`, sale con código 0.
  2. Crea `Moneda` PEN (Soles - `S/`) y USD (Dólares - `$`).
  3. Crea `Plantilla` `Ecomer` (con secciones `Hero`, `nosotros`, `productos`, `marcas`, `contacto`), `Tortas` y `Corporativa`.
  4. Crea el rol `super_admin` con guardia `web`.
  5. Crea el usuario `User` leyendo `ADMIN_NAME`, `ADMIN_EMAIL` y `ADMIN_PASSWORD` del `.env` y le asigna el rol `super_admin`.
  6. Crea el registro `Tienda` base para control de stock.
  7. Crea el registro `Site` en estado `publicado`, con slug normalizado de `config('app.name')`, plantilla `Ecomer`, monedas asociadas y estilos base limpios.

### 2.2. Integración en Webhook Post-Despliegue
- **Ubicación:** `app/Http/Controllers/DeployWebhookController.php`
- **Secuencia de tareas remotas:**
  ```php
  $tasks = [
      'migrate' => ['--force' => true],
      'store:bootstrap' => [], // <-- Se ejecuta automáticamente aquí
      'optimize:clear' => [],
      'config:cache' => [],
      'route:cache' => [],
      'view:cache' => [],
  ];
  ```

### 2.3. Pruebas Automatizadas (Gatekeeper)
- `tests/Feature/AutoBootstrapStoreTest.php`: Verifica inicialización completa sobre base de datos vacía y valida que una segunda ejecución no duplique ni sobrescriba datos.
- `tests/Feature/DeployWebhookTest.php`: Verifica que el webhook ejecute la tarea `store:bootstrap` y responda HTTP 200 OK.

---

## ⏸️ 3. Estado del Proyecto: `peruledlightecnology` (En Standby)

El proyecto quedó completamente preparado en el repositorio Git a la espera de que el cliente o proveedor entregue las credenciales del servidor cPanel:

- **Rama de Trabajo:** `ecommerce/peruledlightecnology` (subida a GitHub `origin`, en el commit que contiene todo el blindaje y el auto-bootstrap).
- **Rama de Producción Local:** `ecommerce/peruledlightecnology-produccion` (lista para hacer push cuando se configure el entorno).

---

## 🚀 4. Kit de Despliegue Rápido (Para Reanudar)

Cuando te entreguen el hosting cPanel de **Peru Led Light Tecnology**, sigue únicamente estos 4 pasos:

### Paso 1: Crear Base de Datos Vacía en cPanel
- En cPanel > **Bases de Datos MySQL**, crea una base de datos vacía y su usuario con todos los privilegios.
- **NO necesitas entrar a phpMyAdmin ni importar ningún archivo SQL.**

### Paso 2: Crear el Archivo `.env` en `public_html/` de cPanel
Copia y pega la siguiente plantilla:

```env
APP_NAME="Peru Led Light Tecnology"
APP_ENV=production
APP_KEY=base64:rFpF7d9NkDAQ2tCF8JBCHk2T11Fia3UdJs5GOC4zxmI=
APP_DEBUG=false
APP_URL=https://tudominio-peruled.com

APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_ES

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# ==========================================
# BASE DE DATOS EN LIMPIO (cPanel MySQL)
# ==========================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usuario_peruleddb
DB_USERNAME=usuario_peruledusr
DB_PASSWORD="TuPasswordDeBaseDeDatos"

# ==========================================
# USUARIO ADMINISTRADOR INICIAL (Auto-Bootstrap)
# ==========================================
ADMIN_NAME="Administrador Peru Led"
ADMIN_EMAIL="admin@tudominio-peruled.com"
ADMIN_PASSWORD="PeruLed2026!Password"

# ==========================================
# SESIÓN Y ALMACENAMIENTO
# ==========================================
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
CACHE_STORE=file

# Token de despliegue automatizado
DEPLOY_TOKEN=deploy_peruledlightecnology_4d8a1c9e2b3f75

# ==========================================
# CORREO SMTP
# ==========================================
MAIL_MAILER=smtp
MAIL_HOST=mail.tudominio-peruled.com
MAIL_PORT=465
MAIL_USERNAME=ventas@tudominio-peruled.com
MAIL_PASSWORD="TuPasswordDeCorreo"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="ventas@tudominio-peruled.com"
MAIL_FROM_NAME="Peru Led Light Tecnology"

# ==========================================
# VITE REACT
# ==========================================
VITE_APP_NAME="${APP_NAME}"
```

### Paso 3: Configurar Secretos en GitHub
En GitHub > **Settings > Environments > New environment**:
- **Nombre:** `ecommerce/peruledlightecnology-produccion`
- **Secretos:**
  - `FTP_HOST`: Servidor o IP del cPanel.
  - `FTP_USERNAME`: Usuario FTP (`deployer@tudominio-peruled.com`).
  - `FTP_PASSWORD`: Contraseña asignada a la cuenta FTP.
  - `FTP_SERVER_DIR`: `/`
  - `PROD_URL`: `https://tudominio-peruled.com` *(sin barra final)*.
  - `DEPLOY_TOKEN`: `deploy_peruledlightecnology_4d8a1c9e2b3f75`.

### Paso 4: Asegurar los dos `.htaccess` en cPanel
- `public_html/.htaccess` (redirección a `public/`).
- `public_html/public/.htaccess` (redirección a `index.php`).

### Paso 5: Lanzar el Despliegue con un Solo Comando
```bash
git push origin ecommerce/peruledlightecnology:ecommerce/peruledlightecnology-produccion
```
El pipeline de GitHub Actions compilará el frontend, subirá los archivos y el webhook ejecutará las migraciones y el auto-bootstrap. La tienda quedará 100% activa en HTTP 200 con su usuario administrador listo para entrar a `/admin`.
