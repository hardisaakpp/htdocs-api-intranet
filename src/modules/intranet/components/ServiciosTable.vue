<template>
  <div class="table-container">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <div class="table-responsive">
      <table width="100%" class="table table-striped table-hover" id="servicios-table" style="font-size: 12px; text-align: center;">
        <thead>
          <tr width="100%" style="font-size: 0.9rem;">
            <th>NOMBRE SERVICIO</th>
            <th>ENLACE SERVICIO</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="2" class="text-center py-4">
              <p class="text-gray-500">Cargando...</p>
            </td>
          </tr>
          <tr v-else-if="servicios.length === 0">
            <td colspan="2" class="text-center py-4">
              <p class="text-muted">No hay servicios disponibles</p>
            </td>
          </tr>
          <tr v-else v-for="(servicio, index) in servicios" :key="index">
            <td style="font-size: 0.9rem; vertical-align: middle; text-align: center;">
              <strong>{{ servicio.ser_txt_nombre }}</strong>
            </td>
            <td>
              <a :href="servicio.ser_txt_enlace" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
                <div class="icon-container" @mousedown="animateStar">
                  <span class="tooltip-text">Pulsa aquí</span>
                  <i class="bi bi-hand-index-thumb-fill hand-icon"></i>
                  <div class="star" :class="{ animate: starAnimated }">
                    <i class="bi bi-star-fill"></i>
                  </div>
                </div>
              </a>
            </td>
          </tr>
          <!-- Panel Bendo hardcodeado -->
          <tr>
            <td style="font-size: 0.9rem; vertical-align: middle; text-align: center;">
              <strong>Panel Bendo</strong>
            </td>
            <td>
              <a href="https://panel.bendo.ec/login" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
                <div class="icon-container" @mousedown="animateStar">
                  <span class="tooltip-text">Pulsa aquí</span>
                  <i class="bi bi-hand-index-thumb-fill hand-icon"></i>
                  <div class="star" :class="{ animate: starAnimated }">
                    <i class="bi bi-star-fill"></i>
                  </div>
                </div>
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { Servicio } from '../types'

interface Props {
  servicios: Servicio[]
  loading: boolean
}

defineProps<Props>()

const starAnimated = ref(false)

const animateStar = () => {
  starAnimated.value = false
  setTimeout(() => {
    starAnimated.value = true
  }, 10)
}
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

.icon-container {
  position: relative;
  cursor: pointer;
  display: inline-block;
  transition: transform 0.3s ease;
}

.icon-container:hover {
  transform: scale(1.1);
}

.hand-icon {
  font-size: 2rem;
  color: #64b5f6;
  display: inline-block;
  transition: color 0.3s ease;
}

.icon-container:hover .hand-icon {
  color: #1976d2;
}

.star {
  position: absolute;
  top: 5%;
  left: 55%;
  transform: translate(-50%, -50%);
  color: gold;
  font-size: 1.2rem;
  opacity: 0;
  pointer-events: none;
}

.star.animate {
  animation: pop 0.6s ease-out;
}

@keyframes pop {
  0% {
    transform: translate(-50%, -50%) scale(0.5);
    opacity: 0;
  }
  50% {
    transform: translate(-50%, -50%) scale(1.2);
    opacity: 1;
  }
  100% {
    transform: translate(-50%, -50%) scale(1);
    opacity: 0;
  }
}

.tooltip-text {
  position: absolute;
  top: -2.5rem;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  color: #fff;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.icon-container:hover .tooltip-text {
  opacity: 1;
}
</style>

