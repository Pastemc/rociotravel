<template>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <RouterLink to="/datos-cliente" class="brand-link">
      <div class="brand-image-placeholder">
        <i class="fas fa-ship"></i>
      </div>
      <span class="brand-text font-weight-light">Rocío Travel</span>
    </RouterLink>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <div class="user-avatar-placeholder">
            <i class="fas fa-user"></i>
          </div>
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ currentUser?.name || 'Usuario' }}</a>
          <small class="text-muted">{{ currentUser?.role || 'Sin rol' }}</small>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <!-- ========== CENTRAL TOURS EXPRESS - 4 MÓDULOS ========== -->
          
          <!-- 1. DATOS DEL CLIENTE -->
          <li class="nav-header">DATOS DEL CLIENTE</li>
          
          <li class="nav-item">
            <RouterLink to="/datos-cliente" class="nav-link" :class="{ active: $route.path === '/datos-cliente' }">
              <i class="nav-icon fas fa-user-plus"></i>
              <p>
                Datos del Cliente
                <span class="badge badge-success right">{{ clientesCount || 0 }}</span>
              </p>
            </RouterLink>
          </li>
          
          <!-- 2. AGREGAR DETALLES DEL PASAJE -->
          <li class="nav-header">AGREGAR DETALLES DEL PASAJE</li>
          
          <li class="nav-item">
            <RouterLink to="/agregar-detalles-pasaje" class="nav-link" :class="{ active: $route.path === '/agregar-detalles-pasaje' }">
              <i class="nav-icon fas fa-plus-circle"></i>
              <p>
                Agregar Detalles del Pasaje
                <span class="badge badge-success right">{{ detallesPasajeCount || 0 }}</span>
              </p>
            </RouterLink>
          </li>
          
          <!-- 3. DETALLES DEL PASAJE (RESUMEN) -->
          <li class="nav-header">DETALLES DEL PASAJE</li>
          
          <li class="nav-item">
            <RouterLink to="/detalles-pasaje" class="nav-link" :class="{ active: $route.path === '/detalles-pasaje' }">
              <i class="nav-icon fas fa-list-alt"></i>
              <p>
                Detalles del Pasaje
                <span class="badge badge-primary right">{{ pasajesCount || 0 }}</span>
              </p>
            </RouterLink>
          </li>
          
          <!-- 4. VER NOTA DE VENTA (HISTORIAL/CONSULTA) -->
          <li class="nav-header">VER NOTA DE VENTA</li>
          
          <li class="nav-item">
            <RouterLink to="/ver-nota-venta" class="nav-link" :class="{ active: $route.path === '/ver-nota-venta' }">
              <i class="nav-icon fas fa-receipt"></i>
              <p>
                Ver Nota de Venta
                <span class="badge badge-info right">{{ notasVentaCount || 0 }}</span>
              </p>
            </RouterLink>
          </li>
          
          <!-- Separador -->
          <li class="nav-header"></li>
          
          <!-- Cerrar Sesión -->
          <li class="nav-item">
            <a href="#" class="nav-link text-danger" @click.prevent="logout">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Cerrar Sesión</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Modal de confirmación -->
    <div v-if="mostrarModalLogout" class="modal-logout" @click="cerrarModal">
      <div class="modal-logout-content" @click.stop>
        <div class="modal-logout-header">
          <i class="fas fa-exclamation-triangle"></i>
          <h3>Cerrar Sesión</h3>
        </div>
        <div class="modal-logout-body">
          <p>¿Estás seguro de cerrar sesión?</p>
        </div>
        <div class="modal-logout-footer">
          <button @click="cerrarModal" class="btn-cancelar">Cancelar</button>
          <button @click="confirmarLogout" class="btn-confirmar">Cerrar Sesión</button>
        </div>
      </div>
    </div>
  </aside>
</template>

<script>
export default {
  name: "Sidebar",
  props: {
    currentUser: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      // ========== CONTADORES PARA LOS 4 MÓDULOS ==========
      clientesCount: 0,
      detallesPasajeCount: 0,
      pasajesCount: 0,
      notasVentaCount: 0,
      mostrarModalLogout: false
    }
  },
  mounted() {
    this.loadCounts()
    // Redirigir a datos-cliente si está en la raíz
    if (this.$route.path === '/') {
      this.$router.push('/datos-cliente')
    }
  },
  methods: {
    async loadCounts() {
      try {
        // ========== DATOS SIMULADOS PARA LOS 4 MÓDULOS ==========
        
        
        // TODO: Reemplazar con llamadas reales a la API
        // const response = await axios.get('/api/dashboard/counts')
        // Object.assign(this, response.data)
        
      } catch (error) {
        console.error('Error loading counts:', error)
      }
    },
    
    logout() {
      this.mostrarModalLogout = true
    },

    cerrarModal() {
      this.mostrarModalLogout = false
    },

    confirmarLogout() {
      this.mostrarModalLogout = false
      this.$emit('logout')
      // Opcional: Limpiar localStorage/sessionStorage
      localStorage.clear()
      sessionStorage.clear()
      // Redirigir al login
      this.$router.push('/login')
    },
    
    refreshCounts() {
      this.loadCounts()
    }
  }
}
</script>

