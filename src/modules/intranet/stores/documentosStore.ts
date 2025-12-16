import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Documento } from '../types'
import { intranetService } from '../services/intranetApi'

export const useDocumentosStore = defineStore('intranetDocumentos', () => {
  const documentos = ref<Documento[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchDocumentos() {
    loading.value = true
    error.value = null
    try {
      documentos.value = await intranetService.getDocumentos()
    } catch (e: any) {
      error.value = e.message || 'Error al cargar documentos'
      console.error('Error fetching documentos:', e)
    } finally {
      loading.value = false
    }
  }

  async function descargarDocumento(id: number, nombre: string) {
    try {
      const blob = await intranetService.descargarDocumento(id)
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = nombre
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
    } catch (e: any) {
      error.value = e.message || 'Error al descargar documento'
      throw e
    }
  }

  async function verDocumento(id: number): Promise<string> {
    try {
      return await intranetService.verDocumento(id)
    } catch (e: any) {
      error.value = e.message || 'Error al ver documento'
      throw e
    }
  }

  return {
    documentos,
    loading,
    error,
    fetchDocumentos,
    descargarDocumento,
    verDocumento
  }
})




