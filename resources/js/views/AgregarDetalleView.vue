<template>
  <div class="travel-search-container">
    <!-- Header -->
    <div class="header">
      <div class="header-top">
        <h2>Agregar Detalle de Pasaje</h2>
        <!-- Botón para abrir modal -->
        <button class="modal-button" @click="abrirModal">
          <i class="fas fa-edit"></i>
          Agregar/Actualizar
        </button>
      </div>
      
      <!-- Mostrar datos del cliente actual -->
      <div v-if="clienteActual" class="cliente-info">
        <div class="cliente-card">
          <i class="fas fa-user"></i>
          <div class="cliente-datos">
            <p><strong>Cliente:</strong> {{ clienteActual.nombre }}</p>
            <p><strong>Documento:</strong> {{ clienteActual.documento }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Formulario principal -->
    <div class="search-form">
      <!-- Origen y Destino de Ruta -->
      <div class="route-section">
        <div class="input-group origen-group">
          <label for="origen">Destino</label>
          <select 
            v-if="!modoManualOrigen"
            id="origen" 
            v-model="formData.origen" 
            class="location-input"
            @change="actualizarDescripcion"
          >
            <option value="">Seleccionar origen</option>
            <option value="PUCALLPA">Pucallpa</option>
            <option value="YURIMAGUAS">Yurimaguas</option>
            <option value="FRONTERA">Frontera</option>
            <option value="OTROS">Otros</option>
          </select>
          <input 
            v-else
            id="origenManual"
            type="text" 
            v-model="formData.origen" 
            class="location-input manual-input"
            placeholder="Destino personalizado"
            readonly
          />
          <button 
            v-if="modoManualOrigen" 
            @click="volverASelectOrigen" 
            class="reset-button"
            title="Volver a opciones predefinidas"
          >
            <i class="fas fa-undo"></i>
          </button>
        </div>

        <button class="swap-button" @click="intercambiarRutas">
          <i class="fas fa-exchange-alt"></i>
        </button>

        <div class="input-group destino-group">
          <label for="destinoRuta">Ruta</label>
          <select 
            v-if="!modoManualRuta"
            id="destinoRuta" 
            v-model="formData.destinoRuta" 
            class="location-input"
            @change="actualizarDescripcion"
          >
            <option value="">Seleccionar destino</option>
            <option value="PUCALLPA">Pucallpa</option>
            <option value="REQUENA">Requena</option>
            <option value="YURIMAGUAS">Yurimaguas</option>
            <option value="SAN LORENZO">San Lorenzo</option>
            <option value="SANTA ROSA">Santa Rosa</option>
            <option value="TROMPETERO">Trompetero</option>
            <option value="INTUTO">Intuto</option>
          </select>
          <input 
            v-else
            id="rutaManual"
            type="text" 
            v-model="formData.destinoRuta" 
            class="location-input manual-input"
            placeholder="Ruta personalizada"
            readonly
          />
          <button 
            v-if="modoManualRuta" 
            @click="volverASelectRuta" 
            class="reset-button"
            title="Volver a opciones predefinidas"
          >
            <i class="fas fa-undo"></i>
          </button>
        </div>
      </div>

      <!-- Cantidad fija -->
      <div class="cantidad-section">
        <div class="cantidad-group">
          <label>Cantidad</label>
          <div class="cantidad-display">
            <span class="cantidad-number">{{ formData.cantidad }}</span>
          </div>
        </div>
      </div>

      <!-- Precio -->
      <div class="price-section">
        <div class="price-group">
          <label for="precio">Precio Unitario</label>
          <input 
            id="precio"
            type="number" 
            v-model="formData.precioUnitario" 
            class="price-input"
            placeholder="0.00"
            step="0.01"
            @input="calcularYEnviarDatos"
          />
        </div>
        <div class="price-total">
          <span class="total-label">Total:</span>
          <span class="total-amount">S/ {{ precioTotal.toFixed(2) }}</span>
        </div>
      </div>

      <!-- Botón de agregar -->
      <button class="add-button" @click="agregarPasaje" :disabled="cargandoAgregar">
        <i class="fas fa-plus" v-if="!cargandoAgregar"></i>
        <i class="fas fa-spinner fa-spin" v-if="cargandoAgregar"></i>
        {{ cargandoAgregar ? 'Agregando...' : 'Agregar' }}
      </button>

      <!-- DEBUG: Mostrar datos que se están enviando -->
      <div v-if="formData.descripcion && formData.precioUnitario" class="debug-info">
        <h4>Datos que se enviarán:</h4>
        <p><strong>Cantidad:</strong> {{ formData.cantidad }}</p>
        <p><strong>Descripción:</strong> {{ formData.descripcion }}</p>
        <p><strong>Precio Unitario:</strong> {{ parseFloat(formData.precioUnitario).toFixed(2) }}</p>
        <p><strong>Subtotal:</strong> {{ precioTotal.toFixed(2) }}</p>
        <p v-if="formData.embarcacion"><strong>Embarcación:</strong> {{ formData.embarcacion }}</p>
        <p v-if="formData.puertoEmbarque"><strong>Puerto:</strong> {{ formData.puertoEmbarque }}</p>
        <p v-if="!formData.embarcacion || !formData.puertoEmbarque" class="optional-info">
          <i class="fas fa-info-circle"></i>
          Embarcación y puerto se pueden agregar opcionalmente desde "Agregar/Actualizar"
        </p>
      </div>
    </div>

    <!-- Modal MEJORADO -->
    <div v-if="mostrarModal" class="modal-overlay" @click="cerrarModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3>Agregar/Actualizar Detalles</h3>
          <button class="modal-close" @click="cerrarModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="modal-body">
          <!-- Sección Ruta -->
          <div class="modal-section">
            <h4 class="section-title">
              <i class="fas fa-route"></i>
              Actualizar Ruta del Viaje
            </h4>
            
            <div class="modal-route-section">
              <div class="modal-input-group">
                <label for="modalDetalle">Detalle</label>
                <input 
                  id="modalDetalle"
                  type="text" 
                  v-model="modalData.origen" 
                  class="modal-input"
                  placeholder="Ingrese el detalle del origen"
                />
              </div>

              <div class="modal-swap-container">
                <button class="modal-swap-button" @click="intercambiarRutasModal">
                  <i class="fas fa-exchange-alt"></i>
                </button>
              </div>

              <div class="modal-input-group">
                <label for="modalRuta">Ruta</label>
                <input 
                  id="modalRuta"
                  type="text" 
                  v-model="modalData.destinoRuta" 
                  class="modal-input"
                  placeholder="Ingrese la ruta de destino"
                />
              </div>
            </div>
          </div>

          <!-- SECCIÓN OPCIONAL: Embarcación y Puerto -->
          <div class="modal-section">
            <h4 class="section-title">
              <i class="fas fa-ship"></i>
              Detalles del Viaje (Opcional)
            </h4>
            <p class="section-description">
              <i class="fas fa-info-circle"></i>
              Estos campos son opcionales. Se pueden completar aquí o después en la vista de detalles.
            </p>
            
            <div class="modal-details-section">
              <div class="modal-input-group">
                <label for="modalEmbarcacion">Embarcación</label>
                <input 
                  id="modalEmbarcacion"
                  type="text"
                  v-model="modalData.embarcacion" 
                  class="modal-input"
                  placeholder="Nombre de la embarcación (opcional)"
                />
              </div>

              <div class="modal-input-group">
                <label for="modalPuerto">Puerto de embarque</label>
                <input 
                  id="modalPuerto"
                  type="text"
                  v-model="modalData.puertoEmbarque" 
                  class="modal-input"
                  placeholder="Puerto de embarque (opcional)"
                />
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button class="btn-modal-cancel" @click="cerrarModal">
            <i class="fas fa-times"></i>
            Cancelar
          </button>
          <button class="btn-modal-save" @click="guardarCambiosModal">
            <i class="fas fa-save"></i>
            Guardar Cambios
          </button>
        </div>
      </div>
    </div>

    <!-- Mensaje de validación -->
    <div v-if="mensajeError" class="error-message">
      <i class="fas fa-exclamation-circle"></i>
      {{ mensajeError }}
    </div>

    <!-- Mensaje de éxito -->
    <div v-if="mensajeExito" class="success-message">
      <i class="fas fa-check-circle"></i>
      {{ mensajeExito }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'AgregarDetalleView',
  data() {
    return {
      mensajeError: '',
      mensajeExito: '',
      clienteActual: null,
      mostrarModal: false,
      cargandoAgregar: false,
      
      // Nuevas variables para modo manual
      modoManualOrigen: false,
      modoManualRuta: false,
      
      formData: {
        origen: '',
        destinoRuta: '',
        cantidad: 1,
        precioUnitario: '',
        descripcion: '',
        embarcacion: '',
        puertoEmbarque: ''
      },
      
      modalData: {
        origen: '',
        destinoRuta: '',
        embarcacion: '',
        puertoEmbarque: ''
      }
    }
  },
  
  computed: {
    precioTotal() {
      const precioBase = parseFloat(this.formData.precioUnitario) || 0
      return precioBase * this.formData.cantidad
    }
  },
  
  mounted() {
    this.cargarDatosCliente()
  },
  
  methods: {
    cargarDatosCliente() {
      const datosCliente = sessionStorage.getItem('datosCliente')
      if (datosCliente) {
        try {
          this.clienteActual = JSON.parse(datosCliente)
          console.log('✅ Datos del cliente cargados:', this.clienteActual)
        } catch (error) {
          console.error('❌ Error al cargar datos del cliente:', error)
          this.clienteActual = null
        }
      } else {
        console.log('⚠️ No se encontraron datos del cliente en sessionStorage')
      }
    },

    // Métodos del Modal MEJORADOS
    abrirModal() {
      // Cargar datos actuales en el modal
      this.modalData.origen = this.formData.origen
      this.modalData.destinoRuta = this.formData.destinoRuta
      this.modalData.embarcacion = this.formData.embarcacion
      this.modalData.puertoEmbarque = this.formData.puertoEmbarque
      
      this.mostrarModal = true
    },

    cerrarModal() {
      this.mostrarModal = false
      // Limpiar datos del modal
      this.modalData = {
        origen: '',
        destinoRuta: '',
        embarcacion: '',
        puertoEmbarque: ''
      }
    },

    intercambiarRutasModal() {
      if (this.modalData.origen && this.modalData.destinoRuta) {
        const temp = this.modalData.origen
        this.modalData.origen = this.modalData.destinoRuta
        this.modalData.destinoRuta = temp
      }
    },

    guardarCambiosModal() {
      // Validar solo los campos obligatorios de ruta
      if (!this.modalData.origen || !this.modalData.destinoRuta) {
        this.mensajeError = 'Por favor complete el detalle y la ruta'
        setTimeout(() => { this.mensajeError = '' }, 3000)
        return
      }
      
      // Detectar si son valores personalizados (no están en las opciones predefinidas)
      const opcionesOrigen = ['PUCALLPA', 'YURIMAGUAS', 'FRONTERA', 'OTROS']
      const opcionesRuta = ['PUCALLPA', 'REQUENA', 'YURIMAGUAS', 'SAN LORENZO', 'SANTA ROSA', 'TROMPETERO', 'INTUTO']
      
      this.modoManualOrigen = !opcionesOrigen.includes(this.modalData.origen.toUpperCase())
      this.modoManualRuta = !opcionesRuta.includes(this.modalData.destinoRuta.toUpperCase())
      
      // Actualizar TODOS los campos del formulario principal
      this.formData.origen = this.modalData.origen
      this.formData.destinoRuta = this.modalData.destinoRuta
      this.formData.embarcacion = this.modalData.embarcacion || '' // Opcional
      this.formData.puertoEmbarque = this.modalData.puertoEmbarque || '' // Opcional
      
      // Actualizar descripción automáticamente
      this.actualizarDescripcion()
      
      // Guardar nuevos valores en sessionStorage para que se mantengan en listas
      if (this.modalData.embarcacion && this.modalData.embarcacion.trim() !== '') {
        sessionStorage.setItem('nuevaEmbarcacionAgregada', this.modalData.embarcacion)
      }
      
      if (this.modalData.puertoEmbarque && this.modalData.puertoEmbarque.trim() !== '') {
        sessionStorage.setItem('nuevoPuertoAgregado', this.modalData.puertoEmbarque)
      }
      
      // Mostrar mensaje de éxito
      let mensaje = 'Datos actualizados correctamente'
      if (this.modoManualOrigen || this.modoManualRuta) {
        mensaje += '. Valores personalizados detectados - se muestran como texto'
      }
      if (this.modalData.embarcacion || this.modalData.puertoEmbarque) {
        mensaje += '. Detalles del viaje guardados'
      }
      
      this.mensajeExito = mensaje
      setTimeout(() => {
        this.mensajeExito = ''
      }, 3000)
      
      // Cerrar modal
      this.cerrarModal()
    },

    // Nuevos métodos para volver a selects
    volverASelectOrigen() {
      this.modoManualOrigen = false
      this.formData.origen = ''
      this.actualizarDescripcion()
    },

    volverASelectRuta() {
      this.modoManualRuta = false
      this.formData.destinoRuta = ''
      this.actualizarDescripcion()
    },

    intercambiarRutas() {
      if (this.formData.origen && this.formData.destinoRuta) {
        const origenTemp = this.formData.origen
        this.formData.origen = this.formData.destinoRuta
        this.formData.destinoRuta = origenTemp
        this.actualizarDescripcion()
      }
    },

    actualizarDescripcion() {
      if (this.formData.origen && this.formData.destinoRuta) {
        this.formData.descripcion = `${this.formData.origen} - ${this.formData.destinoRuta}`
        this.calcularYEnviarDatos()
      }
    },

    calcularYEnviarDatos() {
      if (this.formData.descripcion && this.formData.precioUnitario > 0) {
        const datosActualizados = {
          cantidad: parseInt(this.formData.cantidad),
          descripcion: this.formData.descripcion,
          precio_unitario: parseFloat(this.formData.precioUnitario),
          subtotal: this.precioTotal,
          destino: this.formData.origen || '',
          ruta: this.formData.descripcion,
          embarcacion: this.formData.embarcacion || '',
          puerto_embarque: this.formData.puertoEmbarque || ''
        }
        
        console.log('🚀 Enviando datos en tiempo real:', datosActualizados)
        
        // Guardar para DetallesPasajeView
        sessionStorage.setItem('datosPasajeActuales', JSON.stringify(datosActualizados))
        sessionStorage.setItem('datosPasajeNavegacion', JSON.stringify(datosActualizados))
        
        if (this.$root) {
          this.$root.$emit('datos-pasaje-actualizados', datosActualizados)
        }
      }
    },
    
    // VALIDACIÓN SIMPLIFICADA - Solo campos esenciales
    validarFormulario() {
      if (!this.formData.origen) {
        this.mensajeError = 'Por favor seleccione un destino'
        return false
      }
      
      if (!this.formData.destinoRuta) {
        this.mensajeError = 'Por favor seleccione una ruta'
        return false
      }
      
      if (!this.formData.precioUnitario || parseFloat(this.formData.precioUnitario) <= 0) {
        this.mensajeError = 'Por favor ingrese un precio válido'
        return false
      }
      
      this.mensajeError = ''
      return true
    },
    
    async agregarPasaje() {
      if (!this.validarFormulario()) {
        return
      }

      this.cargandoAgregar = true
      
      try {
        // Crear el objeto de datos para agregar
        const datosPasaje = {
          id: Date.now(), // ID único temporal
          destino: this.determinarDestinoCategoria(this.formData.destinoRuta),
          ruta: this.formData.descripcion,
          descripcion: this.formData.descripcion,
          cantidad: parseInt(this.formData.cantidad),
          precio_unitario: parseFloat(this.formData.precioUnitario),
          subtotal: this.precioTotal,
          embarcacion: this.formData.embarcacion || '', // Campo opcional
          puerto_embarque: this.formData.puertoEmbarque || '', // Campo opcional
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString()
        }
        
        console.log('📤 Agregando pasaje a la tabla detalles_pasaje:', datosPasaje)
        
        // 1. Obtener datos existentes de la tabla detalles_pasaje
        let datosExistentes = []
        const datosGuardados = sessionStorage.getItem('rociotravel.detalles_pasaje')
        if (datosGuardados) {
          try {
            datosExistentes = JSON.parse(datosGuardados)
            if (!Array.isArray(datosExistentes)) {
              datosExistentes = []
            }
          } catch (error) {
            console.error('Error al parsear datos existentes:', error)
            datosExistentes = []
          }
        }
        
        // 2. Agregar el nuevo pasaje al array
        datosExistentes.push(datosPasaje)
        
        // 3. Guardar de vuelta en sessionStorage
        sessionStorage.setItem('rociotravel.detalles_pasaje', JSON.stringify(datosExistentes))
        
        // 4. También guardar en el formato que usa tu aplicación
        sessionStorage.setItem('datosTablaDetalles', JSON.stringify(datosExistentes))
        sessionStorage.setItem('datosPasajeNavegacion', JSON.stringify(datosPasaje))
        
        // 5. Emitir evento para notificar a otros componentes
        if (this.$root) {
          this.$root.$emit('pasaje-agregado', datosPasaje)
          this.$root.$emit('tabla-actualizada', datosExistentes)
        }
        
        // 6. Mostrar mensaje de éxito
        this.mensajeExito = '¡Pasaje agregado correctamente! Redirigiendo...'
        
        // 7. Limpiar solo el precio (mantener la configuración del modal)
        this.formData.precioUnitario = ''
        this.mensajeError = ''
        
        // 8. Redirigir a la vista de detalles del pasaje después de un breve retraso
        setTimeout(() => {
          this.irADetallesDelPasaje(datosPasaje)
        }, 1500)
        
      } catch (error) {
        console.error('❌ Error al agregar pasaje:', error)
        this.mensajeError = 'Error al agregar el pasaje. Intenta nuevamente.'
        setTimeout(() => {
          this.mensajeError = ''
        }, 3000)
      } finally {
        this.cargandoAgregar = false
      }
    },
    
    determinarDestinoCategoria(destino) {
      const destinosPrincipales = ['PUCALLPA', 'YURIMAGUAS']
      const frontera = ['SANTA ROSA']
      
      if (destinosPrincipales.includes(destino)) {
        return destino
      } else if (frontera.includes(destino)) {
        return 'FRONTERA'
      } else {
        return 'OTROS'
      }
    },
    
    irADetallesDelPasaje(datos) {
      // Opción 1: Si usas router con nombre de ruta
      if (this.$router) {
        try {
          this.$router.push({
            name: 'detalles-pasaje', // Ajusta el nombre según tu ruta
            query: {
              id: datos.id,
              cantidad: datos.cantidad.toString(),
              descripcion: datos.descripcion,
              precio_unitario: datos.precio_unitario.toString(),
              subtotal: datos.subtotal.toString(),
              destino: datos.destino,
              ruta: datos.ruta,
              embarcacion: datos.embarcacion,
              puerto_embarque: datos.puerto_embarque,
              agregado: 'true' // Flag para indicar que fue recién agregado
            }
          })
        } catch (error) {
          console.error('Error al navegar con router:', error)
          // Opción 2: Si falla el router, usar navegación manual
          this.navegacionManual()
        }
      } else {
        // Opción 3: Navegación manual si no hay router
        this.navegacionManual()
      }
    },
    
    navegacionManual() {
      // Si no funciona el router, redirigir manualmente
      // Ajusta la URL según tu aplicación
      const urlDestino = '/detalles-pasaje' // o la URL que corresponda
      window.location.href = urlDestino
    },
    
    limpiarFormulario() {
      this.formData = {
        origen: '',
        destinoRuta: '',
        cantidad: 1,
        precioUnitario: '',
        descripcion: '',
        embarcacion: '',
        puertoEmbarque: ''
      }
      this.mensajeError = ''
      
      // No limpiar los datos de la tabla, solo los temporales del formulario
      sessionStorage.removeItem('datosPasajeActuales')
    }
  }
}
</script>

<style scoped>
.travel-search-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
  text-align: center;
  margin-bottom: 30px;
  color: white;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.header h2 {
  font-size: 32px;
  font-weight: 300;
  margin: 0;
  flex: 1;
}

/* Botón Modal */
.modal-button {
  padding: 12px 20px;
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  white-space: nowrap;
  box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.modal-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
  background: linear-gradient(135deg, #218838, #1e7e34);
}

.cliente-info {
  display: flex;
  justify-content: center;
  margin-bottom: 15px;
}

.cliente-card {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 15px;
  padding: 15px 25px;
  display: flex;
  align-items: center;
  gap: 15px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}

.cliente-card i {
  font-size: 24px;
  color: #ffffff;
}

.cliente-datos p {
  margin: 3px 0;
  color: white;
  font-size: 14px;
  font-weight: 500;
}

.cliente-datos strong {
  font-weight: 600;
}

.search-form {
  background: white;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.route-section {
  display: flex;
  align-items: flex-end;
  gap: 15px;
  margin-bottom: 25px;
}

.input-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.input-group label {
  font-weight: 600;
  color: #555;
  font-size: 14px;
}

.location-input {
  padding: 15px 20px;
  border: 2px solid #e1e5e9;
  border-radius: 12px;
  font-size: 16px;
  background: #f8f9fa;
  transition: all 0.3s ease;
  color: #333;
}

.location-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
  background: white;
}

/* Campos de solo lectura y manuales */
.manual-input {
  background: #fff3cd !important;
  border-color: #ffc107 !important;
  color: #856404 !important;
  font-weight: 600;
}

.manual-input::placeholder {
  color: #856404;
  opacity: 0.7;
}

.reset-button {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 30px;
  height: 30px;
  background: #ffc107;
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: all 0.3s ease;
}

.reset-button:hover {
  background: #e0a800;
  transform: translateY(-50%) scale(1.1);
}

.origen-group, .destino-group {
  position: relative;
}

.swap-button {
  width: 50px;
  height: 50px;
  background: #667eea;
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  transition: all 0.3s ease;
  margin-bottom: 5px;
}

.swap-button:hover {
  background: #764ba2;
  transform: rotate(180deg);
}

.cantidad-section {
  margin-bottom: 25px;
}

.cantidad-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: center;
}

.cantidad-group label {
  font-weight: 600;
  color: #555;
  font-size: 14px;
}

.cantidad-display {
  padding: 15px 30px;
  border: 2px solid #e1e5e9;
  border-radius: 12px;
  background: #f8f9fa;
}

.cantidad-number {
  font-weight: 700;
  font-size: 24px;
  color: #333;
}

.price-section {
  display: flex;
  gap: 20px;
  align-items: flex-end;
  margin-bottom: 30px;
}

.price-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.price-group label {
  font-weight: 600;
  color: #555;
  font-size: 14px;
}

.price-input {
  padding: 15px 20px;
  border: 2px solid #e1e5e9;
  border-radius: 12px;
  font-size: 16px;
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.price-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
  background: white;
}

.price-total {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 5px;
}

.total-label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.total-amount {
  font-size: 24px;
  font-weight: 700;
  color: #28a745;
  background: #f8fff9;
  padding: 10px 15px;
  border-radius: 8px;
  border: 2px solid #d4edda;
}

.add-button {
  width: 100%;
  padding: 18px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 15px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.add-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102,126,234,0.3);
}

