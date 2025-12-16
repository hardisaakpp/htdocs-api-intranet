import { defineStore } from 'pinia'
import { ref } from 'vue'
import { intranetService } from '../services/intranetApi'

export const useCarruselStore = defineStore('intranetCarrusel', () => {
  const imagenes = ref<string[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchImagenes() {
    loading.value = true
    error.value = null
    try {
      imagenes.value = await intranetService.getImagenesCarrusel()
    } catch (e: any) {
      error.value = e.message || 'Error al cargar imágenes del carrusel'
      console.error('Error fetching carrusel imágenes:', e)
    } finally {
      loading.value = false
    }
  }

  return {
    imagenes,
    loading,
    error,
    fetchImagenes
  }
})




