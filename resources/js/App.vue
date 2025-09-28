<template>
  <div id="app-root">
    <!-- Pantalla de Carga Inicial -->
    <div v-show="isInitialLoading" class="initial-loading">
      <div class="loading-animation">
        <div class="ship-container">
          <div class="ship">
            <div class="ship-body"></div>
            <div class="ship-sail"></div>
            <div class="ship-flag"></div>
          </div>
          <div class="water-waves">
            <div class="wave wave-1"></div>
            <div class="wave wave-2"></div>
            <div class="wave wave-3"></div>
          </div>
        </div>
        <div class="loading-text">
          <h2>Rocío Travel</h2>
          <p>{{ loadingMessage }}</p>
          <div class="loading-bar">
            <div class="loading-progress" :style="{ width: loadingProgress + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pantalla de Carga de Login -->
    <div v-show="isLoginLoading" class="login-loading-overlay">
      <div class="login-loading-content">
        <div class="anchor-loading">
          <i class="fas fa-anchor"></i>
          <div class="loading-ripples">
            <div class="ripple"></div>
            <div class="ripple"></div>
            <div class="ripple"></div>
          </div>
        </div>
        <h3>{{ loginLoadingMessage }}</h3>
        <div class="dots-loading">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </div>

    <!-- Login Page Fluvial -->
    <div v-show="!isAuthenticated && !isInitialLoading && !isLoginLoading" class="fluvial-login-page">
      <div class="water-animation"></div>
      <div class="login-container">
        <div class="login-grid">
          <!-- Sección de Imagen -->
          <div class="image-section">
            <img :src="imageUrl" alt="Rocío Travel" class="background-image" />
            <div class="water-overlay"></div>
          </div>
          
          <!-- Sección del Formulario -->
          <div class="form-section">
            <div class="form-content">
              <!-- Logo -->
              <div class="header-logo">
                <div class="logo-container">
                  <img :src="logoUrl" alt="Rocío Travel Logo" class="logo-image" />
                </div>
                <div class="logo-text-group">
                  <h2 class="logo-text">Rocío Travel</h2>
                  <p class="logo-subtitle">Navegando hacia tu destino</p>
                </div>
              </div>
              
              <!-- Subtítulo -->
              <h3 class="subtitle">Inicia sesión en tu cuenta</h3>
              
              <!-- Formulario -->
              <form @submit.prevent="handleLogin" class="login-form">
                <div class="field-group">
                  <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input 
                      v-model="email" 
                      type="email" 
                      class="input-field" 
                      placeholder="Correo electrónico"
                      :class="{ 'input-error': errorType === 'email' }"
                      required
                    />
                  </div>
                </div>
                
                <div class="field-group">
                  <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                      v-model="password" 
                      type="password" 
                      class="input-field" 
                      placeholder="Contraseña"
                      :class="{ 'input-error': errorType === 'password' }"
                      required
                    />
                  </div>
                </div>
                
                <div class="remember-group">
                  <label class="remember-checkbox">
                    <input type="checkbox" v-model="remember">
                    <span class="checkmark"></span>
                    <span class="checkbox-text">Recordar sesión</span>
                  </label>
                </div>
                
                <div v-if="error" class="error-box">
                  <i class="fas fa-exclamation-triangle"></i>
                  {{ error }}
                </div>
                
                <button type="submit" class="submit-btn" :disabled="loading">
                  <i class="fas fa-anchor btn-icon"></i>
                  <span v-if="loading">ZARPANDO...</span>
                  <span v-else>ZARPAR AHORA</span>
                </button>
                
                <div class="login-footer">
                  <p>¿Problemas para navegar? <a href="#" class="help-link">Contacta al capitán</a></p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard (AdminLTE) -->
    <div v-show="isAuthenticated && !isInitialLoading && !isLoginLoading" class="wrapper">
      <!-- Navbar -->
      <TopBar @logout="handleLogout" :currentUser="currentUser" />
      
      <!-- Main Sidebar Container -->
      <Sidebar :currentUser="currentUser" />
      
      <!-- Content Wrapper -->
      <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">Rocío Travel Fluvial</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><RouterLink to="/">Inicio</RouterLink></li>
                  <li class="breadcrumb-item active">Panel</li>
                </ol>
              </div>
            </div>
          </div>
        </div>

        <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <RouterView />
          </div>
        </div>
      </div>

      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <div class="p-3">
          <h5>Configuración</h5>
          <p>Gestión de pasajes fluviales</p>
        </div>
      </aside>

      <!-- Main Footer -->
      <Footer />
    </div>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar.vue'
