<template>
  <main>
    <div class="modern-page-header">
      <h4>DOCUMENTACIÓN</h4>
      <p class="text-muted">Explora y descarga documentos corporativos</p>
    </div>

    <DocumentosTable :documentos="documentosStore.documentos" :loading="documentosStore.loading" />

    <div class="talento-humano-wrapper">
      <div class="icon-container">
        <span class="tooltip-text">Abrir carpeta</span>
        <button class="btn btn-outline-primary d-inline-flex align-items-center gap-2" @click="irAExplorador">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-folder-fill" viewBox="0 0 16 16">
            <path d="M9.828 4a.5.5 0 0 1 .354.146l1.5 1.5A.5.5 0 0 1 11.5 6H14a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h5.172a.5.5 0 0 1 .354.146l1.5 1.5A.5.5 0 0 1 9.5 3H14a2 2 0 0 1 2 2v1H9.828z"/>
          </svg>
          TALENTO HUMANO
        </button>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useDocumentosStore } from '../stores/documentosStore'
import DocumentosTable from '../components/DocumentosTable.vue'

const router = useRouter()
const documentosStore = useDocumentosStore()

onMounted(() => {
  documentosStore.fetchDocumentos()
})

const irAExplorador = () => {
  router.push({ name: 'intranet-explorador' })
}
</script>

<style scoped>
.modern-page-header {
  text-align: center;
  margin-bottom: 2rem;
  padding: 1.5rem 0;
}

.modern-page-header h4 {
  font-size: 2rem;
  font-weight: 600;
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.5rem;
}

.talento-humano-wrapper {
  margin: 1.5rem auto 3rem;
  display: flex;
  justify-content: center;
  max-width: 360px;
}

.tooltip-text {
  position: absolute;
  top: -2.5rem;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  color: #fff;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
  z-index: 100;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.icon-container {
  position: relative;
  display: inline-block;
  transition: transform 0.3s ease;
}

.icon-container:hover {
  transform: scale(1.1);
}

.icon-container:hover .tooltip-text {
  opacity: 1;
}

.icon-container svg {
  transition: all 0.3s ease;
  color: #64b5f6;
}

.icon-container:hover svg {
  color: #1976d2;
  transform: scale(1.1);
}

.btn-outline-primary {
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-outline-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(100, 181, 246, 0.3);
}
</style>