<style scoped>
.brand-image-placeholder {
  width: 33px;
  height: 33px;
  background: linear-gradient(135deg, #0ea5e9, #06b6d4);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 16px;
  margin-right: 10px;
  float: left;
  margin-top: -3px;
  opacity: 0.9;
  box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
}

.user-avatar-placeholder {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
  box-shadow: 0 2px 4px rgba(0,0,0,.2);
}

.brand-link {
  display: block;
  padding: .8125rem .5rem;
  font-size: 1.25rem;
  line-height: 1.2;
  text-decoration: none;
  color: rgba(255,255,255,.8);
  white-space: nowrap;
  border-bottom: 1px solid #4f5962;
  transition: all .3s ease-in-out;
}

.brand-link:hover {
  color: #fff;
  text-decoration: none;
  background-color: rgba(255,255,255,.05);
}

.image {
  display: flex;
  align-items: center;
}

.brand-text {
  font-size: 1.1rem;
  line-height: 1.2;
  font-weight: 600;
}

.info {
  padding-left: 10px;
  display: flex;
  flex-direction: column;
}

.info a {
  color: #c2c7d0;
  font-weight: 500;
}

.info small {
  color: #8a9096;
  font-size: 0.75rem;
}

.nav-header {
  padding: .5rem .5rem;
  font-size: .75rem;
  color: #0ea5e9;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .5px;
  border-bottom: 1px solid rgba(14, 165, 233, 0.2);
  margin-bottom: 0.5rem;
}

.badge {
  font-size: 0.6rem;
  padding: 0.25em 0.4em;
  border-radius: 0.5rem;
}

.badge.right {
  float: right;
  margin-top: 3px;
  margin-right: 0.5rem;
}

.nav-treeview {
  transition: all 0.3s ease;
}

.nav-link:hover {
  background-color: rgba(14, 165, 233, 0.1);
  color: #0ea5e9;
  transform: translateX(2px);
  transition: all 0.2s ease;
}

.nav-link.active {
  background-color: #0ea5e9;
  color: #fff;
  box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
}

.nav-link.text-danger:hover {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545 !important;
}

.fas.fa-angle-left {
  transition: transform 0.3s ease;
}

.menu-open .fas.fa-angle-left {
  transform: rotate(-90deg);
}

.nav-treeview .nav-link {
  padding-left: 2.5rem;
  font-size: 0.9rem;
  border-left: 2px solid transparent;
}

.nav-treeview .nav-link:hover {
  border-left-color: #0ea5e9;
}

.nav-treeview .nav-link.active {
  border-left-color: #0ea5e9;
  background-color: rgba(14, 165, 233, 0.1);
  color: #0ea5e9;
}

.nav-treeview .nav-link .nav-icon {
  font-size: 0.8rem;
  margin-right: 0.5rem;
}

/* Estilos específicos para badges por categoría */
.badge-success { background-color: #28a745; }
.badge-info { background-color: #17a2b8; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-danger { background-color: #dc3545; }
.badge-primary { background-color: #0ea5e9; }
.badge-secondary { background-color: #6c757d; }
.badge-dark { background-color: #343a40; }

/* Estilos adicionales para mejor organización visual */
.nav-header:first-of-type {
  margin-top: 0.5rem;
}

.nav-item {
  margin-bottom: 2px;
}

.nav-link {
  border-radius: 6px;
  margin: 1px 8px;
}

/* Efectos hover mejorados */
.nav-link:hover .nav-icon {
  transform: scale(1.1);
  transition: transform 0.2s ease;
}

.nav-link:hover .badge {
  transform: scale(1.1);
  transition: transform 0.2s ease;
}

/* Modal de logout */
.modal-logout {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-logout-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-logout-header {
  padding: 25px;
  text-align: center;
  border-bottom: 2px solid #f0f0f0;
}

.modal-logout-header i {
  font-size: 3rem;
  color: #dc3545;
  margin-bottom: 15px;
}

.modal-logout-header h3 {
  margin: 0;
  color: #333;
  font-size: 22px;
  font-weight: 600;
}

.modal-logout-body {
  padding: 30px 25px;
  text-align: center;
}

.modal-logout-body p {
  margin: 0;
  color: #666;
  font-size: 16px;
  line-height: 1.5;
}

.modal-logout-footer {
  padding: 20px 25px;
  display: flex;
  justify-content: center;
  gap: 15px;
  border-top: 2px solid #f0f0f0;
}

.btn-cancelar,
.btn-confirmar {
  padding: 12px 30px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-cancelar {
  background: #6c757d;
  color: white;
}

.btn-cancelar:hover {
  background: #5a6268;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.btn-confirmar {
  background: #dc3545;
  color: white;
}

.btn-confirmar:hover {
  background: #c82333;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

@media (max-width: 767.98px) {
  .brand-text {
    display: none;
  }
  
  .badge.right {
    display: none;
  }
  
  .nav-header {
    font-size: 0.7rem;
  }
  
  .nav-link p {
    font-size: 0.85rem;
  }

  .modal-logout-content {
    width: 95%;
    margin: 15px;
  }

  .modal-logout-footer {
    flex-direction: column;
  }

  .btn-cancelar,
  .btn-confirmar {
    width: 100%;
  }
}

/* Scrollbar personalizado para el sidebar */
.sidebar::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-track {
  background: rgba(255,255,255,0.1);
}

.sidebar::-webkit-scrollbar-thumb {
  background: rgba(14, 165, 233, 0.3);
  border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
  background: rgba(14, 165, 233, 0.5);
}
</style>