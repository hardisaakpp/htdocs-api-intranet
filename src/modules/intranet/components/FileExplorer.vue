<template>
  <div class="explorer-wrapper">
    <div class="explorer-header">
      <h3>
        <a @click="navegarARaiz">📁 Explorador de Carpetas - TH</a>
        <span v-if="nombreCarpetaActual" class="folder-separator" style="margin: 0 0.5rem;">/</span>
        <span v-if="nombreCarpetaActual" class="folder-name">{{ nombreCarpetaActual }}</span>
      </h3>
    </div>

    <!-- Formulario de búsqueda -->
    <form class="search-form" @submit.prevent="buscar">
      <input
        type="text"
        v-model="searchTerm"
        class="form-control"
        placeholder="🔍 Buscar archivo o carpeta..."
      />
      <button class="btn btn-outline-secondary" type="submit">Buscar</button>
      <button
        v-if="searchTerm"
        @click="limpiarBusqueda"
        class="btn btn-outline-danger"
        type="button"
      >
        Limpiar
      </button>
    </form>

    <!-- Tabla de resultados -->
    <div style="overflow-x: auto;">
      <table class="table table-hover table-sm">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Ruta</th>
            <th>Tipo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="3" class="empty-state">Cargando...</td>
          </tr>
          <tr v-else-if="archivos.length === 0">
            <td colspan="3" class="empty-state">No se encontraron resultados.</td>
          </tr>
          <tr
            v-else
            v-for="(item, index) in archivos"
            :key="index"
            :class="{ 'folder-row': item.es_directorio }"
            @click="item.es_directorio ? navegarACarpeta(item.ruta_relativa) : null"
            :style="item.es_directorio ? 'cursor: pointer;' : ''"
          >
            <td>
              <img
                v-if="item.es_directorio"
                src="https://cdn-icons-png.flaticon.com/512/715/715676.png"
                class="folder-icon"
              />
              <img
                v-else
                :src="obtenerIconoPorExtension(item.nombre)"
                class="file-icon"
                :style="esPDF(item.extension) ? 'cursor: pointer;' : ''"
                @click.stop="esPDF(item.extension) ? abrirPDF(item.ruta_relativa, item.nombre) : null"
              />
              <span
                v-if="esPDF(item.extension)"
                class="file-name-pdf"
                @click.stop="abrirPDF(item.ruta_relativa, item.nombre)"
              >
                {{ item.nombre }}
              </span>
              <span v-else class="file-name">{{ item.nombre }}</span>
            </td>
            <td class="text-muted">{{ item.ruta_relativa }}</td>
            <td>{{ item.es_directorio ? 'Carpeta' : 'Archivo' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal PDF -->
  <div v-if="showPdfModal" id="pdfModal" class="pdf-modal" @click="cerrarModalPDF">
    <div class="pdf-modal-content" @click.stop>
      <div class="pdf-modal-header">
        <span id="modalFilename" class="pdf-modal-filename">{{ pdfFileName }}</span>
        <div>
          <button @click="imprimirPDF" class="pdf-modal-btn">🖨 Imprimir</button>
          <a :href="pdfUrl" class="pdf-modal-btn" download target="_blank" style="text-decoration: none; display: inline-block;">⬇ Descargar</a>
          <button @click="cerrarModalPDF" class="pdf-modal-btn pdf-modal-btn-close">✖ Cerrar</button>
        </div>
      </div>
      <iframe id="pdfViewer" class="pdf-viewer" :src="pdfUrl"></iframe>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useArchivosStore } from '../stores/archivosStore'
import { useRoute, useRouter } from 'vue-router'

const archivosStore = useArchivosStore()
const route = useRoute()
const router = useRouter()

const searchTerm = ref('')
const showPdfModal = ref(false)
const pdfUrl = ref('')
const pdfFileName = ref('')

const archivos = computed(() => archivosStore.archivos)
const loading = computed(() => archivosStore.loading)
const nombreCarpetaActual = computed(() => archivosStore.nombreCarpetaActual)

const obtenerIconoPorExtension = (nombreArchivo: string): string => {
  const ext = nombreArchivo.split('.').pop()?.toLowerCase() || ''
  const iconos: Record<string, string> = {
    pdf: 'https://cdn-icons-png.flaticon.com/512/337/337946.png',
    doc: 'https://cdn-icons-png.flaticon.com/512/281/281760.png',
    docx: 'https://cdn-icons-png.flaticon.com/512/281/281760.png',
    xls: 'https://cdn-icons-png.flaticon.com/512/732/732220.png',
    xlsx: 'https://cdn-icons-png.flaticon.com/512/732/732220.png',
    jpg: 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
    jpeg: 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
    png: 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
    gif: 'https://cdn-icons-png.flaticon.com/512/136/136524.png',
    zip: 'https://cdn-icons-png.flaticon.com/512/888/888879.png',
    rar: 'https://cdn-icons-png.flaticon.com/512/888/888879.png',
    txt: 'https://cdn-icons-png.flaticon.com/512/3022/3022255.png',
    mp3: 'https://cdn-icons-png.flaticon.com/512/727/727245.png',
    wav: 'https://cdn-icons-png.flaticon.com/512/727/727245.png',
    mp4: 'https://cdn-icons-png.flaticon.com/512/727/727248.png',
    avi: 'https://cdn-icons-png.flaticon.com/512/727/727248.png'
  }
  return iconos[ext] || 'https://cdn-icons-png.flaticon.com/512/833/833524.png'
}

const esPDF = (extension: string): boolean => {
  return extension.toLowerCase() === 'pdf'
}

const buscar = () => {
  const path = route.query.path as string | undefined
  archivosStore.fetchArchivos(path, searchTerm.value)
}

const limpiarBusqueda = () => {
  searchTerm.value = ''
  const path = route.query.path as string | undefined
  archivosStore.fetchArchivos(path)
}

const navegarARaiz = () => {
  router.push({ name: 'intranet-explorador' })
}

const navegarACarpeta = (rutaRelativa: string) => {
  router.push({
    name: 'intranet-explorador',
    query: { path: rutaRelativa }
  })
}

const abrirPDF = async (rutaRelativa: string, nombre: string) => {
  try {
    pdfUrl.value = await archivosStore.verArchivo(rutaRelativa)
    pdfFileName.value = nombre
    showPdfModal.value = true
  } catch (error) {
    console.error('Error al abrir PDF:', error)
  }
}

const cerrarModalPDF = () => {
  showPdfModal.value = false
  pdfUrl.value = ''
  pdfFileName.value = ''
}

const imprimirPDF = () => {
  const iframe = document.getElementById('pdfViewer') as HTMLIFrameElement
  if (iframe && iframe.contentWindow) {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
  }
}

// Cargar archivos cuando cambie la ruta
watch(() => route.query.path, (newPath) => {
  archivosStore.fetchArchivos(newPath as string | undefined, searchTerm.value || undefined)
}, { immediate: true })
</script>

<style scoped>
.explorer-wrapper {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #dee2e6;
  padding: 2rem;
  margin-top: 1rem;
}

.explorer-header {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #dee2e6;
}

.explorer-header h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #212529;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.explorer-header h3 a {
  color: #212529;
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
}

