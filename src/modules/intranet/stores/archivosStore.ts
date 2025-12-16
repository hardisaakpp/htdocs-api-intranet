import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { ArchivoItem } from '../types'
import { intranetService } from '../services/intranetApi'

export const useArchivosStore = defineStore('intranetArchivos', () => {
  const archivos = ref<ArchivoItem[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const rutaActual = ref<string>('')
  const nombreCarpetaActual = ref<string>('')

  async function fetchArchivos(path?: string, search?: string) {
    loading.value = true
    error.value = null
    try {
      const response = await intranetService.getArchivos(path, search)
      archivos.value = response.items || []
      rutaActual.value = response.ruta_actual || ''
      nombreCarpetaActual.value = response.nombre_carpeta_actual || ''
    } catch (e: any) {
      error.value = e.message || 'Error al cargar archivos'
      console.error('Error fetching archivos:', e)
    } finally {
      loading.value = false
    }
  }

  async function verArchivo(rutaRelativa: string): Promise<string> {
    try {
      return await intranetService.verArchivo(rutaRelativa)
    } catch (e: any) {
      error.value = e.message || 'Error al ver archivo'
      throw e
    }
  }

  function formatearTamaño(bytes: number): string {
    if (bytes === 0) return '0 KB'
    if (bytes > 1024 * 1024) {
      return `${(bytes / (1024 * 1024)).toFixed(2)} MB`
    }
    return `${(bytes / 1024).toFixed(2)} KB`
  }

  function formatearFecha(timestamp: number): string {
    const fecha = new Date(timestamp * 1000)
    return fecha.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  return {
    archivos,
    loading,
    error,
    rutaActual,
    nombreCarpetaActual,
    fetchArchivos,
    verArchivo,
    formatearTamaño,
    formatearFecha
  }
})



