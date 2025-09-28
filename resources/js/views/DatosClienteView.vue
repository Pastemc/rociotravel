<template>
  <div class="datos-cliente-container">
    <!-- Header de la Empresa -->
    <div class="empresa-header">
      <div class="empresa-info-centered">
        <div class="logo-section">
          <img :src="logoUrl" alt="Rocio Travel" class="empresa-logo" />
        </div>
        <div class="empresa-detalles">
          <h2 class="empresa-nombre">ROCIO TRAVEL</h2>
          <p class="empresa-descripcion">VENTA DE PASAJES FLUVIALES</p>
          <div class="empresa-servicios">
            <p>IQUITOS - YURIMAGUAS - PUCALLPA - SANTA ROSA - INTUTO - SAN LORENZO</p>
            <p>TROMPETEROS - PANTOJA - REQUENA - Y PUERTOS INTERMEDIOS</p>
          </div>
          <div class="empresa-contacto">
            <p><strong>Dirección:</strong> Calle. Pevas N° 366</p>
            <p><strong>Correo:</strong> travelrocio19@gmail.com</p>
            <p><strong>Contacto:</strong> +51901978379</p>
            <p><strong>Yape:</strong> 989667653</p>
            <p class="empresa-rutas"><strong>IQUITOS - MAYNAS - LORETO</strong></p>
          </div>
        </div>
      </div>
      <div class="fecha-emision">
        <div class="fecha-info">
          <p class="fecha-actual">{{ fechaActual }}</p>
          <p class="hora-actual">{{ horaActual }}</p>
          <div class="operador-info">
            <p><strong>Por operador: ROCÍO TRAVEL</strong></p>
            <p><strong>Fecha de emisión:</strong> {{ fechaActual }}</p>
            <p><strong>Hora de emisión:</strong> {{ horaActual }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Indicador de Proceso -->
    <div class="proceso-indicador">
      <div class="paso activo">
        <div class="paso-numero">1</div>
        <div class="paso-texto">Datos del Cliente</div>
      </div>
      <div class="paso-linea"></div>
      <div class="paso siguiente">
        <div class="paso-numero">2</div>
        <div class="paso-texto">Detalles del Pasaje</div>
      </div>
    </div>

    <!-- Formulario de Datos del Cliente -->
    <div class="cliente-form-section">
      <div class="section-title">
        <h3>Registro de Cliente</h3>
        <div v-if="clienteEncontrado" class="cliente-encontrado">
          <i class="fas fa-user-check"></i>
          <span>Cliente encontrado - Datos cargados automáticamente</span>
        </div>
      </div>
      
      <form @submit.prevent="guardarCliente" class="cliente-form">
        <div class="form-row">
          <div class="form-group">
            <label for="numDoc">Número de Documento</label>
            <input 
              type="text" 
              id="numDoc" 
              v-model="cliente.numero_documento"
              @input="validarNumeroDocumento"
              class="form-control"
              placeholder="8 dígitos numéricos"
              maxlength="8"
              pattern="[0-9]{8}"
              required
            />
            <div v-if="errorDocumento" class="error-message">
              <i class="fas fa-exclamation-triangle"></i>
              {{ errorDocumento }}
            </div>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input 
              type="text" 
              id="nombre" 
              v-model="cliente.nombre"
              class="form-control nombre-field"
              :class="{ 'auto-filled': clienteEncontrado }"
              placeholder="Nombre completo del cliente"
              required
            />
            <div v-if="clienteEncontrado" class="auto-filled-indicator">
              <i class="fas fa-magic"></i>
              <span>Cargado automáticamente</span>
            </div>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group contacto-group">
            <label for="contacto">Contacto</label>
            <input 
              type="text" 
              id="contacto" 
              v-model="cliente.contacto"
              class="form-control contacto-field"
              :class="{ 'auto-filled': clienteEncontrado }"
              placeholder="Teléfono/Celular"
            />
            <div v-if="clienteEncontrado" class="auto-filled-indicator">
              <i class="fas fa-magic"></i>
              <span>Cargado automáticamente</span>
            </div>
          </div>
          <div class="form-group nacionalidad-group">
            <label for="nacionalidad">Nacionalidad</label>
            <select 
              id="nacionalidad" 
              v-model="cliente.nacionalidad"
              class="form-control nacionalidad-field"
              :class="{ 'auto-filled': clienteEncontrado }"
              required
            >
              <option value="PERUANA">PERUANA</option>
              <option value="EXTRANJERA">EXTRANJERA</option>
            </select>
            <div v-if="clienteEncontrado" class="auto-filled-indicator">
              <i class="fas fa-magic"></i>
              <span>Cargado automáticamente</span>
            </div>
          </div>
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn btn-primary btn-large" :disabled="loading || !formularioValido">
            <i class="fas fa-arrow-right"></i>
            {{ loading ? 'Procesando...' : 'Continuar con este Cliente' }}
          </button>
          <button type="button" class="btn btn-secondary" @click="limpiarFormulario">
            <i class="fas fa-user-plus"></i>
            LIMPIAR
          </button>
        </div>
      </form>
    </div>

    <!-- Información del Cliente Encontrado -->
    <div v-if="clienteEncontrado" class="cliente-info-card">
      <div class="info-header">
        <i class="fas fa-history"></i>
        <h4>Historial del Cliente</h4>
      </div>
      <div class="info-content">
        <div class="info-item">
          <strong>Nombre:</strong> {{ cliente.nombre }}
        </div>
        <div class="info-item">
          <strong>Documento:</strong> {{ cliente.numero_documento }}
        </div>
        <div class="info-item">
          <strong>Contacto:</strong> {{ cliente.contacto || 'No registrado' }}
        </div>
        <div class="info-item">
          <strong>Nacionalidad:</strong> {{ cliente.nacionalidad }}
        </div>
        <div v-if="totalCompras > 0" class="info-item compras-totales">
          <strong>Total de viajes:</strong> {{ totalCompras }} {{ totalCompras === 1 ? 'viaje' : 'viajes' }}
        </div>
        <div v-if="ultimaCompra" class="info-item ultima-compra">
          <strong>Último viaje:</strong> {{ formatearFecha(ultimaCompra.created_at) }}
        </div>
      </div>
    </div>

    <!-- Mensaje de éxito/error -->
    <div v-if="mensaje.show" :class="['mensaje', mensaje.tipo]">
      <i :class="mensaje.icono"></i>
      {{ mensaje.texto }}
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'DatosClienteView',
  data() {
    return {
      logoUrl: '/images/rocio.jpg',
      fechaActual: '',
      horaActual: '',
      loading: false,
      clienteEncontrado: false,
      ultimaCompra: null,
      totalCompras: 0,
      errorDocumento: '',
      cliente: {
        numero_documento: '',
        nombre: '',
        contacto: '',
        nacionalidad: 'PERUANA'
      },
      mensaje: {
        show: false,
        texto: '',
        tipo: '',
        icono: ''
      }
    }
  },
  computed: {
    formularioValido() {
      return this.cliente.numero_documento.length === 8 && 
             this.cliente.nombre.trim() !== '' &&
             this.cliente.nacionalidad !== '' &&
             !this.errorDocumento
    }
  },
  mounted() {
    this.actualizarFechaHora()
    setInterval(this.actualizarFechaHora, 60000)
  },
  methods: {
    actualizarFechaHora() {
      const ahora = new Date()
      this.fechaActual = ahora.toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric'
      })
      this.horaActual = ahora.toLocaleTimeString('es-PE', {
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    validarNumeroDocumento(event) {
      // Solo permitir números
      let valor = event.target.value.replace(/[^0-9]/g, '')
      
      // Limitar a 8 dígitos
      if (valor.length > 8) {
        valor = valor.substring(0, 8)
      }
      
      this.cliente.numero_documento = valor
      
      // Validar longitud
      if (valor.length > 0 && valor.length < 8) {
        this.errorDocumento = 'El documento debe tener exactamente 8 dígitos'
      } else {
        this.errorDocumento = ''
      }
      
      // Si se borra el documento, limpiar datos
      if (valor.length === 0) {
        this.limpiarDatosCliente()
      }
      
      // AUTO-BUSCAR cuando tenga exactamente 8 dígitos
      if (valor.length === 8) {
        this.buscarClientePorDocumento()
      }
    },

    async buscarClientePorDocumento() {
      if (this.cliente.numero_documento.length !== 8) {
        return
      }

      try {
        this.loading = true
        console.log(`🔍 Buscando cliente con documento: ${this.cliente.numero_documento}`)
        
        const response = await axios.get(`/api/clientes/buscar-por-documento/${this.cliente.numero_documento}`)
        
        if (response.data.success && response.data.data) {
          // Cliente encontrado - cargar datos automáticamente
          const clienteExistente = response.data.data
          
          console.log('✅ Cliente encontrado:', clienteExistente)
          
          // Cargar todos los datos automáticamente
          this.cliente = {
            numero_documento: clienteExistente.numero_documento,
            nombre: clienteExistente.nombre,
            contacto: clienteExistente.contacto || '',
            nacionalidad: clienteExistente.nacionalidad || 'PERUANA'
          }
          
          this.clienteEncontrado = true
          this.ultimaCompra = clienteExistente.ultima_compra || null
          this.totalCompras = clienteExistente.total_compras || 0
          
          this.mostrarMensaje(
            `¡Cliente recurrente! Datos cargados automáticamente para ${clienteExistente.nombre}`, 
            'success', 
            'fas fa-user-check'
          )
        } else {
          // Cliente no encontrado - formulario limpio para nuevo registro
          console.log('ℹ️ Cliente no encontrado, será un cliente nuevo')
          
          this.clienteEncontrado = false
          this.ultimaCompra = null
          this.totalCompras = 0
          
          // Mantener solo el número de documento, limpiar el resto
          this.cliente.nombre = ''
          this.cliente.contacto = ''
          this.cliente.nacionalidad = 'PERUANA'
          
          this.mostrarMensaje('Cliente nuevo - Complete los datos para registrar', 'info', 'fas fa-user-plus')
        }
        
      } catch (error) {
        console.error('❌ Error buscando cliente:', error)
        this.clienteEncontrado = false
        this.ultimaCompra = null
        this.totalCompras = 0
        
        if (error.response?.status !== 404) {
          this.mostrarMensaje('Error al buscar cliente en el sistema', 'error', 'fas fa-exclamation-triangle')
        } else {
          // Error 404 significa que no existe, es normal
          this.mostrarMensaje('Cliente nuevo - Complete los datos para registrar', 'info', 'fas fa-user-plus')
        }
      } finally {
        this.loading = false
      }
    },
    
    async guardarCliente() {
      try {
        this.loading = true
        
        if (!this.formularioValido) {
          this.mostrarMensaje('Complete todos los campos correctamente', 'error', 'fas fa-exclamation-circle')
          return
        }
        
        let clienteData = { ...this.cliente }
        let esNuevoCliente = !this.clienteEncontrado
        
        if (this.clienteEncontrado) {
          // Cliente existente - solo continuar con los datos actuales
          console.log('📋 Continuando con cliente existente:', clienteData)
          this.mostrarMensaje('Continuando con cliente recurrente...', 'info', 'fas fa-arrow-right')
        } else {
          // Cliente nuevo - guardar en base de datos
          console.log('💾 Guardando cliente nuevo:', clienteData)
          
          const response = await axios.post('/api/clientes', this.cliente)
          
          if (response.data.success) {
            clienteData = response.data.data
            console.log('✅ Cliente nuevo guardado:', clienteData)
            this.mostrarMensaje('Cliente nuevo registrado exitosamente. Continuando...', 'success', 'fas fa-check-circle')
          } else {
            this.mostrarMensaje('Error al registrar cliente', 'error', 'fas fa-exclamation-circle')
            return
          }
        }
        
        // Guardar datos en sessionStorage para el siguiente paso
        const datosParaSessionStorage = {
          id: clienteData.id || null,
          nombre: clienteData.nombre,
          documento: clienteData.numero_documento,
          contacto: clienteData.contacto || '',
          nacionalidad: clienteData.nacionalidad || 'PERUANA',
          esClienteRecurrente: this.clienteEncontrado,
          totalViajes: this.totalCompras
        }
        
        sessionStorage.setItem('datosCliente', JSON.stringify(datosParaSessionStorage))
        
        console.log('💾 Datos guardados en sessionStorage:', datosParaSessionStorage)
        
        // Redirigir después de 2 segundos
        setTimeout(() => {
          this.irADetallesPasaje()
        }, 2000)
        
      } catch (error) {
        console.error('❌ Error al procesar cliente:', error)
        
        if (error.response?.data?.errors) {
          const errores = Object.values(error.response.data.errors).flat()
          this.mostrarMensaje(errores.join(', '), 'error', 'fas fa-exclamation-circle')
        } else {
          this.mostrarMensaje('Error al procesar cliente', 'error', 'fas fa-exclamation-circle')
        }
      } finally {
        this.loading = false
      }
    },
    
    irADetallesPasaje() {
      console.log('🚀 Navegando a detalles del pasaje')
      this.$router.push({
        name: 'AgregarDetalle'
      })
    },
    
    limpiarFormulario() {
      console.log('🧹 Limpiando formulario para nuevo cliente')
      
      this.cliente = {
        numero_documento: '',
        nombre: '',
        contacto: '',
        nacionalidad: 'PERUANA'
      }
      
      this.clienteEncontrado = false
      this.ultimaCompra = null
      this.totalCompras = 0
      this.errorDocumento = ''
      
      // Limpiar también sessionStorage
      sessionStorage.removeItem('datosCliente')
      
      this.mostrarMensaje('Formulario limpio para nuevo cliente', 'info', 'fas fa-broom')
    },

    limpiarDatosCliente() {
      this.cliente = {
        ...this.cliente,
        nombre: '',
        contacto: '',
        nacionalidad: 'PERUANA'
      }
      this.clienteEncontrado = false
      this.ultimaCompra = null
      this.totalCompras = 0
    },
    
    mostrarMensaje(texto, tipo, icono = '') {
      this.mensaje = {
        show: true,
        texto: texto,
        tipo: tipo,
        icono: icono
      }
      
      setTimeout(() => {
        this.mensaje.show = false
      }, 5000)
    },

    formatearFecha(fecha) {
      if (!fecha) return 'N/A'
      
      const date = new Date(fecha)
      return date.toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>

<style scoped>
.datos-cliente-container {
  background: white;
  min-height: 100vh;
  font-family: Arial, sans-serif;
}

/* Header de la empresa - LOGO MÁS GRANDE Y CENTRADO */
.empresa-header {
  background: white;
  padding: 40px;
  border-bottom: 3px solid #333;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.empresa-info-centered {
  display: flex;
  align-items: center;
  flex: 1;
  justify-content: center;
  text-align: center;
  gap: 40px;
}

.logo-section {
  flex-shrink: 0;
}

.empresa-logo {
  width: 180px;
  height: 180px;
  object-fit: cover;
  border-radius: 20px;
  border: 4px solid #0ea5e9;
  box-shadow: 0 8px 25px rgba(14, 165, 233, 0.3);
  transition: all 0.3s ease;
}

.empresa-logo:hover {
  transform: scale(1.02);
  box-shadow: 0 12px 35px rgba(14, 165, 233, 0.4);
}

.empresa-detalles {
  flex: 1;
  max-width: 650px;
}

.empresa-nombre {
  font-size: 32px;
  font-weight: bold;
  color: #0ea5e9;
  margin: 0 0 10px 0;
  letter-spacing: 3px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.empresa-descripcion {
  font-size: 18px;
  color: #666;
  margin: 0 0 18px 0;
  font-weight: 600;
}

.empresa-servicios {
  font-size: 13px;
  color: #555;
  margin-bottom: 22px;
  line-height: 1.5;
}

.empresa-servicios p {
  margin: 4px 0;
}

.empresa-contacto {
  font-size: 14px;
  color: #444;
  line-height: 1.6;
}

.empresa-contacto p {
  margin: 6px 0;
}

.empresa-rutas {
  font-weight: bold;
  margin-top: 15px;
  color: #0ea5e9;
  font-size: 15px;
}

.fecha-emision {
  text-align: right;
  font-size: 12px;
  color: #333;
  min-width: 220px;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  padding: 20px;
  border-radius: 12px;
  border: 2px solid #dee2e6;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.fecha-info p {
  margin: 4px 0;
}

.fecha-actual, .hora-actual {
  font-weight: bold;
  font-size: 16px;
  color: #0ea5e9;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.operador-info p {
  font-size: 12px;
  margin: 3px 0;
}

/* Indicador de Proceso */
.proceso-indicador {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  border-bottom: 2px solid #e5e7eb;
}

.paso {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.paso-numero {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 20px;
  margin-bottom: 12px;
  transition: all 0.3s ease;
}

.paso.activo .paso-numero {
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
  transform: scale(1.1);
}

.paso.siguiente .paso-numero {
  background: #e5e7eb;
  color: #9ca3af;
}

.paso-texto {
  font-size: 16px;
  font-weight: 600;
}

.paso.activo .paso-texto {
  color: #0ea5e9;
}

.paso.siguiente .paso-texto {
  color: #9ca3af;
}

.paso-linea {
  width: 140px;
  height: 4px;
  background: linear-gradient(90deg, #e5e7eb, #d1d5db);
  margin: 0 35px;
  border-radius: 2px;
}

/* Formulario de cliente */
.cliente-form-section {
  padding: 50px;
  max-width: 900px;
  margin: 0 auto;
}

.section-title {
  text-align: center;
  margin-bottom: 40px;
  border-bottom: 4px solid #0ea5e9;
  padding-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-title h3 {
  color: #0ea5e9;
  font-size: 28px;
  font-weight: 700;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 2px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.cliente-encontrado {
  display: flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(135deg, #d4edda, #c3e6cb);
  color: #155724;
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 600;
  border: 2px solid #c3e6cb;
  box-shadow: 0 4px 12px rgba(21, 87, 36, 0.15);
  animation: pulse 1.5s ease-in-out;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.cliente-form {
  background: linear-gradient(135deg, #f8f9fa, #ffffff);
  padding: 40px;
  border-radius: 16px;
  border: 3px solid #e0e0e0;
  box-shadow: 0 12px 35px rgba(0,0,0,0.15);
}

.form-row {
  display: flex;
  gap: 30px;
  margin-bottom: 30px;
}

.form-group {
  flex: 1;
  position: relative;
}

.form-group label {
  display: block;
  font-weight: 700;
  color: #333;
  margin-bottom: 12px;
  font-size: 16px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.form-control {
  width: 100%;
  padding: 18px 22px;
  border: 3px solid #ddd;
  border-radius: 10px;
  font-size: 16px;
  transition: all 0.3s ease;
  background: white;
  font-weight: 500;
}

.form-control:focus {
  outline: none;
  border-color: #0ea5e9;
  box-shadow: 0 0 20px rgba(14, 165, 233, 0.3);
  transform: translateY(-2px);
}

/* Estilos para campos auto-llenados */
.form-control.auto-filled {
  background: linear-gradient(135deg, #e8f5e8, #d4edda);
  border-color: #28a745;
  color: #155724;
  font-weight: 600;
  box-shadow: 0 0 15px rgba(40, 167, 69, 0.2);
}

.auto-filled-indicator {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 5px;
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: #28a745;
  font-weight: 600;
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.auto-filled-indicator i {
  font-size: 14px;
  color: #28a745;
}

.nombre-field {
  flex: 2;
}

.contacto-group {
  flex: 1.2;
}

.nacionalidad-group {
  flex: 0.8;
}

.error-message {
  margin-top: 10px;
  color: #dc3545;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 600;
}

.form-actions {
  display: flex;
  gap: 25px;
  margin-top: 40px;
  justify-content: center;
}

.btn {
  padding: 18px 35px;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 12px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.btn-large {
  padding: 22px 40px;
  font-size: 17px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-primary {
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #0284c7, #0369a1);
  transform: translateY(-4px);
  box-shadow: 0 12px 35px rgba(14, 165, 233, 0.5);
}

.btn-secondary {
  background: linear-gradient(135deg, #6c757d, #5a6268);
  color: white;
  box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

.btn-secondary:hover {
  background: linear-gradient(135deg, #5a6268, #495057);
  transform: translateY(-4px);
  box-shadow: 0 12px 35px rgba(108, 117, 125, 0.5);
}

/* Cliente Info Card */
.cliente-info-card {
  max-width: 900px;
  margin: 25px auto;
  background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
  border: 3px solid #0ea5e9;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 12px 35px rgba(14, 165, 233, 0.2);
  animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.info-header {
  background: linear-gradient(135deg, #0ea5e9, #0284c7);
  color: white;
  padding: 25px;
  display: flex;
  align-items: center;
  gap: 15px;
}

.info-header i {
  font-size: 28px;
}

.info-header h4 {
  margin: 0;
  font-size: 22px;
  font-weight: 700;
}

.info-content {
  padding: 30px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 15px 0;
  border-bottom: 2px solid rgba(14, 165, 233, 0.1);
  font-size: 17px;
}

.info-item:last-child {
  border-bottom: none;
}

.info-item strong {
  color: #0ea5e9;
  font-weight: 700;
}

.compras-totales {
  background: rgba(40, 167, 69, 0.1);
  margin: 15px -30px;
  padding: 20px 30px;
  border-radius: 8px;
  border: 2px solid rgba(40, 167, 69, 0.2);
}

.compras-totales strong {
  color: #28a745;
}

.ultima-compra {
  background: rgba(14, 165, 233, 0.08);
  margin: 20px -30px -30px -30px;
  padding: 25px 30px;
  border-top: 3px solid rgba(14, 165, 233, 0.3);
}

/* Mensajes */
.mensaje {
  position: fixed;
  top: 40px;
  right: 40px;
  padding: 25px 30px;
  border-radius: 12px;
  color: white;
  font-weight: 700;
  z-index: 1001;
  animation: slideIn 0.4s ease;
  max-width: 500px;
  display: flex;
  align-items: center;
  gap: 15px;
  box-shadow: 0 12px 35px rgba(0,0,0,0.25);
}

.mensaje.success {
  background: linear-gradient(135deg, #28a745, #20c997);
}

.mensaje.error {
  background: linear-gradient(135deg, #dc3545, #e74c3c);
}

.mensaje.info {
  background: linear-gradient(135deg, #17a2b8, #20c997);
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .empresa-header {
    flex-direction: column;
    gap: 25px;
    padding: 25px;
  }
  
  .empresa-info-centered {
    flex-direction: column;
    gap: 25px;
  }
  
  .empresa-logo {
    width: 140px;
    height: 140px;
  }
  
  .empresa-nombre {
    font-size: 26px;
  }
  
  .form-row {
    flex-direction: column;
    gap: 25px;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .proceso-indicador {
    flex-direction: column;
    gap: 25px;
  }
  
  .paso-linea {
    width: 4px;
    height: 50px;
  }
  
  .cliente-form-section {
    padding: 25px;
  }
  
  .cliente-form {
    padding: 30px;
  }

  .section-title {
    flex-direction: column;
    gap: 20px;
  }
  
  .info-item {
    flex-direction: column;
    gap: 8px;
  }

  .auto-filled-indicator {
    position: static;
    margin-top: 8px;
    margin-bottom: 15px;
  }
}
</style>