.explorer-header h3 a:hover {
  color: #0d6efd;
  text-decoration: none;
}

[data-bs-theme="dark"] .explorer-wrapper {
  background: #161b22;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
  border: 1px solid #30363d;
}

[data-bs-theme="dark"] .explorer-header {
  border-bottom: 2px solid #30363d;
}

[data-bs-theme="dark"] .explorer-header h3 {
  color: #e6edf3;
}

[data-bs-theme="dark"] .explorer-header h3 a {
  color: #e6edf3;
}

[data-bs-theme="dark"] .explorer-header h3 a:hover {
  color: #58a6ff;
}

.folder-separator {
  color: #6c757d;
}

.folder-name {
  color: #0d6efd;
}

[data-bs-theme="dark"] .folder-separator {
  color: #8b949e;
}

[data-bs-theme="dark"] .folder-name {
  color: #58a6ff;
}

.folder-icon,
.file-icon {
  width: 24px;
  height: 24px;
  margin-right: 10px;
  vertical-align: middle;
  object-fit: contain;
}

.search-form {
  max-width: 500px;
  margin-bottom: 1.5rem;
  display: flex;
  gap: 0.5rem;
  align-items: stretch;
}

.search-form .form-control {
  background: #ffffff;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  padding: 0.6rem 1rem;
  transition: all 0.2s ease;
  font-size: 0.95rem;
  color: #212529;
}

.search-form .form-control::placeholder {
  color: #6c757d;
}

.search-form .form-control:focus {
  background: #ffffff;
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  color: #212529;
}

[data-bs-theme="dark"] .search-form .form-control {
  background: #0d1117;
  border: 2px solid #30363d;
  color: #e6edf3;
}

[data-bs-theme="dark"] .search-form .form-control::placeholder {
  color: #6e7681;
}

