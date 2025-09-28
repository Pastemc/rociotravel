import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import UsuariosView from '../views/UsuariosView.vue'
import DatosClienteView from '../views/DatosClienteView.vue'
import AgregarDetalleView from '../views/AgregarDetalleView.vue'
import DetallesPasajeView from '../views/DetallesPasajeView.vue'
import HistorialVentasView from '../views/HistorialVentasView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/usuarios',
    name: 'usuarios',
    component: UsuariosView
  },
  
  // ========== RUTAS DEL SISTEMA ROCÍO TRAVEL ==========
  
  // RUTA PARA DATOS DEL CLIENTE
  {
    path: '/datos-cliente',
    name: 'datos-cliente',
    component: DatosClienteView
  },
  
  // RUTA PRINCIPAL PARA AGREGAR DETALLES DEL PASAJE
  {
    path: '/agregar-detalle',
    name: 'AgregarDetalle',
    component: AgregarDetalleView,
    props: true
  },
  
  // RUTA ALTERNATIVA PARA AGREGAR DETALLES DEL PASAJE
  {
    path: '/agregar-detalles-pasaje',
    name: 'agregar-detalles-pasaje',
    component: AgregarDetalleView
  },
  
  // RUTA PARA DETALLES DEL PASAJE
  {
    path: '/detalles-pasaje',
    name: 'detalles-pasaje',
    component: DetallesPasajeView
  },
  
  // RUTA PARA HISTORIAL DE VENTAS
  {
    path: '/historial-ventas',
    name: 'historial-ventas',
    component: HistorialVentasView
  },
  
  // ========== RUTAS ALIAS PARA COMPATIBILIDAD ==========
  
  // Alias para acceder al historial de ventas con diferentes URLs
  {
    path: '/ver-nota-venta',
    redirect: '/historial-ventas'
  },
  {
    path: '/nota-ventas',
    redirect: '/historial-ventas'
  },
  {
    path: '/notas-venta',
    redirect: '/historial-ventas'
  },
  {
    path: '/ventas',
    redirect: '/historial-ventas'
  },
  
  // Alias adicionales para agregar detalle
  {
    path: '/detalle-pasaje',
    redirect: '/agregar-detalle'
  },
  {
    path: '/nuevo-pasaje',
    redirect: '/agregar-detalle'
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// ========== GUARDS GLOBALES ==========

// Guard para debugging y configuración de títulos
router.beforeEach((to, from, next) => {
  if (import.meta.env.DEV) {
    console.log(`🔄 Navegando de "${from.path}" a "${to.path}"`)
    console.log(`🔄 Nombre de ruta destino: "${to.name}"`)
  }
  
  // Actualizar título de la página
  const titles = {
    '/': 'Dashboard - Rocío Travel',
    '/usuarios': 'Usuarios - Rocío Travel',
    '/datos-cliente': 'Datos del Cliente - Rocío Travel',
    '/agregar-detalle': 'Agregar Detalle del Pasaje - Rocío Travel',
    '/agregar-detalles-pasaje': 'Agregar Detalles del Pasaje - Rocío Travel',
    '/detalles-pasaje': 'Detalles del Pasaje - Rocío Travel',
    '/historial-ventas': 'Historial de Ventas - Rocío Travel'
  }
  
  if (titles[to.path]) {
    document.title = titles[to.path]
  }
  
  next()
})

// Guard para verificar datos del cliente en rutas que lo requieren
router.beforeEach((to, from, next) => {
  // Si va a agregar-detalle, verificar que venga de datos-cliente o tenga datos guardados
  if (to.name === 'AgregarDetalle' || to.name === 'agregar-detalles-pasaje') {
    const datosCliente = sessionStorage.getItem('datosCliente')
    
    if (!datosCliente && from.name !== 'datos-cliente') {
      if (import.meta.env.DEV) {
        console.log('⚠️ No hay datos de cliente, redirigiendo a datos-cliente')
      }
      // Si no hay datos del cliente y no viene de datos-cliente, redirigir
      next({ name: 'datos-cliente' })
      return
    }
  }
  
  next()
})

// ========== DEBUGGING EN DESARROLLO ==========
if (import.meta.env.DEV) {
  // Agregar método global para debugging
  window.router = router
  
  console.log('🚀 Router configurado correctamente para Rocío Travel')
  console.log('📊 Total de rutas:', router.getRoutes().length)
  console.log('🔧 Rutas disponibles:')
  router.getRoutes().forEach(route => {
    console.log(`   📍 ${route.path} → ${route.name || 'sin-nombre'}`)
  })
  
  // Verificar rutas importantes
  const rutasImportantes = [
    '/datos-cliente',
    '/agregar-detalle', 
    '/historial-ventas'
  ]
  
  rutasImportantes.forEach(path => {
    const route = router.getRoutes().find(r => r.path === path)
    if (route) {
      console.log(`✅ Ruta ${path} configurada correctamente`)
    } else {
      console.log(`❌ Ruta ${path} NO encontrada`)
    }
  })
  
  // Función helper para testing de navegación
  window.testNavigation = (routeName) => {
    console.log(`🧪 Probando navegación a: ${routeName}`)
    router.push({ name: routeName })
      .then(() => {
        console.log(`✅ Navegación exitosa a: ${routeName}`)
      })
      .catch(error => {
        console.error(`❌ Error navegando a ${routeName}:`, error)
      })
  }
  
  // Función para limpiar sessionStorage (útil para testing)
  window.clearClientData = () => {
    sessionStorage.removeItem('datosCliente')
    console.log('🧹 Datos del cliente eliminados del sessionStorage')
  }
}

export default router