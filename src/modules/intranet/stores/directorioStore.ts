import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Contacto } from '../types'
import { intranetService } from '../services/intranetApi'

export const useDirectorioStore = defineStore('intranetDirectorio', () => {
  const contactos = ref<Contacto[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchContactos() {
    loading.value = true
    error.value = null
    try {
      contactos.value = await intranetService.getContactos()
    } catch (e: any) {
      error.value = e.message || 'Error al cargar contactos'
      console.error('Error fetching contactos:', e)
    } finally {
      loading.value = false
    }
  }

  return {
    contactos,
    loading,
    error,
    fetchContactos
  }
})




