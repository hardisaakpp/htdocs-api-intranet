<template>
  <div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button
        v-for="(imagen, index) in imagenes"
        :key="index"
        type="button"
        :data-bs-target="'#myCarousel'"
        :data-bs-slide-to="index"
        :class="{ active: index === 0 }"
        :aria-label="`Slide ${index + 1}`"
      ></button>
    </div>
    <div class="carousel-inner ratio ratio-16x9">
      <div
        v-for="(imagen, index) in imagenes"
        :key="index"
        class="carousel-item"
        :class="{ active: index === 0 }"
      >
        <img :src="imagen" class="img-fluid" :alt="`Slide ${index + 1}`" />
      </div>
    </div>
    <button
      class="carousel-control-prev"
      type="button"
      data-bs-target="#myCarousel"
      data-bs-slide="prev"
    >
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button
      class="carousel-control-next"
      type="button"
      data-bs-target="#myCarousel"
      data-bs-slide="next"
    >
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Siguiente</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'

interface Props {
  imagenes: string[]
}

const props = defineProps<Props>()

onMounted(() => {
  // Script para ocultar botones después de 3 segundos
  const carousel = document.getElementById('myCarousel')
  if (!carousel) return

  const prevButton = carousel.querySelector('.carousel-control-prev')
  const nextButton = carousel.querySelector('.carousel-control-next')
  let hideTimeout: ReturnType<typeof setTimeout> | null = null

  function hideButtons() {
    if (prevButton) prevButton.classList.add('hide-controls')
    if (nextButton) nextButton.classList.add('hide-controls')
  }

  function showButtons() {
    if (prevButton) prevButton.classList.remove('hide-controls')
    if (nextButton) nextButton.classList.remove('hide-controls')
  }

  function resetHideTimer() {
    if (hideTimeout) {
      clearTimeout(hideTimeout)
    }
    showButtons()
    hideTimeout = setTimeout(() => {
      hideButtons()
    }, 3000)
  }

  if (prevButton) {
    prevButton.addEventListener('click', resetHideTimer)
  }
  if (nextButton) {
    nextButton.addEventListener('click', resetHideTimer)
  }

  carousel.addEventListener('mouseleave', resetHideTimer)
  carousel.addEventListener('mouseenter', () => {
    if (hideTimeout) {
      clearTimeout(hideTimeout)
    }
    showButtons()
  })
})

onUnmounted(() => {
  // Cleanup si es necesario
})
</script>

<style scoped>
/* Estilos del carrusel - replicando diseño PHP */
.carousel {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
  margin: 4rem auto 3rem auto;
  position: relative;
  background: var(--bs-body-bg, #212529);
  max-width: 85%;
}

.carousel-inner {
  position: relative;
}

.carousel-inner::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0) 0%,
    rgba(0, 0, 0, 0.05) 50%,
    rgba(0, 0, 0, 0.15) 100%
  );
  z-index: 1;
  pointer-events: none;
  transition: opacity 0.8s ease;
}

.carousel-item {
  position: relative;
  overflow: hidden;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.6s ease;
}

.carousel-item img {
  object-fit: cover;
  width: 100%;
  height: 100%;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.carousel-item.active img {
  transform: scale(1);
}

.carousel-item.active:hover img {
  transform: scale(1.1);
}

.carousel-item:not(.active) img {
  transform: scale(1.05);
}

.carousel-item.active::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.15),
    transparent
  );
  animation: shimmer 4s infinite;
  z-index: 3;
  pointer-events: none;
}

@keyframes shimmer {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}

.carousel-indicators {
  margin-bottom: 1.5rem;
  z-index: 3;
}

.carousel-indicators [type="button"] {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin: 0 6px;
  background-color: rgba(255, 255, 255, 0.5);
  border: 2px solid rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
  opacity: 0.7;
}

.carousel-indicators [type="button"]:hover {
  opacity: 1;
  transform: scale(1.2);
}

.carousel-indicators [type="button"].active {
  width: 40px;
  border-radius: 6px;
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  border-color: rgba(255, 255, 255, 1);
  opacity: 1;
  box-shadow: 0 4px 12px rgba(100, 181, 246, 0.4);
}

.carousel-control-prev,
.carousel-control-next {
  width: 60px;
  height: 60px;
  top: 50%;
  transform: translateY(-50%);
  opacity: 0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: rgba(240, 240, 240, 0.85);
  backdrop-filter: blur(10px);
  border-radius: 50%;
  margin: 0 1rem;
  border: 2px solid rgba(180, 180, 180, 0.4);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.carousel:hover .carousel-control-prev,
.carousel:hover .carousel-control-next {
  opacity: 1;
}

.carousel-control-prev.hide-controls,
.carousel-control-next.hide-controls {
  opacity: 0 !important;
  pointer-events: none;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
  background: rgba(250, 250, 250, 0.95);
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  border-color: rgba(150, 150, 150, 0.5);
}

.carousel-control-prev {
  left: 0;
}

.carousel-control-next {
  right: 0;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 24px;
  height: 24px;
  background-size: 24px 24px;
  filter: brightness(0) saturate(0%) opacity(0.6);
  transition: transform 0.3s ease;
}

.carousel-control-prev:hover .carousel-control-prev-icon,
.carousel-control-next:hover .carousel-control-next-icon {
  filter: brightness(0) saturate(0%) opacity(0.8);
}

[data-bs-theme="dark"] .carousel-control-prev,
[data-bs-theme="dark"] .carousel-control-next {
  background: rgba(80, 80, 80, 0.85);
  border: 2px solid rgba(200, 200, 200, 0.4);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

[data-bs-theme="dark"] .carousel-control-prev:hover,
[data-bs-theme="dark"] .carousel-control-next:hover {
  background: rgba(120, 120, 120, 0.95);
  border-color: rgba(220, 220, 220, 0.5);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

@media (max-width: 768px) {
  .carousel {
    max-width: 95%;
  }

  .carousel-control-prev,
  .carousel-control-next {
    width: 45px;
    height: 45px;
    margin: 0 0.5rem;
  }

  .carousel-indicators [type="button"] {
    width: 10px;
    height: 10px;
    margin: 0 4px;
  }

  .carousel-indicators [type="button"].active {
    width: 30px;
  }
}
</style>




