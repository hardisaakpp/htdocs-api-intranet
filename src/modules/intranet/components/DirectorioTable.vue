<template>
  <div class="table-container">
    <div class="table-responsive">
      <table class="table table-striped table-hover" id="directorio-table" style="font-size: 12px;">
        <thead align="left">
          <tr width="100%" align="center" style="font-size: 0.9rem;">
            <th>ÁREA</th>
            <th>COLABORADOR</th>
            <th>EXTENSIÓN</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="3" class="text-center py-4">
              <p class="text-gray-500">Cargando...</p>
            </td>
          </tr>
          <tr v-else-if="contactos.length === 0">
            <td colspan="3" class="text-center py-4">
              <p class="text-muted">No hay contactos disponibles</p>
            </td>
          </tr>
          <tr v-else v-for="(contacto, index) in contactos" :key="index">
            <td align="center" style="font-size: 0.9rem;">
              <strong>{{ contacto.dir_txt_area }}</strong>
            </td>
            <td align="left" style="font-size: 0.9rem;">{{ contacto.dir_txt_colaborador }}</td>
            <td align="left" style="font-size: 0.9rem;">{{ contacto.dir_val_extensión }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue'
import type { Contacto } from '../types'

interface Props {
  contactos: Contacto[]
  loading: boolean
}

const props = defineProps<Props>()

onMounted(() => {
  if (typeof window !== 'undefined' && (window as any).$) {
    const $ = (window as any).$
    if ($.fn.DataTable) {
      $('#directorio-table').DataTable({
        pageLength: 10,
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'asc']]
      })
    }
  }
})

watch(() => props.contactos, () => {
  if (typeof window !== 'undefined' && (window as any).$) {
    const $ = (window as any).$
    if ($.fn.DataTable) {
      $('#directorio-table').DataTable().destroy()
      $('#directorio-table').DataTable({
        pageLength: 10,
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'asc']]
      })
    }
  }
}, { deep: true })
</script>

<style scoped>
.table-container {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

[data-bs-theme="dark"] .table-container {
  background: #212529;
  border-color: rgba(255, 255, 255, 0.1);
}

.table thead {
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  color: white;
}

.table thead th,
.table thead td {
  border: none;
  padding: 1rem;
  font-weight: 600;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.table tbody tr {
  transition: all 0.2s ease;
}

.table tbody tr:hover {
  background-color: rgba(100, 181, 246, 0.05);
  transform: scale(1.01);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.table tbody td {
  padding: 0.875rem 1rem;
  vertical-align: middle;
  border-color: rgba(0, 0, 0, 0.05);
}
</style>

