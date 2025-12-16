// Tipos TypeScript para el módulo de Intranet

export interface Documento {
  doc_id: number
  doc_txt_nombre: string
  dep_txt_nombre: string
  cat_txt_nombre: string
  tip_txt_nombre: string
  tip_txt_extension: string
  acc_id: number
  acc_txt_ruta: string
}

export interface Contacto {
  dir_txt_area: string
  dir_txt_colaborador: string
  dir_val_extensión: string
}

export interface Servicio {
  ser_txt_nombre: string
  ser_txt_enlace: string
}

export interface ArchivoItem {
  nombre: string
  es_directorio: boolean
  ruta_relativa: string
  ruta_completa: string
  extension: string
  tamaño: number
  fecha: number
}

export interface CarruselImagen {
  ruta: string
  nombre: string
}

export interface IntranetState {
  loading: boolean
  error: string | null
}




