import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/intranet',
    name: 'intranet-home',
    component: () => import('./views/IntranetHomeView.vue'),
    meta: {
      requiresAuth: true,
      title: 'Intranet',
      icon: 'HomeIcon'
    }
  },
  {
    path: '/intranet/documentos',
    name: 'intranet-documentos',
    component: () => import('./views/DocumentosView.vue'),
    meta: {
      requiresAuth: true,
      permissions: ['intranet.documentos.view'],
      title: 'Documentos'
    }
  },
  {
    path: '/intranet/directorio',
    name: 'intranet-directorio',
    component: () => import('./views/DirectorioView.vue'),
    meta: {
      requiresAuth: true,
      permissions: ['intranet.directorio.view'],
      title: 'Directorio'
    }
  },
  {
    path: '/intranet/servicios',
    name: 'intranet-servicios',
    component: () => import('./views/ServiciosView.vue'),
    meta: {
      requiresAuth: true,
      permissions: ['intranet.servicios.view'],
      title: 'Servicios'
    }
  },
  {
    path: '/intranet/explorador',
    name: 'intranet-explorador',
    component: () => import('./views/ExploradorCarpetasView.vue'),
    meta: {
      requiresAuth: true,
      permissions: ['intranet.archivos.view'],
      title: 'Explorador de Carpetas'
    }
  }
]

export default routes