import TopBar from './components/TopBar.vue'
import Footer from './components/Footer.vue'

export default {
  name: "App",
  components: {
    Sidebar,
    TopBar,
    Footer
  },
  data() {
    return {
      isAuthenticated: false,
      isInitialLoading: true,
      isLoginLoading: false,
      currentUser: null,
      email: '',
      password: '',
      remember: false,
      error: '',
      errorType: '',
      loading: false,
      imageUrl: '/images/rocio.jpg',
      logoUrl: '/images/rocio.jpg',
      loadingProgress: 0,
      loadingMessage: 'Preparando embarcación...',
      loginLoadingMessage: 'Abordando...'
    }
  },
  methods: {
    startInitialLoading() {
      const messages = [
        'Preparando embarcación...',
        'Verificando condiciones del río...',
        'Cargando sistema de navegación...',
        'Ajustando velas...',
        'Iniciando motores...',
        'Listo para zarpar...'
      ];
      
      let messageIndex = 0;
      let progress = 0;
      
      const interval = setInterval(() => {
        progress += Math.random() * 15 + 10;
        if (progress > 100) progress = 100;
        
        this.loadingProgress = progress;
        
        if (messageIndex < messages.length - 1 && progress > (messageIndex + 1) * 16) {
          messageIndex++;
          this.loadingMessage = messages[messageIndex];
        }
        
        if (progress >= 100) {
          clearInterval(interval);
          setTimeout(() => {
            this.isInitialLoading = false;
          }, 500);
        }
      }, 300);
    },

    async handleLogin() {
      this.loading = true
      this.error = ''
      this.errorType = ''
      
      try {
        const response = await axios.post('/api/auth/login', {
          email: this.email,
          password: this.password
        })
        
        if (response.data.success) {
          // Mostrar pantalla de carga de login
          this.isLoginLoading = true;
          this.loading = false;
          
          // Simular proceso de ingreso al sistema con mensajes progresivos
          const loginMessages = [
            'Abordando...',
            'Verificando credenciales...',
            'Preparando cabina...',
            'Cargando datos del capitán...',
            'Configurando panel de control...',
            '¡Bienvenido a bordo!'
          ];
          
          let messageIndex = 0;
          const loginInterval = setInterval(() => {
            if (messageIndex < loginMessages.length) {
              this.loginLoadingMessage = loginMessages[messageIndex];
              messageIndex++;
            }
          }, 800);
          
          // Esperar 5 segundos antes de mostrar el dashboard
          setTimeout(() => {
            clearInterval(loginInterval);
            this.isLoginLoading = false;
            this.isAuthenticated = true;
            this.currentUser = response.data.user;
            
            // Guardar sesión
            localStorage.setItem('authenticated', 'true');
            localStorage.setItem('user_id', response.data.user.id);
            localStorage.setItem('user_data', JSON.stringify(response.data.user));
            
            // Mostrar mensaje de bienvenida
            this.showToast(`¡Bienvenido a bordo, ${response.data.user.name}!`, 'success');
          }, 5000);
        }
      } catch (error) {
        this.loading = false;
        if (error.response?.data) {
          this.error = error.response.data.message;
          this.errorType = error.response.data.error_type;
        } else {
          this.error = 'Error de conexión. Intente nuevamente';
        }
      }
    },
    
    handleLogout() {
      this.isAuthenticated = false
      this.currentUser = null
      this.email = ''
      this.password = ''
      this.remember = false
      
      // Limpiar toda la sesión
      localStorage.removeItem('authenticated')
      localStorage.removeItem('user_id')
      localStorage.removeItem('user_data')
      localStorage.removeItem('current_route')
      
      this.showToast('¡Hasta la próxima navegación!', 'info')
    },
    
    async verifySession() {
      const userId = localStorage.getItem('user_id')
      const userData = localStorage.getItem('user_data')
      
      if (userId && userData) {
        try {
          const response = await axios.post('/api/auth/verify', {
            user_id: userId
          })
          
          if (response.data.success) {
            // Esperar a que termine la carga inicial antes de mostrar el dashboard
            setTimeout(() => {
              this.isAuthenticated = true;
              this.currentUser = response.data.user;
              
              // Restaurar ruta actual
              this.$nextTick(() => {
                const savedRoute = localStorage.getItem('current_route');
                if (savedRoute && savedRoute !== '/' && this.$route.path !== savedRoute) {
                  this.$router.push(savedRoute);
                }
              });
            }, 100);
          } else {
            this.handleLogout()
          }
        } catch (error) {
          this.handleLogout()
        }
      }
    },
    
    showToast(message, type = 'info') {
      const toastClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'info': 'alert-info'
      }[type] || 'alert-info'
      
      const toast = document.createElement('div')
      toast.className = `alert ${toastClass} position-fixed`
      toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;'
      toast.innerHTML = `
        ${message}
        <button type="button" class="close" onclick="this.parentElement.remove()">
          <span>&times;</span>
        </button>
      `
      document.body.appendChild(toast)
      setTimeout(() => toast.remove(), 4000)
    }
  },
  
  async mounted() {
    // Iniciar carga inicial
    this.startInitialLoading();
    
    // Verificar sesión mientras se carga
    await this.verifySession();
  }
}
</script>

