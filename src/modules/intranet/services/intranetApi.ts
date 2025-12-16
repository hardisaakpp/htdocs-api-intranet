import axios from 'axios'
import type { Documento, Contacto, Servicio, ArchivoItem } from '../types'

const API_URL = '/api/intranet'

export const intranetService = {
  // Documentos
  async getDocumentos(): Promise<Documento[]> {
    const response = await axios.get(`${API_URL}/documentos`)
    return response.data
  },

  async descargarDocumento(id: number): Promise<Blob> {
    const response = await axios.get(`${API_URL}/documentos/descargar.php`, {
      params: { id },
      responseType: 'blob'
    })
    return response.data
  },

  async verDocumento(id: number): Promise<string> {
    const response = await axios.get(`${API_URL}/documentos/ver.php`, {
      params: { id }
    })
    return response.data.url
  },

  // Directorio
  async getContactos(): Promise<Contacto[]> {
    const response = await axios.get(`${API_URL}/directorio`)
    return response.data
  },

  // Servicios
  async getServicios(): Promise<Servicio[]> {
    const response = await axios.get(`${API_URL}/servicios`)
    return response.data
  },

  // Explorador de archivos
  async getArchivos(path?: string, search?: string): Promise<{
    items: ArchivoItem[]
    ruta_actual: string
    nombre_carpeta_actual: string
  }> {
    const params = new URLSearchParams()
    if (path) params.append('path', path)
    if (search) params.append('search', search)
    
    const response = await axios.get(`${API_URL}/archivos?${params.toString()}`)
    return response.data
  },

  async verArchivo(rutaRelativa: string): Promise<string> {
    const response = await axios.get(`${API_URL}/archivos/ver.php`, {
      params: { file: rutaRelativa }
    })
    return response.data.url
  },

  // Carrusel
  async getImagenesCarrusel(): Promise<string[]> {
    const response = await axios.get(`${API_URL}/carrusel/imagenes`)
    return response.data
  }
}



