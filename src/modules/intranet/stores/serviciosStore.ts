import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Servicio } from '../types'
import { intranetService } from '../services/intranetApi'

export const useServiciosStore = defineStore('intranetServicios', () => {
  const servicios = ref<Servicio[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchServicios() {
    loading.value = true
    error.value = null
    try {
      servicios.value = await intranetService.getServicios()
    } catch (e: any) {
      error.value = e.message || 'Error al cargar servicios'
      console.error('Error fetching servicios:', e)
    } finally {
      loading.value = false
    }
  }

  return {
    servicios,
    loading,
    error,
    fetchServicios
  }
})




