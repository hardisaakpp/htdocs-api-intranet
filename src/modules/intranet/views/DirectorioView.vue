<template>
  <main>
    <div class="modern-page-header">
      <h4>DIRECTORIO EMPRESARIAL</h4>
      <p class="text-muted">Consulta la información de contacto de nuestros colaboradores</p>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <router-link
        to="/intranet/directorio/admin"
        class="btn d-flex align-items-center gap-2"
        style="background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%); color: white; border: none; border-radius: 10px; padding: 0.75rem 1.5rem; font-weight: 500; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(100, 181, 246, 0.3);"
        @mouseover="handleHover"
        @mouseout="handleLeave"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16" style="transition: transform 0.3s ease;">
          <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
        </svg>
        MODIFICAR DIRECTORIO
      </router-link>
    </div>
    <DirectorioTable :contactos="directorioStore.contactos" :loading="directorioStore.loading" />
  </main>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useDirectorioStore } from '../stores/directorioStore'
import DirectorioTable from '../components/DirectorioTable.vue'

const directorioStore = useDirectorioStore()

onMounted(() => {
  directorioStore.fetchContactos()
})

const handleHover = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (target.closest('a')) {
    const link = target.closest('a') as HTMLElement
    link.style.transform = 'translateY(-2px)'
    link.style.boxShadow = '0 6px 16px rgba(100, 181, 246, 0.4)'
    const svg = link.querySelector('svg')
    if (svg) {
      svg.style.transform = 'scale(1.1) rotate(15deg)'
    }
  }
}

const handleLeave = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (target.closest('a')) {
    const link = target.closest('a') as HTMLElement
    link.style.transform = 'translateY(0)'
    link.style.boxShadow = '0 4px 12px rgba(100, 181, 246, 0.3)'
    const svg = link.querySelector('svg')
    if (svg) {
      svg.style.transform = 'scale(1) rotate(0deg)'
    }
  }
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
</style>




