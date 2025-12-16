# 📦 Módulo de Intranet - SCNexus

Este módulo replica la funcionalidad de la Intranet desarrollada en PHP, migrándola a Vue.js siguiendo la estructura modular del proyecto.

## 📁 Estructura del Módulo

```
src/modules/intranet/
├── views/
│   ├── IntranetHomeView.vue          # Página principal con carrusel
│   ├── DocumentosView.vue            # Lista de documentos
│   ├── DirectorioView.vue            # Directorio telefónico
│   ├── ServiciosView.vue             # Gestión de servicios
│   └── ExploradorCarpetasView.vue    # Explorador de archivos
├── components/
│   ├── CarruselViewer.vue            # Componente carrusel de imágenes
│   ├── FeatureCard.vue               # Tarjeta de características
│   ├── DocumentosTable.vue            # Tabla de documentos
│   ├── DirectorioTable.vue           # Tabla de directorio
│   ├── ServiciosTable.vue            # Tabla de servicios
│   └── FileExplorer.vue              # Explorador de archivos
├── services/
│   └── intranetApi.ts                # Servicios API
├── stores/
│   ├── documentosStore.ts            # Estado de documentos
│   ├── directorioStore.ts            # Estado del directorio
│   ├── serviciosStore.ts             # Estado de servicios
│   ├── archivosStore.ts              # Estado del explorador
│   └── carruselStore.ts              # Estado del carrusel
├── router.ts                          # Rutas del módulo
├── types.ts                           # Tipos TypeScript
└── README.md                          # Esta documentación
```

## 🎯 Funcionalidades

### 1. Página Principal (IntranetHomeView)
- Carrusel de imágenes con navegación automática
- Tarjetas de acceso rápido a las secciones principales
- Diseño responsive y moderno

### 2. Documentos (DocumentosView)
- Lista de documentos corporativos
- Filtros por departamento, categoría y tipo
- Visualización y descarga de documentos
- Integración con DataTables para búsqueda y paginación
- Acceso rápido al explorador de carpetas de Talento Humano

### 3. Directorio (DirectorioView)
- Lista de contactos empresariales
- Información de área, colaborador y extensión
- Búsqueda y filtrado con DataTables
- Acceso a administración del directorio (requiere permisos)

### 4. Servicios (ServiciosView)
- Lista de servicios empresariales
- Enlaces externos a servicios
- Animaciones interactivas
- Incluye enlace hardcodeado a Panel Bendo

### 5. Explorador de Carpetas (ExploradorCarpetasView)
- Navegación por carpetas y archivos
- Búsqueda recursiva de archivos
- Visualización de PDFs en modal
- Preview de imágenes
- Iconos según tipo de archivo
- Navegación por breadcrumbs

## 🔌 API Endpoints Requeridos

El módulo espera los siguientes endpoints en el backend:

### Documentos
```
GET    /api/intranet/documentos              # Listar documentos
GET    /api/intranet/documentos/{id}/descargar # Descargar documento
GET    /api/intranet/documentos/{id}/ver      # Ver documento
```

### Directorio
```
GET    /api/intranet/directorio               # Listar contactos
```

### Servicios
```
GET    /api/intranet/servicios                # Listar servicios
```

### Archivos
```
GET    /api/intranet/archivos?path={path}&search={search}  # Listar archivos
GET    /api/intranet/archivos/ver?file={ruta}  # Ver archivo
```

### Carrusel
```
GET    /api/intranet/carrusel/imagenes        # Listar imágenes del carrusel
```

## 🔐 Permisos Requeridos

El módulo utiliza los siguientes permisos:

- `intranet.view` - Acceso general a la intranet
- `intranet.documentos.view` - Ver documentos
- `intranet.directorio.view` - Ver directorio
- `intranet.servicios.view` - Ver servicios
- `intranet.archivos.view` - Acceder al explorador de archivos
- `intranet.admin` - Administración completa (opcional)

## 🚀 Uso

### Importar el módulo en el router principal

```typescript
// src/router/index.ts
import intranetRoutes from '../modules/intranet/router'

// Agregar en children del MainLayout
children: [
  // ... otras rutas
  ...intranetRoutes,
]
```

### Agregar al menú del sidebar

```vue
<!-- src/layouts/MainLayout.vue -->
<router-link
  to="/intranet"
  class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-700 transition-colors"
  :class="{ 'bg-gray-700': $route.path.startsWith('/intranet') }"
>
  <HomeIcon class="h-5 w-5 flex-shrink-0" />
  <span v-show="!isSidebarCollapsed">Intranet</span>
</router-link>
```

## 🎨 Diseño

El módulo replica fielmente el diseño de la versión PHP:

- **Colores**: Gradiente azul (#64b5f6 a #1976d2)
- **Tipografía**: Mismo estilo y tamaños
- **Componentes**: Tablas con DataTables, modales, tooltips
- **Animaciones**: Efectos hover, transiciones suaves
- **Modo oscuro**: Soporte completo para tema oscuro/claro

## 📊 Dependencias

- Vue 3
- Vue Router
- Pinia
- Axios
- DataTables (CDN)
- Bootstrap Icons (CDN)
- Bootstrap 5 (CDN)

## 🔄 Migración desde PHP

Este módulo está diseñado para reemplazar completamente la funcionalidad PHP:

| PHP Original | Vue.js Equivalente |
|-------------|-------------------|
| `documentos.php` | `DocumentosView.vue` |
| `directorio.php` | `DirectorioView.vue` |
| `servicios.php` | `ServiciosView.vue` |
| `explorador_carpetas_th.php` | `ExploradorCarpetasView.vue` |
| `index.php` | `IntranetHomeView.vue` |

## 📝 Notas de Desarrollo

1. **DataTables**: Se carga desde CDN, asegúrate de incluir los scripts en el HTML principal
2. **Bootstrap Icons**: Requiere el CDN de Bootstrap Icons para los iconos
3. **Autenticación**: Usa el sistema de autenticación centralizado del proyecto (JWT/Pinia)
4. **Rutas**: Todas las rutas requieren autenticación (`requiresAuth: true`)
5. **Permisos**: Verificar permisos en el router guard antes de acceder

## 🐛 Solución de Problemas

### DataTables no se inicializa
- Verifica que jQuery y DataTables estén cargados desde CDN
- Asegúrate de que el componente esté montado antes de inicializar

### Imágenes del carrusel no cargan
- Verifica que el endpoint `/api/intranet/carrusel/imagenes` retorne las rutas correctas
- Las rutas deben ser accesibles públicamente o usar proxy

### PDFs no se abren
- Verifica que el endpoint de visualización retorne una URL válida
- Asegúrate de que los archivos PDF estén accesibles

## 📚 Referencias

- [Guía de Módulos](../MODULOS.md)
- [Estructura Modular](../ESTRUCTURA-MODULAR.md)
- Documentación del proyecto PHP original en `/intranet/`