.add-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* Estilos del Modal MEJORADOS */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(5px);
}

.modal-container {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  padding: 20px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 20px 20px 0 0;
}

.modal-header h3 {
  margin: 0;
  font-size: 22px;
  font-weight: 600;
}

.modal-close {
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: rotate(90deg);
}

.modal-body {
  padding: 30px;
}

.modal-section {
  margin-bottom: 30px;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
  color: #333;
  font-size: 18px;
  font-weight: 600;
  padding-bottom: 10px;
  border-bottom: 2px solid #e9ecef;
}

.section-description {
  margin-bottom: 20px;
  padding: 12px 16px;
  background: #e8f4fd;
  border-left: 4px solid #2196f3;
  border-radius: 6px;
  font-size: 14px;
  color: #1565c0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-description i {
  color: #2196f3;
  font-size: 16px;
}

.modal-route-section {
  display: flex;
  gap: 15px;
  align-items: flex-end;
  margin-bottom: 20px;
}

.modal-details-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.modal-swap-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-swap-button {
  width: 45px;
  height: 45px;
  background: #28a745;
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  transition: all 0.3s ease;
}

.modal-swap-button:hover {
  background: #218838;
  transform: rotate(180deg);
}

.modal-input-group {
  flex: 1;
  margin-bottom: 20px;
}

