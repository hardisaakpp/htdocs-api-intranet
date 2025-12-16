# 📡 API REST - Módulo Intranet

API REST para el módulo de Intranet, desarrollada en PHP para ser consumida por el frontend Vue.js.

## 📁 Estructura

```
api/intranet/
├── documentos.php              # Lista de documentos
├── documentos/
│   ├── descargar.php          # Descargar documento
│   └── ver.php                # Ver documento
├── directorio.php             # Lista de contactos
├── servicios.php              # Lista de servicios
├── archivos.php               # Explorador de archivos
├── archivos/
│   └── ver.php                # Ver archivo
├── carrusel/
│   └── imagenes.php           # Imágenes del carrusel
└── README.md                  # Esta documentación
```

## 🔌 Endpoints Disponibles

### Documentos

#### GET `/api/intranet/documentos`
Lista todos los documentos activos.

**Respuesta:**
```json
[
  {
    "doc_id": 1,
    "doc_txt_nombre": "Manual de Usuario",
    "dep_txt_nombre": "Recursos Humanos",
    "cat_txt_nombre": "Manuales",
    "tip_txt_nombre": "PDF",
    "tip_txt_extension": ".pdf",
    "acc_id": 2,
    "acc_txt_ruta": "lectura/"
  }
]
```

#### GET `/api/intranet/documentos/descargar.php?id={id}`
Descarga un documento específico.

**Parámetros:**
- `id` (required): ID del documento

**Respuesta:** Archivo binario (download)

#### GET `/api/intranet/documentos/ver.php?id={id}`
Obtiene la URL para visualizar un documento.

**Parámetros:**
- `id` (required): ID del documento

**Respuesta:**
```json
{
  "url": "/intranet/visualizador.php?MG$=ABC2880...",
  "nombre": "documento.pdf"
}
```

### Directorio

#### GET `/api/intranet/directorio`
Lista todos los contactos del directorio.

**Respuesta:**
```json
[
  {
    "dir_txt_area": "Recursos Humanos",
    "dir_txt_colaborador": "Juan Pérez",
    "dir_val_extensión": "1234"
  }
]
```

### Servicios

#### GET `/api/intranet/servicios`
Lista todos los servicios empresariales.

**Respuesta:**
```json
[
  {
    "ser_txt_nombre": "Sistema de Gestión",
    "ser_txt_enlace": "https://sistema.empresa.com"
  }
]
```

### Archivos

#### GET `/api/intranet/archivos`
Lista archivos y carpetas del explorador.

**Parámetros:**
- `path` (optional): Ruta relativa de la carpeta
- `search` (optional): Término de búsqueda

**Ejemplo:**
- `/api/intranet/archivos` - Lista raíz
- `/api/intranet/archivos?path=carpeta1` - Lista carpeta específica
- `/api/intranet/archivos?search=documento` - Busca archivos

**Respuesta:**
```json
{
  "items": [
    {
      "nombre": "documento.pdf",
      "es_directorio": false,
      "ruta_relativa": "documento.pdf",
      "ruta_completa": "/path/to/file",
      "extension": "pdf",
      "tamaño": 1024,
      "fecha": 1234567890
    }
  ],
  "ruta_actual": "",
  "nombre_carpeta_actual": ""
}
```

#### GET `/api/intranet/archivos/ver.php?file={ruta}`
Obtiene la URL para visualizar un archivo.

**Parámetros:**
- `file` (required): Ruta relativa del archivo

**Respuesta:**
```json
{
  "url": "/intranet/ver_pdf_th.php?file=documento.pdf",
  "ruta_relativa": "documento.pdf"
}
```

### Carrusel

#### GET `/api/intranet/carrusel/imagenes`
Lista todas las imágenes del carrusel.

**Respuesta:**
```json
[
  "/assets/img/intranet/carrusel/imagen1.jpg",
  "/assets/img/intranet/carrusel/imagen2.png"
]
```

## 🔐 Seguridad

- Todos los endpoints validan el método HTTP
- Validación de rutas de archivos para prevenir path traversal
- CORS habilitado para desarrollo (ajustar en producción)
- Validación de parámetros de entrada

## 📊 Base de Datos

La API utiliza las mismas funciones de conexión que el código PHP existente:
- `cx/bd_scs.php` - Funciones de conexión
- `resp_simdim()` - Para consultas que retornan múltiples filas

## 🔄 Compatibilidad

- Compatible con el código PHP existente
- Reutiliza las mismas consultas SQL
- Mantiene la misma estructura de datos
- No afecta el funcionamiento de `intranet/*.php`

## 🚀 Uso desde Vue.js

Los endpoints están configurados para ser consumidos por el módulo Vue.js en:
- `src/modules/intranet/services/intranetApi.ts`

## 📝 Notas

- Los archivos se sirven desde `assets/doc/` y `assets/img/intranet/carrusel/`
- Las rutas de producción y desarrollo se detectan automáticamente
- Los errores se retornan en formato JSON con códigos HTTP apropiados


