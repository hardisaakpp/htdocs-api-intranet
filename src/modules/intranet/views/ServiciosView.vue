<template>
  <main>
    <div class="modern-page-header">
      <h4>ENLACES A SERVICIOS EMPRESARIALES</h4>
      <p class="text-muted">Accede a los servicios empresariales disponibles</p>
    </div>
    <ServiciosTable :servicios="serviciosStore.servicios" :loading="serviciosStore.loading" />
  </main>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useServiciosStore } from '../stores/serviciosStore'
import ServiciosTable from '../components/ServiciosTable.vue'

const serviciosStore = useServiciosStore()

onMounted(() => {
  serviciosStore.fetchServicios()
})
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