[data-bs-theme="dark"] .search-form .form-control:focus {
  background: #161b22;
  border-color: #2f81f7;
  box-shadow: 0 0 0 0.2rem rgba(47, 129, 247, 0.2);
  color: #e6edf3;
}

.table {
  background: #ffffff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 0;
  border: 1px solid #dee2e6;
  color: #212529;
}

.table thead th {
  background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
  color: #495057;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
  padding: 1rem;
}

.table tbody tr {
  transition: all 0.2s ease;
  border-bottom: 1px solid #dee2e6;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
  transform: scale(1.001);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table tbody td {
  padding: 1rem;
  vertical-align: middle;
  color: #212529;
}

[data-bs-theme="dark"] .table {
  background: #161b22;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  border: 1px solid #30363d;
  color: #e6edf3;
}

[data-bs-theme="dark"] .table thead th {
  background: linear-gradient(to bottom, #21262d, #161b22);
  border-bottom: 2px solid #30363d;
  color: #8b949e;
}

[data-bs-theme="dark"] .table tbody tr {
  border-bottom: 1px solid #21262d;
}

[data-bs-theme="dark"] .table tbody tr:hover {
  background-color: #1c2128;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

[data-bs-theme="dark"] .table tbody td {
  color: #e6edf3;
}

.folder-link {
  cursor: pointer;
  color: #0d6efd;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s ease;
}

.folder-link:hover {
  text-decoration: none;
  color: #0a58ca;
}

.folder-row {
  transition: all 0.2s ease;
}

.folder-row:hover {
  background-color: #f8f9fa !important;
  cursor: pointer;
}

[data-bs-theme="dark"] .folder-link {
  color: #58a6ff;
}

[data-bs-theme="dark"] .folder-link:hover {
  color: #79c0ff;
  text-shadow: 0 0 8px rgba(88, 166, 255, 0.4);
}

[data-bs-theme="dark"] .folder-row:hover {
  background-color: #1c2128 !important;
}

.file-name {
  color: #212529;
}

.file-name-pdf {
  cursor: pointer;
  color: #0d6efd;
  text-decoration: underline;
}

.file-name-pdf:hover {
  color: #0a58ca;
}

[data-bs-theme="dark"] .file-name {
  color: #e6edf3;
}

[data-bs-theme="dark"] .file-name-pdf {
  color: #58a6ff;
}

[data-bs-theme="dark"] .file-name-pdf:hover {
  color: #79c0ff;
}

.text-muted {
  color: #6c757d !important;
  font-size: 0.9rem;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6c757d;
}

[data-bs-theme="dark"] .text-muted {
  color: #8b949e !important;
}

[data-bs-theme="dark"] .empty-state {
  color: #8b949e;
}

.empty-state::before {
  content: "📂";
  font-size: 3rem;
  display: block;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.pdf-modal {
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.85);
  z-index: 1000;
}

.pdf-modal-content {
  position: relative;
  width: 90%;
  height: 90%;
  margin: 5% auto;
  background: #ffffff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.pdf-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  color: #212529;
  padding: 12px 16px;
}

.pdf-modal-filename {
  font-weight: 600;
  color: #212529;
}

.pdf-modal-btn {
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  margin-right: 8px;
  transition: all 0.2s;
  border: 1px solid #dee2e6;
  background: #ffffff;
  color: #212529;
}

.pdf-modal-btn:hover {
  background: #e9ecef;
  border-color: #adb5bd;
}

.pdf-modal-btn-close {
  background: #dc3545;
  border: 1px solid #dc3545;
  color: #fff;
}

.pdf-modal-btn-close:hover {
  background: #c82333;
  border-color: #bd2130;
}

.pdf-viewer {
  width: 100%;
  height: calc(100% - 60px);
  border: none;
  background: #f8f9fa;
}

[data-bs-theme="dark"] .pdf-modal-content {
  background: #161b22;
  border: 1px solid #30363d;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
}

[data-bs-theme="dark"] .pdf-modal-header {
  background: #21262d;
  border-bottom: 1px solid #30363d;
  color: #e6edf3;
}

[data-bs-theme="dark"] .pdf-modal-filename {
  color: #e6edf3;
}

[data-bs-theme="dark"] .pdf-modal-btn {
  background: #21262d;
  border: 1px solid #30363d;
  color: #e6edf3;
}

[data-bs-theme="dark"] .pdf-modal-btn:hover {
  background: #30363d;
  border-color: #484f58;
}

[data-bs-theme="dark"] .pdf-viewer {
  background: #0d1117;
}
</style>