<style scoped>
/* Estilos para la carga inicial */
.initial-loading {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #0c4a6e 0%, #1e40af 50%, #3b82f6 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
}

.loading-animation {
  text-align: center;
  color: white;
}

.ship-container {
  position: relative;
  margin-bottom: 40px;
}

.ship {
  position: relative;
  width: 100px;
  height: 60px;
  margin: 0 auto;
  animation: shipRock 3s ease-in-out infinite;
}

.ship-body {
  width: 80px;
  height: 30px;
  background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
  border-radius: 0 0 40px 40px;
  position: relative;
  margin: 0 auto;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.ship-sail {
  width: 40px;
  height: 50px;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  position: absolute;
  top: -45px;
  left: 50%;
  transform: translateX(-50%);
  clip-path: polygon(0% 100%, 0% 20%, 80% 0%, 100% 80%);
  animation: sailWave 2s ease-in-out infinite;
}

.ship-flag {
  width: 20px;
  height: 15px;
  background: #ef4444;
  position: absolute;
  top: -40px;
  right: 10px;
  clip-path: polygon(0% 0%, 85% 0%, 100% 50%, 85% 100%, 0% 100%);
  animation: flagWave 1s ease-in-out infinite;
}

@keyframes shipRock {
  0%, 100% { transform: rotate(-2deg) translateY(0px); }
  50% { transform: rotate(2deg) translateY(-5px); }
}

@keyframes sailWave {
  0%, 100% { transform: translateX(-50%) skewX(-2deg); }
  50% { transform: translateX(-50%) skewX(2deg); }
}

@keyframes flagWave {
  0%, 100% { transform: scaleX(1); }
  50% { transform: scaleX(0.8); }
}

.water-waves {
  display: flex;
  justify-content: center;
  align-items: flex-end;
  height: 20px;
  margin-top: 10px;
}

.wave {
  width: 40px;
  height: 8px;
  background: rgba(59, 130, 246, 0.6);
  border-radius: 10px;
  margin: 0 2px;
  animation: wave 2s ease-in-out infinite;
}

.wave-1 { animation-delay: 0s; }
.wave-2 { animation-delay: 0.2s; }
.wave-3 { animation-delay: 0.4s; }

@keyframes wave {
  0%, 100% { height: 8px; }
  50% { height: 15px; }
}

.loading-text h2 {
  font-size: 36px;
  font-weight: 700;
  margin: 0 0 10px 0;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.loading-text p {
  font-size: 18px;
  margin: 0 0 30px 0;
  opacity: 0.9;
}

.loading-bar {
  width: 300px;
  height: 8px;
  background: rgba(255,255,255,0.2);
  border-radius: 10px;
  margin: 0 auto;
  overflow: hidden;
}

.loading-progress {
  height: 100%;
  background: linear-gradient(90deg, #0ea5e9, #06b6d4);
  border-radius: 10px;
  transition: width 0.3s ease;
  box-shadow: 0 0 20px rgba(14, 165, 233, 0.5);
}

/* Estilos para la carga de login */
.login-loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(12, 74, 110, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(10px);
}

.login-loading-content {
  text-align: center;
  color: white;
}

.anchor-loading {
  position: relative;
  margin-bottom: 30px;
}

.anchor-loading .fas {
  font-size: 60px;
  color: #0ea5e9;
  animation: anchorSpin 2s linear infinite;
}

.loading-ripples {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.ripple {
  position: absolute;
  border: 2px solid #0ea5e9;
  border-radius: 50%;
  animation: ripple 2s linear infinite;
}

.ripple:nth-child(1) {
  width: 100px;
  height: 100px;
  margin: -50px 0 0 -50px;
  animation-delay: 0s;
}

.ripple:nth-child(2) {
  width: 120px;
  height: 120px;
  margin: -60px 0 0 -60px;
  animation-delay: 0.5s;
}

.ripple:nth-child(3) {
  width: 140px;
  height: 140px;
  margin: -70px 0 0 -70px;
  animation-delay: 1s;
}

@keyframes anchorSpin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes ripple {
  0% {
    opacity: 1;
    transform: scale(0);
  }
  100% {
    opacity: 0;
    transform: scale(1);
  }
}

.login-loading-content h3 {
  font-size: 24px;
  margin: 0 0 20px 0;
  font-weight: 300;
}

.dots-loading {
  display: flex;
  justify-content: center;
  align-items: center;
}

.dots-loading span {
  width: 12px;
  height: 12px;
  background: #0ea5e9;
  border-radius: 50%;
  margin: 0 5px;
  animation: dotPulse 1.4s ease-in-out infinite both;
}

.dots-loading span:nth-child(1) { animation-delay: -0.32s; }
.dots-loading span:nth-child(2) { animation-delay: -0.16s; }
.dots-loading span:nth-child(3) { animation-delay: 0s; }

@keyframes dotPulse {
  0%, 80%, 100% {
    transform: scale(0.8);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Estilos existentes del login */
.fluvial-login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0c4a6e 0%, #1e40af 50%, #3b82f6 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  position: relative;
  overflow: hidden;
}

.water-animation {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(14, 165, 233, 0.2) 0%, transparent 50%),
    radial-gradient(circle at 40% 40%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
  animation: waterFlow 15s ease-in-out infinite;
}

@keyframes waterFlow {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(-10px, -10px) rotate(1deg); }
  66% { transform: translate(10px, -5px) rotate(-1deg); }
}

.login-container {
  max-width: 950px;
  width: 100%;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 25px;
  overflow: hidden;
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(10px);
  z-index: 1;
}

.login-grid {
  display: flex;
  min-height: 600px;
}

.image-section {
  flex: 1;
  position: relative;
  overflow: hidden;
}

.background-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.water-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(12, 74, 110, 0.4), rgba(30, 64, 175, 0.3));
}

.form-section {
  flex: 1;
  background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
  padding: 60px 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-content {
  width: 100%;
  max-width: 380px;
}

.header-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 40px;
  flex-direction: column;
}

.logo-container {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 15px;
  box-shadow: 0 10px 30px rgba(14, 165, 233, 0.4);
  border: 3px solid rgba(255, 255, 255, 0.2);
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.logo-text-group {
  text-align: center;
}

.logo-text {
  color: white;
  font-size: 36px;
  font-weight: 700;
  margin: 0;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.logo-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 14px;
  margin: 5px 0 0 0;
  font-style: italic;
}

.subtitle {
  color: rgba(255, 255, 255, 0.9);
  font-size: 20px;
  font-weight: 300;
  margin-bottom: 40px;
  text-align: center;
}

.login-form {
  width: 100%;
}

.field-group {
  margin-bottom: 25px;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(255, 255, 255, 0.7);
  font-size: 16px;
  z-index: 2;
}

.input-field {
  width: 100%;
  background: rgba(255, 255, 255, 0.15);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 15px;
  padding: 20px 20px 20px 55px;
  color: white;
  font-size: 16px;
  transition: all 0.3s ease;
  outline: none;
  backdrop-filter: blur(10px);
}

.input-field::placeholder {
  color: rgba(255, 255, 255, 0.7);
}

.input-field:focus {
  border-color: #0ea5e9;
  background: rgba(255, 255, 255, 0.25);
  box-shadow: 0 0 30px rgba(14, 165, 233, 0.4);
  transform: translateY(-2px);
}

.input-error {
  border-color: #ef4444 !important;
  background: rgba(239, 68, 68, 0.1) !important;
}

.remember-group {
  margin-bottom: 30px;
}

.remember-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  user-select: none;
}

.remember-checkbox input[type="checkbox"] {
  opacity: 0;
  position: absolute;
}

.checkmark {
  width: 20px;
  height: 20px;
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-radius: 4px;
  margin-right: 12px;
  position: relative;
  transition: all 0.3s ease;
}

.remember-checkbox input[type="checkbox"]:checked + .checkmark {
  background: #0ea5e9;
  border-color: #0ea5e9;
}

.remember-checkbox input[type="checkbox"]:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.checkbox-text {
  color: rgba(255, 255, 255, 0.9);
  font-size: 14px;
}

.error-box {
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
  padding: 15px 20px;
  border-radius: 12px;
  margin-bottom: 25px;
  text-align: center;
  font-size: 14px;
  border: 1px solid rgba(239, 68, 68, 0.4);
  backdrop-filter: blur(10px);
}

.error-box i {
  margin-right: 8px;
}

.submit-btn {
  width: 100%;
  background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
  color: white;
  border: none;
  border-radius: 15px;
  padding: 20px 30px;
  font-size: 16px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 10px 30px rgba(14, 165, 233, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon {
  margin-right: 10px;
  font-size: 18px;
}

.submit-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #0284c7 0%, #0891b2 100%);
  transform: translateY(-3px);
  box-shadow: 0 15px 40px rgba(14, 165, 233, 0.5);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-footer {
  text-align: center;
  margin-top: 30px;
}

.login-footer p {
  color: rgba(255, 255, 255, 0.8);
  font-size: 14px;
  margin: 0;
}

.help-link {
  color: #0ea5e9;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.help-link:hover {
  color: #06b6d4;
  text-decoration: underline;
}

@media (max-width: 768px) {
  .login-grid {
    flex-direction: column;
  }
  
  .image-section {
    min-height: 250px;
  }
  
  .form-section {
    padding: 40px 30px;
  }
  
  .logo-text {
    font-size: 28px;
  }
  
  .logo-container {
    width: 60px;
    height: 60px;
  }
  
  .initial-loading .ship {
    width: 80px;
    height: 50px;
  }
  
  .initial-loading .ship-body {
    width: 60px;
    height: 25px;
  }
  
  .initial-loading .ship-sail {
    width: 30px;
    height: 40px;
    top: -35px;
  }
  
  .loading-text h2 {
    font-size: 28px;
  }
  
  .loading-text p {
    font-size: 16px;
  }
  
  .loading-bar {
    width: 250px;
  }
  
  .anchor-loading .fas {
    font-size: 50px;
  }
  
  .login-loading-content h3 {
    font-size: 20px;
  }
}
</style>