# ECOMER - CMS Multi-Tenant & E-Commerce
## Contexto e Instrucciones para Agentes de IA

### 1. Resumen del Proyecto
Plataforma SaaS Multi-Tenant y E-Commerce modular que permite instanciar y personalizar múltiples sitios web desde un único código base y base de datos, administrado mediante Filament PHP.

### 2. Stack Tecnológico
- **Backend:** PHP 8.4+ / Laravel 13.x / Eloquent ORM.
- **Panel Administrativo:** Filament PHP v5 con Filament Shield (Spatie Laravel Permission).
- **Frontend:** React 19, Inertia.js v3, Tailwind CSS v4, Vite 8.
- **Librerías Frontend:** Zustand (carrito y estado global), Framer Motion (animaciones), Swiper (sliders), Lucide / React-icons.
- **Generación y Medios:** Barryvdh DomPDF (catálogos en PDF), Intervention Image, WebP-Convert.

### 3. Arquitectura y Modelos Clave
- **Multi-Tenant / Enrutamiento:** `SitePageController.php` resuelve el sitio por dominio HTTP o slug (`/{dominio}/{seccion}`).
- **Motor CMS Dinámico:**
  - `Plantilla`: Tema visual base (`Ecomer`, `Tortas`, `Corporativa`, `Anuncio`).
  - `Seccion`: Bloques o páginas activas (`Inicio`, `Nosotros`, `Productos`, `Servicios`, `Contacto`, etc.).
  - `Pregunta`: Campos configurables del diseño o contenido.
  - `Respuesta`: Valores personalizados por Sitio (`Site`), con fallback a la plantilla base.
  - Estilos dinámicos inyectados como variables CSS (`--color-primario`, `--color-secundario`, `--tipografia-titulos`, `--tipografia-texto`).
- **Módulo E-Commerce:**
  - `Producto`, `Categoria`, `Subcategoria`, `Marca`.
  - `Variante`, `Talla`, `Color`.
  - Multi-tienda y stock por almacén: `ProductoTienda`.
  - Multi-moneda: Soles, Dólares y Euros (`Moneda`).

### 4. Componentes y Plantillas Frontend
- Ubicación principal: `resources/js/pages/sites/`
  - `Ecomer/`: Tienda virtual completa con navegación, catálogo, filtros, sliders, carrito flotante.
  - `Tortas/`: Catálogo y pedidos para pastelería y repostería.
  - `Corporativa/`: Sitio institucional y servicios.
  - `Anuncio/`: Landing page de conversión.
- **Tarjetas de Producto:**
  - Soporte de Crossfade a 2ª imagen en hover (`prod.imagenes`).
  - Inversión dinámica de colores en botones (`--color-primario` y `--color-secundario`).

### 5. Comandos Frecuentes
- Compilar frontend: `npm run build`
- Modo desarrollo frontend: `npm run dev`
- Limpieza de caché Laravel: `php artisan optimize:clear`
- Ejecutar servidor local: `php artisan serve` o `deploy/start-ecomer-8081.bat`

### 6. Convención de Commits (Conventional Commits)
Todos los commits deben seguir el estándar de Conventional Commits con la **descripción en español**:

- **Formato general:**
  `<tipo>(<alcance_opcional>): <descripción en español>`

- **Tipos permitidos:**
  - `feat`: Nueva funcionalidad para el usuario (ej. `feat(catalogo): agregar crossfade de imagenes en hover`).
  - `fix`: Corrección de un error o bug (ej. `fix(carrito): resolver calculo incorrecto de totales en soles`).
  - `style`: Cambios de estilos visuales, CSS, formato o espaciado sin alterar lógica (ej. `style(boton): invertir colores en hover segun variables del panel`).
  - `refactor`: Reestructuración de código sin agregar funcionalidad ni corregir errores (ej. `refactor(productos): modularizar tarjeta de producto`).
  - `perf`: Mejoras de rendimiento o tiempos de carga (ej. `perf(imagenes): optimizar carga diferida de galeria`).
  - `chore`: Tareas de mantenimiento, dependencias o configuración (ej. `chore(deps): actualizar dependencias de vite y laravel`).
  - `docs`: Cambios o adiciones en documentación (ej. `docs: actualizar guia de despliegue en cpanel`).
  - `test`: Creación o modificación de pruebas (ej. `test: anadir pruebas para endpoints de api`).

