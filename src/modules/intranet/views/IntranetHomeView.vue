<template>
  <main>
    <CarruselViewer v-if="!carruselStore.loading && carruselStore.imagenes.length > 0" :imagenes="carruselStore.imagenes" />
    
    <div v-if="carruselStore.loading" class="text-center py-12">
      <p class="text-gray-500">Cargando carrusel...</p>
    </div>

    <div class="container marketing">
      <div class="row g-4">
        <div class="col-lg-4">
          <FeatureCard to="/intranet/directorio">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877zm10.761.135a.5.5 0 0 1 .708 0l2.5 2.5a.5.5 0 0 1 0 .708l-2.5 2.5a.5.5 0 0 1-.708-.708L14.293 4H9.5a.5.5 0 0 1 0-1h4.793l-1.647-1.646a.5.5 0 0 1 0-.708"/>
              </svg>
            </template>
            <template #title>DIRECTORIO EMPRESARIAL</template>
            <template #description>Consulta la información de contacto de nuestros colaboradores</template>
            <template #button-text>Ver Directorio</template>
          </FeatureCard>
        </div>
        <div class="col-lg-4">
          <FeatureCard to="/intranet/servicios">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
              </svg>
            </template>
            <template #title>ENLACES A SERVICIOS</template>
            <template #description>Accede a los servicios empresariales disponibles</template>
            <template #button-text>Ver Servicios</template>
          </FeatureCard>
        </div>
        <div class="col-lg-4">
          <FeatureCard to="/intranet/documentos">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                <path d="M3 0h6a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/>
                <path d="M7.5 1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM3 3a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
                <path d="M11.5 2H13a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-1.5a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5z"/>
              </svg>
            </template>
            <template #title>DOCUMENTACIÓN</template>
            <template #description>Explora y descarga documentos corporativos</template>
            <template #button-text>Ver Documentos</template>
          </FeatureCard>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useCarruselStore } from '../stores/carruselStore'
import CarruselViewer from '../components/CarruselViewer.vue'
import FeatureCard from '../components/FeatureCard.vue'

const carruselStore = useCarruselStore()

onMounted(() => {
  carruselStore.fetchImagenes()
})
</script>

<style scoped>
.container.marketing {
  padding: 2rem 0;
}
</style>