.modal-input-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
  font-size: 14px;
}

.modal-input {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 16px;
  background: #f8f9fa;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.modal-input:focus {
  outline: none;
  border-color: #28a745;
  box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
  background: white;
}

.modal-footer {
  padding: 20px 30px;
  border-top: 1px solid #e9ecef;
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  background: #f8f9fa;
  border-radius: 0 0 20px 20px;
}

.btn-modal-cancel, .btn-modal-save {
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-modal-cancel {
  background: #6c757d;
  color: white;
}

.btn-modal-cancel:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

.btn-modal-save {
  background: #28a745;
  color: white;
}

.btn-modal-save:hover {
  background: #218838;
  transform: translateY(-2px);
}

.debug-info {
  margin-top: 20px;
  padding: 15px;
  background: #e3f2fd;
  border-radius: 8px;
  border-left: 4px solid #2196f3;
}

.debug-info h4 {
  margin: 0 0 10px 0;
  color: #1976d2;
}

.debug-info p {
  margin: 5px 0;
  font-size: 14px;
  color: #424242;
}

.optional-info {
  background: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: 6px;
  padding: 10px;
  margin-top: 10px;
  font-size: 13px;
  color: #856404;
  display: flex;
  align-items: center;
  gap: 8px;
}

.optional-info i {
  color: #ffc107;
  font-size: 14px;
}

.error-message, .success-message {
  margin-top: 20px;
  padding: 15px 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
}

.error-message {
  background: #fff5f5;
  color: #c53030;
  border: 2px solid #fed7d7;
}

.success-message {
  background: #f0fff4;
  color: #38a169;
  border: 2px solid #c6f6d5;
}

/* Animación del spinner */
.fa-spin {
  animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(359deg);
  }
}

@media (max-width: 768px) {
  .travel-search-container {
    padding: 15px;
  }
  
  .header-top {
    flex-direction: column;
    gap: 15px;
    align-items: center;
  }
  
  .header h2 {
    font-size: 24px;
    text-align: center;
  }
  
  .modal-button {
    width: 100%;
    justify-content: center;
  }
  
  .cliente-card {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .route-section {
    flex-direction: column;
    gap: 20px;
  }
  
  .swap-button {
    align-self: center;
    transform: rotate(90deg);
  }
  
  .price-section {
    flex-direction: column;
    gap: 15px;
  }
  
  .search-form {
    padding: 20px;
  }

  .modal-container {
    width: 95%;
    margin: 20px;
  }

  .modal-route-section {
    flex-direction: column;
    gap: 15px;
  }

  .modal-details-section {
    grid-template-columns: 1fr;
    gap: 15px;
  }

  .modal-swap-container {
    order: 2;
  }
  
  .modal-footer {
    flex-direction: column-reverse;
  }
  
  .btn-modal-cancel,
  .btn-modal-save {
    width: 100%;
    justify-content: center;
  }
}
</style>