- **Reglas obligatorias:**
  - El mensaje / descripción debe estar redactado en **español**.
  - Redacción en tiempo presente e infinitivo o imperativo claro (ej. `feat: agregar...` o `fix: corregir...`).
  - No usar puntos finales en el título del commit.

### 7. Directrices de Rendimiento al Agregar Nuevas Secciones (Performance & Core Web Vitals)
Para mantener las métricas óptimas de rendimiento (Lighthouse 80–90+, bajo TBT y LCP rápido) al crear o integrar nuevas secciones en el frontend:

- **Contención de Renderizado (`content-visibility`):**
  - Toda nueva sección que se ubique *below-the-fold* (a partir de la 3ª sección en adelante) debe envolverse con `<div className="content-auto">` para diferir el cálculo del layout y pintado en el navegador hasta que el usuario haga scroll hacia ella.
- **División de Código (Code-Splitting):**
  - Secciones con dependencias pesadas, iframes de terceros o de pie de página (ej. videos de TikTok, reproductores, mapas interactivos pesados) deben cargarse con `React.lazy()` dentro de un bloque `<Suspense fallback={null}>`.
- **Optimización y Carga de Medios:**
  - Todas las imágenes debajo del primer viewport deben incluir `loading="lazy"` y `decoding="async"`.
  - Priorizar formato **WebP** y mantener el peso de las imágenes individuales por debajo de **150 KB** mediante el optimizador del panel Filament (`ImageOptimizerService`).
  - Emplear etiquetas `<picture>` con puntos de interrupción responsivos (`min-width: 1024px`, `min-width: 640px`) para entregar versiones ligeras a dispositivos móviles.
- **Consultas a Base de Datos y Payload:**
  - Limitar la cantidad de productos o ítems mostrados por bloque (recomendado entre 8 y 16 ítems) para no sobrecargar el JSON inicial ni elevar el TTFB (Time to First Byte).
  - Asegurar *eager loading* en relaciones Eloquent para prevenir el problema N+1.
- **Scripts y Widgets de Terceros:**
  - Evitar la inserción directa de scripts o iframes sin diferir; implementar carga bajo demanda (por ejemplo, al hacer clic en "Ver mapa" o al entrar en el viewport con IntersectionObserver).

### 8. Estándares de Arquitectura y Clean Code
Directrices obligatorias para el desarrollo, refactorización y mantenimiento del código:

- **Separación de Lógica y Presentación (SoC):**
  - Los componentes React (`.jsx` / `.tsx`) deben enfocarse en el renderizado y la experiencia visual.
  - La normalización de datos complejos, deduplicación (con `Map`/`Set`) o filtrados intensivos deben encapsularse en Custom Hooks (`hooks/`) o funciones puras auxiliares (`utils/`).
- **Principio DRY en Consumo del CMS:**
  - Evitar reinventar helpers locales de búsqueda (`getItem` / `getValor`) dentro de cada subcomponente. Utilizar utilitarios compartidos o selectores tipados para extraer títulos, descripciones, arrays e imágenes responsivas de `seccionData.contenido`.
- **Controladores y Validación en Backend (Laravel):**
  - No ubicar lógica de negocio ni closures anónimos en archivos de rutas (`routes/api.php` o `routes/web.php`).
  - Cada endpoint debe contar con su Controlador dedicado y validación mediante `FormRequest` desacoplados.
- **Gestión de Estado Aislado:**
  - Mantener la separación de estado global con Zustand (ej. `useCartStore`, estado de modales y filtros) sin recurrir al *prop drilling* excesivo.
- **Tipado Progresivo y Contratos:**
  - Aprovechar la generación automática de TypeScript (`Wayfinder`) para llamadas a rutas y controladores.
  - Al crear nuevos componentes o servicios, preferir interfaces tipadas que definan claramente la estructura esperada de los modelos (ej. `Producto`, `Site`, `Seccion`).




