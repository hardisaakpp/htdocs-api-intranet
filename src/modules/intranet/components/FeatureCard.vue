<template>
  <div class="feature-card">
    <div class="feature-icon">
      <slot name="icon"></slot>
    </div>
    <h3 class="feature-title">
      <slot name="title"></slot>
    </h3>
    <p class="text-muted mb-4">
      <slot name="description"></slot>
    </p>
    <router-link :to="to">
      <button type="button" class="feature-btn">
        <span><slot name="button-text"></slot></span>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
        </svg>
      </button>
    </router-link>
  </div>
</template>

<script setup lang="ts">
interface Props {
  to: string
}

defineProps<Props>()
</script>

<style scoped>
.feature-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  text-align: center;
  height: 100%;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.05);
  position: relative;
  overflow: hidden;
}

.feature-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
}

.feature-card:hover::before {
  transform: scaleX(1);
}

[data-bs-theme="dark"] .feature-card {
  background: #212529;
  border-color: rgba(255, 255, 255, 0.1);
}

.feature-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 1.5rem;
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  transition: transform 0.3s ease;
}

.feature-card:hover .feature-icon {
  transform: scale(1.1) rotate(5deg);
}

.feature-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #212529;
}

[data-bs-theme="dark"] .feature-title {
  color: #f8f9fa;
}

.feature-btn {
  background: linear-gradient(135deg, #64b5f6 0%, #1976d2 100%);
  border: none;
  border-radius: 10px;
  padding: 0.75rem 1.5rem;
  color: white;
  font-weight: 500;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(100, 181, 246, 0.3);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.feature-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(100, 181, 246, 0.4);
  color: white;
}

.feature-btn svg {
  transition: transform 0.3s ease;
}

.feature-btn:hover svg {
  transform: translateX(4px);
}
</style>




