<template>
  <div class="detalles-pasaje-container">
    <!-- Header -->
    <div class="header-section">
      <h2>Detalles del pasaje</h2>
      <div class="header-actions">
        <button @click="generarImagenBoleta" class="btn-imagen" :disabled="!puedeGenerarImagen">
          <i class="fas fa-image"></i>
          Generar Imagen
        </button>
        <button @click="imprimirTicketVenta" class="btn-ticket" :disabled="!puedeGenerarImagen">
          <i class="fas fa-print"></i>
          Imprimir ticket de venta
        </button>
        <button @click="limpiarFormulario" class="btn-secondary">
          <i class="fas fa-broom"></i>
          Limpiar
        </button>
      </div>
    </div>

    <!-- Tabla de Detalles del Pasaje -->
    <div class="detalles-table-section">
      <div class="table-wrapper">
        <table class="detalles-table">
          <thead>
            <tr>
              <th class="text-center">Cantidad</th>
              <th class="text-center">Descripción</th>
              <th class="text-center">Precio Unitario</th>
              <th class="text-center">Sub Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in detallesPasaje" :key="index">
              <td class="text-center">{{ item.cantidad }}</td>
              <td class="text-center">{{ item.descripcion }}</td>
              <td class="text-center">S/ {{ parseFloat(item.precio_unitario || 0).toFixed(2) }}</td>
              <td class="text-center subtotal">S/ {{ parseFloat(item.subtotal || 0).toFixed(2) }}</td>
            </tr>
            <tr v-if="detallesPasaje.length === 0">
              <td colspan="4" class="no-items">
                <i class="fas fa-plus-circle"></i>
                <p>No hay elementos agregados. Los datos se cargarán automáticamente desde el formulario anterior.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Total -->
      <div class="total-section">
        <div class="total-row">
          <span class="total-label">Total: S/</span>
          <span class="total-amount">{{ totalGeneral.toFixed(2) }}</span>
        </div>
      </div>
    </div>

    <!-- Formulario de Detalles de Viaje -->
    <div class="form-section">
      <div class="form-grid">
        <!-- Columna Izquierda -->
        <div class="form-column">
          <div class="form-group">
            <label for="embarcacion">Embarcación:</label>
            <div class="input-with-edit">
              <select 
                v-if="!modoEditarEmbarcacion"
                id="embarcacion" 
                v-model="datosViaje.embarcacion" 
                class="form-select" 
                @change="validarImagen"
              >
                <option value="">Seleccionar embarcación</option>
                <option v-for="embarcacion in embarcaciones" :key="embarcacion" :value="embarcacion">
                  {{ embarcacion }}
                </option>
              </select>
              <input 
                v-else
                type="text" 
                v-model="embarcacionEditando"
                class="form-input edit-input"
                placeholder="Editar embarcación"
                @keyup.enter="guardarEdicionEmbarcacion"
              />
              <button 
                v-if="datosViaje.embarcacion && !modoEditarEmbarcacion" 
                @click="activarEdicionEmbarcacion"
                class="btn-edit-inline"
                title="Editar embarcación"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button 
                v-if="modoEditarEmbarcacion" 
                @click="guardarEdicionEmbarcacion"
                class="btn-save-inline"
                title="Guardar cambios"
              >
                <i class="fas fa-check"></i>
              </button>
              <button 
                v-if="modoEditarEmbarcacion" 
                @click="cancelarEdicionEmbarcacion"
                class="btn-cancel-inline"
                title="Cancelar"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="puertoEmbarque">Puerto de embarque:</label>
            <div class="input-with-edit">
              <select 
                v-if="!modoEditarPuerto"
                id="puertoEmbarque" 
                v-model="datosViaje.puertoEmbarque" 
                class="form-select" 
                @change="validarImagen"
              >
                <option value="">Seleccionar puerto</option>
                <option v-for="puerto in puertosEmbarque" :key="puerto" :value="puerto">
                  {{ puerto }}
                </option>
              </select>
              <input 
                v-else
                type="text" 
                v-model="puertoEditando"
                class="form-input edit-input"
                placeholder="Editar puerto"
                @keyup.enter="guardarEdicionPuerto"
              />
              <button 
                v-if="datosViaje.puertoEmbarque && !modoEditarPuerto" 
                @click="activarEdicionPuerto"
                class="btn-edit-inline"
                title="Editar puerto"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button 
                v-if="modoEditarPuerto" 
                @click="guardarEdicionPuerto"
                class="btn-save-inline"
                title="Guardar cambios"
              >
                <i class="fas fa-check"></i>
              </button>
              <button 
                v-if="modoEditarPuerto" 
                @click="cancelarEdicionPuerto"
                class="btn-cancel-inline"
                title="Cancelar"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="horaEmbarque">Hora de embarque:</label>
            <select id="horaEmbarque" v-model="datosViaje.horaEmbarque" class="form-select" @change="validarImagen">
              <option value="">Seleccionar hora</option>
              <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
                {{ hora }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="horaSalida">Hora de salida:</label>
            <select id="horaSalida" v-model="datosViaje.horaSalida" class="form-select" @change="validarImagen">
              <option value="">Seleccionar hora</option>
              <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
                {{ hora }}
              </option>
            </select>
          </div>

          <!-- Medio de Pago -->
          <div class="form-group">
            <label>Medio de pago:</label>
            <div class="payment-methods">
              <div class="radio-group" @click="seleccionarMedioPago('efectivo')">
                <input 
                  type="radio" 
                  id="efectivo" 
                  value="efectivo" 
                  v-model="datosViaje.medioPago"
                  @change="validarImagen"
                >
                <label for="efectivo">Efectivo</label>
              </div>
              <div class="radio-group" @click="seleccionarMedioPago('yape')">
                <input 
                  type="radio" 
                  id="yape" 
                  value="yape" 
                  v-model="datosViaje.medioPago"
                  @change="validarImagen"
                >
                <label for="yape">Yape</label>
              </div>
              <div class="radio-group" @click="seleccionarMedioPago('plin')">
                <input 
                  type="radio" 
                  id="plin" 
                  value="plin" 
                  v-model="datosViaje.medioPago"
                  @change="validarImagen"
                >
                <label for="plin">Plin</label>
              </div>
            </div>
            <div class="checkbox-group" @click="togglePagoMixto">
              <input 
                type="checkbox" 
                id="pagoMixto" 
                v-model="datosViaje.pagoMixto"
                @change="validarImagen"
              >
              <label for="pagoMixto">Pago Mixto</label>
            </div>

            <!-- Resumen del pago -->
            <div v-if="datosViaje.medioPago && !datosViaje.pagoMixto" class="payment-summary">
              <div class="payment-info">
                <i :class="getPaymentIcon(datosViaje.medioPago)"></i>
                <span>{{ getPaymentLabel(datosViaje.medioPago) }} - S/ {{ totalGeneral.toFixed(2) }}</span>
              </div>
            </div>

            <div v-if="datosViaje.pagoMixto" class="payment-summary">
              <div class="payment-info mixed">
                <i class="fas fa-coins"></i>
                <span>Pago Mixto - S/ {{ totalGeneral.toFixed(2) }}</span>
              </div>
              <div v-if="pagoMixtoDetalle.efectivo > 0" class="payment-breakdown">
                <span>Efectivo: S/ {{ pagoMixtoDetalle.efectivo.toFixed(2) }}</span>
              </div>
              <div v-if="pagoMixtoDetalle.yape > 0" class="payment-breakdown">
                <span>Yape: S/ {{ pagoMixtoDetalle.yape.toFixed(2) }}</span>
              </div>
              <div v-if="pagoMixtoDetalle.plin > 0" class="payment-breakdown">
                <span>Plin: S/ {{ pagoMixtoDetalle.plin.toFixed(2) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna Derecha -->
        <div class="form-column">
          <!-- Datos del Cliente Bloqueados -->
          <div class="cliente-locked-info">
            <div class="locked-header">
              <i class="fas fa-lock"></i>
              <span>Datos del Cliente (Cargados automáticamente)</span>
            </div>
          </div>

          <div class="form-group">
            <label for="nombreCliente">Nombre del Cliente:</label>
            <input 
              id="nombreCliente"
              type="text"
              v-model="datosViaje.nombreCliente"
              class="form-input form-input-locked"
              placeholder="Cargado automáticamente"
              readonly
              disabled
            >
            <div class="locked-indicator">
              <i class="fas fa-info-circle"></i>
              <span>Este campo se carga automáticamente del cliente seleccionado</span>
            </div>
          </div>

          <div class="form-group">
            <label for="documentoCliente">Documento de Identidad:</label>
            <input 
              id="documentoCliente"
              type="text"
              v-model="datosViaje.documentoCliente"
              class="form-input form-input-locked"
              placeholder="Cargado automáticamente"
              readonly
              disabled
            >
            <div class="locked-indicator">
              <i class="fas fa-info-circle"></i>
              <span>Este campo se carga automáticamente del cliente seleccionado</span>
            </div>
          </div>

          <div class="form-group">
            <label for="contactoCliente">Contacto:</label>
            <input 
              id="contactoCliente"
              type="text"
              v-model="datosViaje.contactoCliente"
              class="form-input form-input-locked"
              placeholder="Cargado automáticamente"
              readonly
              disabled
            >
            <div class="locked-indicator">
              <i class="fas fa-info-circle"></i>
              <span>Este campo se carga automáticamente del cliente seleccionado</span>
            </div>
          </div>

          <div class="form-group">
            <label for="nacionalidadCliente">Nacionalidad:</label>
            <input 
              id="nacionalidadCliente"
              type="text"
              v-model="datosViaje.nacionalidadCliente"
              class="form-input form-input-locked"
              placeholder="Cargado automáticamente"
              readonly
              disabled
            >
            <div class="locked-indicator">
              <i class="fas fa-info-circle"></i>
              <span>Este campo se carga automáticamente del cliente seleccionado</span>
            </div>
          </div>

          <div class="form-group">
            <label for="nota">Nota:</label>
            <textarea 
              id="nota"
              v-model="datosViaje.nota"
              class="form-textarea"
              rows="4"
              placeholder="Ingrese observaciones o notas adicionales sobre el viaje..."
              @input="validarImagen"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Botones de Acción -->
      <div class="form-actions">
        <button @click="guardarTodo" class="btn-save-all">
          <i class="fas fa-save"></i>
          Guardar Todo
        </button>
        <button @click="cancelar" class="btn-cancel-form">
          <i class="fas fa-arrow-left"></i>
          Cancelar
        </button>
      </div>
    </div>

    <!-- Modal Pago Efectivo -->
    <div v-if="mostrarModalEfectivo" class="modal-overlay" @click="cerrarModalEfectivo">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3><i class="fas fa-money-bill-wave"></i> Pago en Efectivo</h3>
          <button @click="cerrarModalEfectivo" class="btn-close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="pago-efectivo-content">
            <div class="total-pagar">
              <label>Total a pagar:</label>
              <div class="amount-display">S/ {{ totalGeneral.toFixed(2) }}</div>
            </div>
            <div class="cantidad-cliente">
              <label for="cantidadCliente">Cantidad que da el cliente:</label>
              <input 
                type="number" 
                id="cantidadCliente"
                v-model="cantidadCliente"
                @input="calcularVuelto"
                step="0.01"
                min="0"
                class="form-input"
                placeholder="0.00"
              >
            </div>
            <div class="vuelto-display">
              <label>Vuelto:</label>
              <div class="amount-display" :class="{ 'negative': vuelto < 0 }">
                S/ {{ vuelto.toFixed(2) }}
              </div>
              <div v-if="vuelto < 0" class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                La cantidad es insuficiente
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="confirmarPagoEfectivo" class="btn-confirm" :disabled="vuelto < 0">
            <i class="fas fa-check"></i>
            Confirmar Pago
          </button>
          <button @click="cerrarModalEfectivo" class="btn-cancel">
            <i class="fas fa-times"></i>
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Pago Mixto -->
    <div v-if="mostrarModalPagoMixto" class="modal-overlay" @click="cerrarModalPagoMixto">
      <div class="modal-content modal-mixto" @click.stop>
        <div class="modal-header">
          <h3><i class="fas fa-coins"></i> Configurar Pago Mixto</h3>
          <button @click="cerrarModalPagoMixto" class="btn-close-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="pago-mixto-content">
            <div class="total-mixto">
              <label>Total a pagar:</label>
              <div class="amount-display">S/ {{ totalGeneral.toFixed(2) }}</div>
            </div>
            <div class="payment-breakdown-form">
              <div class="payment-method-input">
                <label><i class="fas fa-money-bill-wave"></i> Efectivo:</label>
                <input 
                  type="number" 
                  v-model="pagoMixtoTemp.efectivo"
                  @input="calcularPagoMixto"
                  step="0.01"
                  min="0"
                  class="form-input"
                  placeholder="0.00"
                >
              </div>
              <div class="payment-method-input">
                <label><i class="fab fa-cc-visa"></i> Yape:</label>
                <input 
                  type="number" 
                  v-model="pagoMixtoTemp.yape"
                  @input="calcularPagoMixto"
                  step="0.01"
                  min="0"
                  class="form-input"
                  placeholder="0.00"
                >
              </div>
              <div class="payment-method-input">
                <label><i class="fas fa-mobile-alt"></i> Plin:</label>
                <input 
                  type="number" 
                  v-model="pagoMixtoTemp.plin"
                  @input="calcularPagoMixto"
                  step="0.01"
                  min="0"
                  class="form-input"
                  placeholder="0.00"
                >
              </div>
            </div>
            <div class="total-calculado">
              <div class="subtotal-line">
                <span>Subtotal pagado:</span>
                <span class="amount">S/ {{ totalPagoMixto.toFixed(2) }}</span>
              </div>
              <div class="diferencia-line" :class="{ 'error': diferenciaPago !== 0, 'success': diferenciaPago === 0 }">
                <span>Diferencia:</span>
                <span class="amount">S/ {{ Math.abs(diferenciaPago).toFixed(2) }}</span>
                <span v-if="diferenciaPago > 0" class="status-text">(Falta)</span>
                <span v-else-if="diferenciaPago < 0" class="status-text">(Sobra)</span>
                <span v-else class="status-text">(Exacto)</span>
              </div>
              <div v-if="diferenciaPago !== 0" class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                El total debe coincidir exactamente con S/ {{ totalGeneral.toFixed(2) }}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="confirmarPagoMixto" class="btn-confirm" :disabled="diferenciaPago !== 0">
            <i class="fas fa-check"></i>
            Confirmar Pago Mixto
          </button>
          <button @click="cerrarModalPagoMixto" class="btn-cancel">
            <i class="fas fa-times"></i>
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="cargando" class="loading-overlay">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>{{ mensajeCarga }}</p>
      </div>
    </div>

    <!-- Notificaciones -->
    <div v-if="mensaje" :class="['notification', tipoMensaje]">
      <i :class="iconoMensaje"></i>
      <span>{{ mensaje }}</span>
      <button @click="cerrarMensaje" class="btn-close-notification">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "DetallesPasajeView",
  data() {
    return {
      cargando: false,
      mensaje: '',
      tipoMensaje: 'success',
      mensajeCarga: 'Procesando...',
      detalleActualId: null,
      puedeGenerarImagen: false,
      
      // Modos de edición
      modoEditarEmbarcacion: false,
      modoEditarPuerto: false,
      embarcacionEditando: '',
      puertoEditando: '',
      embarcacionOriginal: '',
      puertoOriginal: '',
      
      mostrarModalEfectivo: false,
      mostrarModalPagoMixto: false,
      
      cantidadCliente: 0,
      vuelto: 0,
      
      pagoMixtoTemp: {
        efectivo: 0,
        yape: 0,
        plin: 0
      },
      pagoMixtoDetalle: {
        efectivo: 0,
        yape: 0,
        plin: 0
      },
      
      embarcaciones: [],
      puertosEmbarque: [],

      horasDisponibles: [
        '04:00 AM', '04:30 AM', '05:00 AM', '05:30 AM', '06:00 AM', '06:30 AM', 
        '07:00 AM', '07:30 AM', '08:00 AM', '08:30 AM', '09:00 AM', '09:30 AM', 
        '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM', 
        '01:00 PM', '01:30 PM', '02:00 PM', '02:30 PM', '03:00 PM', '03:30 PM', 
        '04:00 PM', '04:30 PM', '05:00 PM', '05:30 PM', '06:00 PM', '06:30 PM', 
        '07:00 PM', '07:30 PM', '08:00 PM'
      ],

      detallesPasaje: [],
      
      datosViaje: {
        embarcacion: '',
        puertoEmbarque: '',
        horaEmbarque: '',
        horaSalida: '',
        medioPago: '',
        pagoMixto: false,
        detallesPago: '',
        nota: '',
        nombreCliente: '',
        documentoCliente: '',
        contactoCliente: '',
        nacionalidadCliente: ''
      }
    }
  },

  computed: {
    totalGeneral() {
      return this.detallesPasaje.reduce((total, item) => {
        return total + parseFloat(item.subtotal || 0)
      }, 0)
    },

    iconoMensaje() {
      const iconos = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
      }
      return iconos[this.tipoMensaje] || 'fas fa-info-circle'
    },

    totalPagoMixto() {
      return parseFloat(this.pagoMixtoTemp.efectivo || 0) + 
             parseFloat(this.pagoMixtoTemp.yape || 0) + 
             parseFloat(this.pagoMixtoTemp.plin || 0)
    },

    diferenciaPago() {
      return this.totalGeneral - this.totalPagoMixto
    }
  },

  mounted() {
    this.inicializarListas()
    this.cargarDatosDesdeFormulario()
    this.cargarDatosCliente()
    this.cargarEmbarcacionesYPuertos()
    this.sincronizarDatosDesdeAgregarDetalle()
    
    // Escuchar evento de sincronización desde AgregarDetalleView
    window.addEventListener('elementosActualizados', this.sincronizarDatosDesdeAgregarDetalle)
  },

  beforeUnmount() {
    window.removeEventListener('elementosActualizados', this.sincronizarDatosDesdeAgregarDetalle)
  },

  methods: {
    // NUEVAS FUNCIONES DE SINCRONIZACIÓN Y EDICIÓN
    
    sincronizarDatosDesdeAgregarDetalle() {
      console.log('🔄 Sincronizando datos desde AgregarDetalleView...')
      
      // Cargar embarcaciones desde localStorage
      const embarcacionesGuardadas = localStorage.getItem('embarcacionesPersonalizadas')
      if (embarcacionesGuardadas) {
        try {
          const embarcacionesArray = JSON.parse(embarcacionesGuardadas)
          embarcacionesArray.forEach(embarcacion => {
            if (!this.embarcaciones.includes(embarcacion)) {
              this.embarcaciones.push(embarcacion)
            }
          })
        } catch (error) {
          console.error('Error cargando embarcaciones:', error)
        }
      }
      
      // Cargar puertos desde localStorage
      const puertosGuardados = localStorage.getItem('puertosPersonalizados')
      if (puertosGuardados) {
        try {
          const puertosArray = JSON.parse(puertosGuardados)
          puertosArray.forEach(puerto => {
            if (!this.puertosEmbarque.includes(puerto)) {
              this.puertosEmbarque.push(puerto)
            }
          })
        } catch (error) {
          console.error('Error cargando puertos:', error)
        }
      }
      
      // Cargar embarcación y puerto desde sessionStorage (datos del pasaje actual)
      const datosPasaje = sessionStorage.getItem('datosPasajeNavegacion')
      if (datosPasaje) {
        try {
          const datos = JSON.parse(datosPasaje)
          
          if (datos.embarcacion) {
            if (!this.embarcaciones.includes(datos.embarcacion)) {
              this.embarcaciones.push(datos.embarcacion)
            }
            this.datosViaje.embarcacion = datos.embarcacion
            this.mostrarMensaje(`Embarcación "${datos.embarcacion}" cargada automáticamente`, 'success')
          }
          
          if (datos.puerto_embarque) {
            if (!this.puertosEmbarque.includes(datos.puerto_embarque)) {
              this.puertosEmbarque.push(datos.puerto_embarque)
            }
            this.datosViaje.puertoEmbarque = datos.puerto_embarque
            this.mostrarMensaje(`Puerto "${datos.puerto_embarque}" cargado automáticamente`, 'success')
          }
          
          this.validarImagen()
        } catch (error) {
          console.error('Error sincronizando datos del pasaje:', error)
        }
      }
      
      console.log('✅ Sincronización completada:', {
        embarcaciones: this.embarcaciones.length,
        puertos: this.puertosEmbarque.length,
        embarcacionActual: this.datosViaje.embarcacion,
        puertoActual: this.datosViaje.puertoEmbarque
      })
    },
    
    activarEdicionEmbarcacion() {
      this.embarcacionOriginal = this.datosViaje.embarcacion
      this.embarcacionEditando = this.datosViaje.embarcacion
      this.modoEditarEmbarcacion = true
    },
    
    guardarEdicionEmbarcacion() {
      if (!this.embarcacionEditando || this.embarcacionEditando.trim() === '') {
        this.mostrarMensaje('El nombre de la embarcación no puede estar vacío', 'warning')
        return
      }
      
      const embarcacionNueva = this.embarcacionEditando.toUpperCase().trim()
      
      // Actualizar en el array si no existe
      if (!this.embarcaciones.includes(embarcacionNueva)) {
        this.embarcaciones.push(embarcacionNueva)
        
        // Actualizar localStorage
        const embarcacionesGuardadas = JSON.parse(localStorage.getItem('embarcacionesPersonalizadas') || '[]')
        if (!embarcacionesGuardadas.includes(embarcacionNueva)) {
          embarcacionesGuardadas.push(embarcacionNueva)
          localStorage.setItem('embarcacionesPersonalizadas', JSON.stringify(embarcacionesGuardadas))
        }
      }
      
      // Actualizar el valor seleccionado
      this.datosViaje.embarcacion = embarcacionNueva
      this.modoEditarEmbarcacion = false
      
      this.mostrarMensaje(`Embarcación actualizada a "${embarcacionNueva}"`, 'success')
      this.validarImagen()
      
      // Disparar evento para sincronizar con otros componentes
      window.dispatchEvent(new Event('elementosActualizados'))
    },
    
    cancelarEdicionEmbarcacion() {
      this.datosViaje.embarcacion = this.embarcacionOriginal
      this.modoEditarEmbarcacion = false
      this.embarcacionEditando = ''
    },
    
    activarEdicionPuerto() {
      this.puertoOriginal = this.datosViaje.puertoEmbarque
      this.puertoEditando = this.datosViaje.puertoEmbarque
      this.modoEditarPuerto = true
    },
    
    guardarEdicionPuerto() {
      if (!this.puertoEditando || this.puertoEditando.trim() === '') {
        this.mostrarMensaje('El nombre del puerto no puede estar vacío', 'warning')
        return
      }
      
      const puertoNuevo = this.puertoEditando.toUpperCase().trim()
      
      // Actualizar en el array si no existe
      if (!this.puertosEmbarque.includes(puertoNuevo)) {
        this.puertosEmbarque.push(puertoNuevo)
        
        // Actualizar localStorage
        const puertosGuardados = JSON.parse(localStorage.getItem('puertosPersonalizados') || '[]')
        if (!puertosGuardados.includes(puertoNuevo)) {
          puertosGuardados.push(puertoNuevo)
          localStorage.setItem('puertosPersonalizados', JSON.stringify(puertosGuardados))
        }
      }
      
      // Actualizar el valor seleccionado
      this.datosViaje.puertoEmbarque = puertoNuevo
      this.modoEditarPuerto = false
      
      this.mostrarMensaje(`Puerto actualizado a "${puertoNuevo}"`, 'success')
      this.validarImagen()
      
      // Disparar evento para sincronizar con otros componentes
      window.dispatchEvent(new Event('elementosActualizados'))
    },
    
    cancelarEdicionPuerto() {
      this.datosViaje.puertoEmbarque = this.puertoOriginal
      this.modoEditarPuerto = false
      this.puertoEditando = ''
    },

    // FUNCIONES EXISTENTES
    
    inicializarListas() {
      console.log('📋 Inicializando listas de embarcaciones y puertos...')
      
      // Listas base predefinidas
      const embarcacionesBase = [
        'KAORY', 'DON JULIO', 'ORIENTE 1', 'HAYDEE', 'MACHI MACHIN', 
        'MAGIN', 'TONY', 'CRISTIAN', 'ALEAXIA', 'ROMERO', 'NR', 
        'VALERIA 1', 'ZOE ALEXA', 'RAYZA', 'DOÑA LIDIA'
      ]
      
      const puertosBase = [
        'CALLE PEVAS #456', 'CALLE PEVAS #408', 'CALLE REQUENA #155', 
        'CALLE ABTAO #1350', 'PUERTO PRINCIPAL DE NAUTA', 
        'CALLE JR LIMA #712 (NAUTA)', 'PUERTO LA BOCA', 'PUERTO MAGIN', 
        'LA CAPITANIA', 'PUERTO DIEGUITO', 'PUERTO SAJUTA', 
        'LA CASONA DE SAJUTA', 'PUERTO RELOJ PÚBLICO', 'PUERTO GANZO AZUL', 
        'LA BALSA MUNICIPAL', 'FITZCARRALD #377', 'PUERTO MANAOS', 
        'CALLE NAUTA #342', 'PUERTO PRINCIPAL DE TROMPETEROS'
      ]
      
      // Inicializar solo si están vacías
      if (this.embarcaciones.length === 0) {
        this.embarcaciones = [...embarcacionesBase]
      }
      
      if (this.puertosEmbarque.length === 0) {
        this.puertosEmbarque = [...puertosBase]
      }
      
      console.log('✅ Listas inicializadas:', {
        embarcaciones: this.embarcaciones.length,
        puertos: this.puertosEmbarque.length
      })
    },

    async cargarEmbarcacionesYPuertos() {
      try {
        console.log('📡 Cargando embarcaciones y puertos desde API y localStorage...')
        
        // Intentar cargar desde API
        try {
          const responseEmbarcaciones = await fetch('/api/embarcaciones', {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
          })

          if (responseEmbarcaciones.ok) {
            const resultEmbarcaciones = await responseEmbarcaciones.json()
            if (resultEmbarcaciones.success && resultEmbarcaciones.data) {
              const embarcacionesAPI = resultEmbarcaciones.data.map(e => e.nombre)
              embarcacionesAPI.forEach(emb => {
                if (!this.embarcaciones.includes(emb)) {
                  this.embarcaciones.push(emb)
                }
              })
              console.log('✅ Embarcaciones cargadas desde API')
            }
          }
        } catch (error) {
          console.log('⚠️ API de embarcaciones no disponible')
        }

        try {
          const responsePuertos = await fetch('/api/puertos-embarque', {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
          })

          if (responsePuertos.ok) {
            const resultPuertos = await responsePuertos.json()
            if (resultPuertos.success && resultPuertos.data) {
              const puertosAPI = resultPuertos.data.map(p => p.nombre)
              puertosAPI.forEach(puerto => {
                if (!this.puertosEmbarque.includes(puerto)) {
                  this.puertosEmbarque.push(puerto)
                }
              })
              console.log('✅ Puertos cargados desde API')
            }
          }
        } catch (error) {
          console.log('⚠️ API de puertos no disponible')
        }

        // Cargar personalizados desde localStorage
        this.sincronizarDatosDesdeAgregarDetalle()
        
      } catch (error) {
        console.log('ℹ️ Error general, usando valores por defecto')
      }
    },

    seleccionarMedioPago(tipo) {
      this.datosViaje.medioPago = tipo
      this.datosViaje.pagoMixto = false
      this.limpiarPagoMixto()
      this.validarImagen()
      
      if (tipo === 'efectivo') {
        this.abrirModalEfectivo()
      } else {
        this.datosViaje.detallesPago = `${this.getPaymentLabel(tipo)}: S/ ${this.totalGeneral.toFixed(2)}`
      }
    },

    togglePagoMixto() {
      this.datosViaje.pagoMixto = !this.datosViaje.pagoMixto
      
      if (this.datosViaje.pagoMixto) {
        this.datosViaje.medioPago = ''
        this.abrirModalPagoMixto()
      } else {
        this.limpiarPagoMixto()
      }
      
      this.validarImagen()
    },

    abrirModalEfectivo() {
      this.cantidadCliente = this.totalGeneral
      this.calcularVuelto()
      this.mostrarModalEfectivo = true
    },

    cerrarModalEfectivo() {
      this.mostrarModalEfectivo = false
      this.cantidadCliente = 0
      this.vuelto = 0
    },

    calcularVuelto() {
      this.vuelto = parseFloat(this.cantidadCliente || 0) - this.totalGeneral
    },

    confirmarPagoEfectivo() {
      if (this.vuelto >= 0) {
        this.datosViaje.detallesPago = `Efectivo: S/ ${this.totalGeneral.toFixed(2)} - Cliente: S/ ${parseFloat(this.cantidadCliente).toFixed(2)} - Vuelto: S/ ${this.vuelto.toFixed(2)}`
        this.mostrarMensaje('Pago en efectivo configurado correctamente', 'success')
        this.cerrarModalEfectivo()
      }
    },

    abrirModalPagoMixto() {
      this.pagoMixtoTemp = { efectivo: 0, yape: 0, plin: 0 }
      this.mostrarModalPagoMixto = true
    },

    cerrarModalPagoMixto() {
      this.mostrarModalPagoMixto = false
      this.pagoMixtoTemp = { efectivo: 0, yape: 0, plin: 0 }
    },

    calcularPagoMixto() {
      // Calculado automáticamente con computed properties
    },

    confirmarPagoMixto() {
      if (this.diferenciaPago === 0) {
        this.pagoMixtoDetalle = { ...this.pagoMixtoTemp }
        
        let detalles = []
        if (this.pagoMixtoDetalle.efectivo > 0) {
          detalles.push(`Efectivo: S/ ${this.pagoMixtoDetalle.efectivo.toFixed(2)}`)
        }
        if (this.pagoMixtoDetalle.yape > 0) {
          detalles.push(`Yape: S/ ${this.pagoMixtoDetalle.yape.toFixed(2)}`)
        }
        if (this.pagoMixtoDetalle.plin > 0) {
          detalles.push(`Plin: S/ ${this.pagoMixtoDetalle.plin.toFixed(2)}`)
        }
        
        this.datosViaje.detallesPago = detalles.join(' + ')
        this.datosViaje.pagoMixto = true
        this.datosViaje.medioPago = 'mixto'
        
        this.mostrarMensaje('Pago mixto configurado correctamente', 'success')
        this.cerrarModalPagoMixto()
        this.validarImagen()
      }
    },

    limpiarPagoMixto() {
      this.pagoMixtoDetalle = { efectivo: 0, yape: 0, plin: 0 }
      if (!this.datosViaje.medioPago) {
        this.datosViaje.detallesPago = ''
      }
    },

    getPaymentIcon(tipo) {
      const iconos = {
        efectivo: 'fas fa-money-bill-wave',
        yape: 'fab fa-cc-visa',
        plin: 'fas fa-mobile-alt'
      }
      return iconos[tipo] || 'fas fa-credit-card'
    },

    getPaymentLabel(tipo) {
      const labels = {
        efectivo: 'Efectivo',
        yape: 'Yape',
        plin: 'Plin'
      }
      return labels[tipo] || tipo
    },

    cargarDatosCliente() {
      console.log('👤 Cargando datos del cliente...')
      
      const datosCliente = sessionStorage.getItem('datosCliente')
      if (datosCliente) {
        try {
          const cliente = JSON.parse(datosCliente)
          this.datosViaje.nombreCliente = cliente.nombre || ''
          this.datosViaje.documentoCliente = cliente.documento || ''
          this.datosViaje.contactoCliente = cliente.contacto || ''
          this.datosViaje.nacionalidadCliente = cliente.nacionalidad || 'PERUANA'
          
          this.mostrarMensaje('Datos del cliente cargados automáticamente', 'info')
          this.validarImagen()
        } catch (error) {
          this.mostrarMensaje('Error al cargar los datos del cliente', 'warning')
        }
      }
    },

    validarImagen() {
      const camposBasicos = this.detallesPasaje.length > 0 && 
                           this.datosViaje.embarcacion && 
                           this.datosViaje.puertoEmbarque &&
                           this.datosViaje.horaEmbarque &&
                           this.datosViaje.horaSalida

      let medioPagoValido = false
      
      if (this.datosViaje.pagoMixto) {
        medioPagoValido = this.datosViaje.detallesPago && 
                         this.datosViaje.detallesPago.trim() !== '' &&
                         (this.pagoMixtoDetalle.efectivo > 0 || 
                          this.pagoMixtoDetalle.yape > 0 || 
                          this.pagoMixtoDetalle.plin > 0)
      } else {
        medioPagoValido = this.datosViaje.medioPago && 
                         this.datosViaje.medioPago.trim() !== ''
      }

      this.puedeGenerarImagen = camposBasicos && medioPagoValido
    },

    async generarImagenBoleta() {
      if (!this.puedeGenerarImagen) {
        this.mostrarMensaje('Complete todos los campos obligatorios', 'warning')
        return
      }

      try {
        this.cargando = true
        this.mensajeCarga = 'Generando imagen del pasaje...'
        
        const datosCompletos = this.prepararDatosParaImagen()

        const response = await fetch('/api/pasajes/generar-imagen-tiempo-real', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'text/html,application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify(datosCompletos)
        })

        if (!response.ok) throw new Error(`Error ${response.status}`)

        const contentType = response.headers.get('content-type') || ''

        if (contentType.includes('text/html')) {
          const htmlContent = await response.text()
          this.procesarHTMLBoleta(htmlContent)
        } else if (contentType.includes('application/json')) {
          const result = await response.json()
          if (result.success && result.imagen_base64) {
            this.descargarImagenDesdeBase64(result.imagen_base64)
          }
        }

      } catch (error) {
        this.mostrarMensaje(`Error al generar la imagen: ${error.message}`, 'error')
      } finally {
        this.cargando = false
      }
    },

    procesarHTMLBoleta(htmlContent) {
      const newWindow = window.open('', '_blank', 'width=800,height=1000,scrollbars=yes,resizable=yes')
      
      if (newWindow) {
        newWindow.document.write(htmlContent)
        newWindow.document.close()
        newWindow.focus()
        this.mostrarMensaje('Boleta generada correctamente', 'success')
      } else {
        throw new Error('No se pudo abrir ventana')
      }
    },

    descargarImagenDesdeBase64(base64Data) {
      const base64Limpio = base64Data.replace(/^data:image\/(png|jpeg|jpg);base64,/, '')
      const byteCharacters = atob(base64Limpio)
      const byteNumbers = new Array(byteCharacters.length)
      
      for (let i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i)
      }
      
      const byteArray = new Uint8Array(byteNumbers)
      const blob = new Blob([byteArray], { type: 'image/jpeg' })
      this.descargarImagenJPG(blob)
    },

    descargarImagenJPG(blob) {
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      
      const fecha = new Date()
      const fechaStr = `${fecha.getFullYear()}${String(fecha.getMonth()+1).padStart(2, '0')}${String(fecha.getDate()).padStart(2, '0')}`
      const horaStr = `${String(fecha.getHours()).padStart(2, '0')}${String(fecha.getMinutes()).padStart(2, '0')}`
      const nombreCliente = this.datosViaje.nombreCliente 
        ? this.datosViaje.nombreCliente.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 20)
        : 'Cliente'
      
      link.download = `Boleta_${nombreCliente}_${fechaStr}_${horaStr}.jpg`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      
      setTimeout(() => window.URL.revokeObjectURL(url), 1000)
      this.mostrarMensaje('Imagen descargada exitosamente', 'success')
    },

    prepararDatosParaImagen() {
      const ahora = new Date()
      const primerDetalle = this.detallesPasaje[0]
      
      if (!primerDetalle) throw new Error('No hay detalles de pasaje')
      
      return {
        cliente: {
          nombre: this.datosViaje.nombreCliente || 'CLIENTE',
          documento: this.datosViaje.documentoCliente || '',
          contacto: this.datosViaje.contactoCliente || '',
          nacionalidad: this.datosViaje.nacionalidadCliente || 'PERUANA'
        },
        cantidad: primerDetalle.cantidad || 1,
        descripcion: primerDetalle.descripcion || 'Pasaje',
        precio_unitario: parseFloat(primerDetalle.precio_unitario || 0),
        subtotal: parseFloat(primerDetalle.subtotal || 0),
        total: parseFloat(this.totalGeneral),
        embarcacion: this.datosViaje.embarcacion || '',
        puerto_embarque: this.datosViaje.puertoEmbarque || '',
        hora_embarque: this.datosViaje.horaEmbarque || '',
        hora_salida: this.datosViaje.horaSalida || '',
        medio_pago: this.datosViaje.medioPago || 'efectivo',
        pago_mixto: this.datosViaje.pagoMixto || false,
        detalles_pago: this.datosViaje.detallesPago || '',
        nota: this.datosViaje.nota || '',
        destino: primerDetalle.destino || '',
        ruta: primerDetalle.ruta || primerDetalle.descripcion || '',
        fecha_emision: this.formatearFecha(ahora),
        hora_emision: this.formatearHora(ahora),
        operador: 'ROCÍO TRAVEL'
      }
    },

    async imprimirTicketVenta() {
      if (!this.puedeGenerarImagen) {
        this.mostrarMensaje('Complete todos los campos obligatorios', 'warning')
        return
      }

      try {
        this.cargando = true
        this.mensajeCarga = 'Generando ticket de venta...'
        
        const datosCompletos = this.prepararDatosParaTicket()

        const response = await fetch('/api/pasajes/generar-ticket-venta', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'text/html',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify(datosCompletos)
        })

        if (response.ok) {
          const htmlContent = await response.text()
          const ticketWindow = window.open('', '_blank', 'width=400,height=600')
          
          if (ticketWindow) {
            ticketWindow.document.write(htmlContent)
            ticketWindow.document.close()
            ticketWindow.focus()
            this.mostrarMensaje('Ticket generado exitosamente', 'success')
          }
        } else {
          throw new Error(`Error ${response.status}`)
        }

      } catch (error) {
        this.mostrarMensaje(`Error al generar el ticket: ${error.message}`, 'error')
      } finally {
        this.cargando = false
      }
    },

    prepararDatosParaTicket() {
      const ahora = new Date()
      const primerDetalle = this.detallesPasaje[0]
      
      if (!primerDetalle) throw new Error('No hay detalles de pasaje')
      
      let medioPagoTicket = 'efectivo'
      let esPagoMixto = false
      let detallesPago = ''
      
      if (this.datosViaje.pagoMixto) {
        medioPagoTicket = 'mixto'
        esPagoMixto = true
        detallesPago = this.datosViaje.detallesPago || ''
      } else if (this.datosViaje.medioPago) {
        medioPagoTicket = this.datosViaje.medioPago.toLowerCase()
        detallesPago = this.datosViaje.detallesPago || `${this.getPaymentLabel(this.datosViaje.medioPago)}: S/ ${this.totalGeneral.toFixed(2)}`
      }
      
      return {
        numero_ticket: this.generarNumeroTicket(),
        fecha_emision: this.formatearFecha(ahora),
        hora_emision: this.formatearHoraTicket(ahora),
        cliente: {
          nombre: this.datosViaje.nombreCliente || 'CLIENTE',
          documento: this.datosViaje.documentoCliente || 'N/A',
          contacto: this.datosViaje.contactoCliente || 'N/A',
          nacionalidad: this.datosViaje.nacionalidadCliente || 'PERUANA'
        },
        cantidad: parseInt(primerDetalle.cantidad) || 1,
        descripcion: primerDetalle.descripcion || 'Pasaje',
        precio_unitario: parseFloat(primerDetalle.precio_unitario || 0).toFixed(2),
        subtotal: parseFloat(primerDetalle.subtotal || 0).toFixed(2),
        total: this.totalGeneral.toFixed(2),
        fecha_viaje: this.formatearFecha(ahora),
        embarcacion: this.datosViaje.embarcacion || 'N/A',
        puerto_embarque: this.datosViaje.puertoEmbarque || 'N/A',
        hora_embarque: this.datosViaje.horaEmbarque || 'N/A',
        hora_salida: this.datosViaje.horaSalida || 'N/A',
        medio_pago: medioPagoTicket,
        pago_mixto: esPagoMixto,
        detalles_pago: detallesPago,
        nota: this.datosViaje.nota || '',
        destino: primerDetalle.destino || 'N/A',
        ruta: primerDetalle.ruta || 'N/A',
        operador: 'ROCÍO TRAVEL'
      }
    },

    formatearFecha(fecha) {
      const dia = String(fecha.getDate()).padStart(2, '0')
      const mes = String(fecha.getMonth() + 1).padStart(2, '0')
      const año = fecha.getFullYear()
      return `${dia}/${mes}/${año}`
    },

    formatearHora(fecha) {
      let horas = fecha.getHours()
      const minutos = String(fecha.getMinutes()).padStart(2, '0')
      const ampm = horas >= 12 ? 'p. m.' : 'a. m.'
      
      if (horas === 0) horas = 12
      else if (horas > 12) horas -= 12
      
      return `${String(horas).padStart(2, '0')}:${minutos} ${ampm}`
    },

    formatearHoraTicket(fecha) {
      const horas = String(fecha.getHours()).padStart(2, '0')
      const minutos = String(fecha.getMinutes()).padStart(2, '0')
      const segundos = String(fecha.getSeconds()).padStart(2, '0')
      return `${horas}:${minutos}:${segundos}`
    },

    generarNumeroTicket() {
      const ahora = new Date()
      const año = ahora.getFullYear().toString().slice(-2)
      const mes = String(ahora.getMonth() + 1).padStart(2, '0')
      const dia = String(ahora.getDate()).padStart(2, '0')
      const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0')
      return `NV-${año}${mes}${dia}${random}`
    },

    cargarDatosDesdeFormulario() {
      const queryParams = this.$route.query
      const datosSessionStorage = sessionStorage.getItem('datosPasajeNavegacion')
      let datosParsed = null
      
      if (datosSessionStorage) {
        try {
          datosParsed = JSON.parse(datosSessionStorage)
        } catch (error) {
          console.error('Error al parsear datos:', error)
        }
      }
      
      const datosPasaje = Object.keys(queryParams).length > 0 ? queryParams : datosParsed

      if (datosPasaje && (datosPasaje.cantidad || datosPasaje.descripcion)) {
        const detalle = {
          cantidad: parseInt(datosPasaje.cantidad) || 1,
          descripcion: datosPasaje.descripcion || datosPasaje.ruta || '',
          precio_unitario: parseFloat(datosPasaje.precio_unitario) || 0,
          subtotal: parseFloat(datosPasaje.subtotal) || 0,
          destino: datosPasaje.destino || '',
          ruta: datosPasaje.ruta || ''
        }

        this.detallesPasaje = [detalle]
        this.mostrarMensaje('Datos cargados correctamente', 'success')
        this.validarImagen()
      }
    },

    async guardarTodo() {
      if (!this.validarFormularioCompleto()) return

      this.cargando = true
      this.mensajeCarga = 'Guardando datos...'
      
      try {
        await this.enviarTodosLosDatos()
        
        // Guardar embarcación y puerto en localStorage si son nuevos
        if (this.datosViaje.embarcacion) {
          const embarcacionesGuardadas = JSON.parse(localStorage.getItem('embarcacionesPersonalizadas') || '[]')
          if (!embarcacionesGuardadas.includes(this.datosViaje.embarcacion)) {
            embarcacionesGuardadas.push(this.datosViaje.embarcacion)
            localStorage.setItem('embarcacionesPersonalizadas', JSON.stringify(embarcacionesGuardadas))
          }
        }
        
        if (this.datosViaje.puertoEmbarque) {
          const puertosGuardados = JSON.parse(localStorage.getItem('puertosPersonalizados') || '[]')
          if (!puertosGuardados.includes(this.datosViaje.puertoEmbarque)) {
            puertosGuardados.push(this.datosViaje.puertoEmbarque)
            localStorage.setItem('puertosPersonalizados', JSON.stringify(puertosGuardados))
          }
        }
        
        // Disparar evento de sincronización
        window.dispatchEvent(new Event('elementosActualizados'))
        
        this.mostrarMensaje('Todos los datos guardados correctamente', 'success')
      } catch (error) {
        this.mostrarMensaje('Error al guardar los datos', 'error')
      } finally {
        this.cargando = false
      }
    },

    validarFormularioCompleto() {
      if (this.detallesPasaje.length === 0) {
        this.mostrarMensaje('No hay detalles de pasaje', 'warning')
        return false
      }
      
      if (!this.datosViaje.embarcacion) {
        this.mostrarMensaje('Seleccione una embarcación', 'warning')
        return false
      }
      
      if (!this.datosViaje.puertoEmbarque) {
        this.mostrarMensaje('Seleccione un puerto', 'warning')
        return false
      }

      if (!this.datosViaje.horaEmbarque || !this.datosViaje.horaSalida) {
        this.mostrarMensaje('Complete las horas', 'warning')
        return false
      }

      if (this.datosViaje.pagoMixto) {
        if (!this.datosViaje.detallesPago || this.datosViaje.detallesPago.trim() === '') {
          this.mostrarMensaje('Configure el pago mixto', 'warning')
          return false
        }
      } else {
        if (!this.datosViaje.medioPago || this.datosViaje.medioPago.trim() === '') {
          this.mostrarMensaje('Seleccione un medio de pago', 'warning')
          return false
        }
      }
      
      return true
    },

    async enviarTodosLosDatos() {
      const primerDetalle = this.detallesPasaje[0]
      
      let medioPagoGuardar = 'efectivo'
      let esPagoMixtoGuardar = false
      let detallesPagoGuardar = ''
      
      if (this.datosViaje.pagoMixto) {
        medioPagoGuardar = 'mixto'
        esPagoMixtoGuardar = true
        detallesPagoGuardar = this.datosViaje.detallesPago || ''
      } else if (this.datosViaje.medioPago) {
        medioPagoGuardar = this.datosViaje.medioPago.toLowerCase()
        detallesPagoGuardar = this.datosViaje.detallesPago || `${this.getPaymentLabel(this.datosViaje.medioPago)}: S/ ${this.totalGeneral.toFixed(2)}`
      }
      
      const datosCompletos = {
        cantidad: primerDetalle.cantidad,
        descripcion: primerDetalle.descripcion,
        precio_unitario: primerDetalle.precio_unitario,
        subtotal: primerDetalle.subtotal,
        destino: primerDetalle.destino,
        ruta: primerDetalle.ruta,
        embarcacion: this.datosViaje.embarcacion,
        puerto_embarque: this.datosViaje.puertoEmbarque,
        hora_embarque: this.datosViaje.horaEmbarque,
        hora_salida: this.datosViaje.horaSalida,
        medio_pago: medioPagoGuardar,
        pago_mixto: esPagoMixtoGuardar,
        detalles_pago: detallesPagoGuardar,
        nota: this.datosViaje.nota,
        nombre_cliente: this.datosViaje.nombreCliente,
        documento_cliente: this.datosViaje.documentoCliente,
        contacto_cliente: this.datosViaje.contactoCliente,
        nacionalidad_cliente: this.datosViaje.nacionalidadCliente
      }

      const response = await fetch('/api/pasajes', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(datosCompletos)
      })

      const result = await response.json()

      if (!result.success) {
        throw new Error(result.message || 'Error al guardar los datos')
      }

      this.detalleActualId = result.data.id
      return result
    },

    limpiarFormulario() {
      this.detallesPasaje = []
      this.datosViaje = {
        embarcacion: '',
        puertoEmbarque: '',
        horaEmbarque: '',
        horaSalida: '',
        medioPago: '',
        pagoMixto: false,
        detallesPago: '',
        nota: '',
        nombreCliente: '',
        documentoCliente: '',
        contactoCliente: '',
        nacionalidadCliente: ''
      }
      this.detalleActualId = null
      this.puedeGenerarImagen = false
      this.limpiarPagoMixto()
      this.cantidadCliente = 0
      this.vuelto = 0
      
      sessionStorage.removeItem('datosPasajeNavegacion')
      sessionStorage.removeItem('datosPasajeActuales')
      sessionStorage.removeItem('datosCliente')
      
      this.mostrarMensaje('Formulario limpiado', 'info')
    },

    cancelar() {
      if (confirm('¿Está seguro de cancelar? Se perderán los datos no guardados.')) {
        this.$router.go(-1)
      }
    },

    mostrarMensaje(texto, tipo = 'info') {
      this.mensaje = texto
      this.tipoMensaje = tipo
      setTimeout(() => this.cerrarMensaje(), 5000)
    },

    cerrarMensaje() {
      this.mensaje = ''
    }
  },

  watch: {
    datosViaje: {
      handler() {
        this.validarImagen()
      },
      deep: true
    }
  }
}
</script>

<style scoped>
.detalles-pasaje-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header-section {
  background: white;
  border-radius: 15px;
  padding: 25px;
  margin-bottom: 25px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.header-section h2 {
  margin: 0 0 20px 0;
  color: #333;
  font-size: 28px;
  font-weight: 600;
}

.header-actions {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.btn-imagen, .btn-ticket, .btn-secondary {
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-imagen {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-ticket {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: white;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-imagen:hover, .btn-ticket:hover, .btn-secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.btn-imagen:disabled, .btn-ticket:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.detalles-table-section {
  background: white;
  border-radius: 15px;
  padding: 25px;
  margin-bottom: 25px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.table-wrapper {
  overflow-x: auto;
}

.detalles-table {
  width: 100%;
  border-collapse: collapse;
}

.detalles-table thead {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.detalles-table th {
  padding: 15px;
  text-align: center;
  font-weight: 600;
}

.detalles-table td {
  padding: 15px;
  border-bottom: 1px solid #e9ecef;
}

.detalles-table tbody tr:hover {
  background: #f8f9fa;
}

.subtotal {
  font-weight: 600;
  color: #28a745;
}

.no-items {
  text-align: center;
  padding: 40px;
  color: #6c757d;
}

.no-items i {
  font-size: 48px;
  color: #dee2e6;
  margin-bottom: 15px;
}

.total-section {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid #dee2e6;
}

.total-row {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 15px;
}

.total-label {
  font-size: 20px;
  font-weight: 600;
  color: #333;
}

.total-amount {
  font-size: 28px;
  font-weight: 700;
  color: #28a745;
}

.form-section {
  background: white;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  margin-bottom: 30px;
}

.form-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: #555;
  font-size: 14px;
}

.input-with-edit {
  display: flex;
  gap: 8px;
  align-items: center;
  position: relative;
}

.form-select, .form-input {
  flex: 1;
  padding: 12px 15px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 15px;
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.form-select:focus, .form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
  background: white;
}

.edit-input {
  background: #fff3cd !important;
  border-color: #ffc107 !important;
}

.btn-edit-inline, .btn-save-inline, .btn-cancel-inline {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  transition: all 0.3s ease;
}

.btn-edit-inline {
  background: #17a2b8;
  color: white;
}

.btn-save-inline {
  background: #28a745;
  color: white;
}

.btn-cancel-inline {
  background: #dc3545;
  color: white;
}

.btn-edit-inline:hover {
  background: #138496;
  transform: scale(1.05);
}

.btn-save-inline:hover {
  background: #218838;
  transform: scale(1.05);
}

.btn-cancel-inline:hover {
  background: #c82333;
  transform: scale(1.05);
}

.form-input-locked {
  background: #e9ecef !important;
  color: #6c757d;
  cursor: not-allowed;
}

.cliente-locked-info {
  background: #e3f2fd;
  border-radius: 10px;
  padding: 15px;
  margin-bottom: 15px;
}

.locked-header {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #1976d2;
  font-weight: 600;
}

.locked-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #6c757d;
  margin-top: 4px;
}

.form-textarea {
  padding: 12px 15px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 15px;
  background: #f8f9fa;
  font-family: inherit;
  resize: vertical;
  transition: all 0.3s ease;
}

.form-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
  background: white;
}

.payment-methods {
  display: flex;
  gap: 15px;
  margin-bottom: 10px;
}

.radio-group {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 15px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.radio-group:hover {
  border-color: #667eea;
  background: #f8f9fa;
}

.radio-group input[type="radio"] {
  cursor: pointer;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 15px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  width: fit-content;
}

.checkbox-group:hover {
  border-color: #667eea;
  background: #f8f9fa;
}

.payment-summary {
  margin-top: 15px;
  padding: 15px;
  background: #e8f5e9;
  border-radius: 10px;
  border-left: 4px solid #28a745;
}

.payment-info {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  color: #155724;
}

.payment-info.mixed {
  color: #856404;
}

.payment-breakdown {
  margin-top: 8px;
  padding-left: 30px;
  color: #155724;
  font-size: 14px;
}

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 2px solid #e9ecef;
}

.btn-save-all, .btn-cancel-form {
  padding: 15px 30px;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s ease;
}

.btn-save-all {
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
}

.btn-cancel-form {
  background: #6c757d;
  color: white;
}

.btn-save-all:hover, .btn-cancel-form:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(5px);
}

.modal-content {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 550px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px rgba(0,0,0,0.3);
  animation: modalSlideIn 0.3s ease-out;
}

.modal-mixto {
  max-width: 650px;
  max-height: 85vh;
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
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 20px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 20px 20px 0 0;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-close-modal {
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  cursor: pointer;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.btn-close-modal:hover {
  background: rgba(255,255,255,0.2);
  transform: rotate(90deg);
}

.modal-body {
  padding: 30px;
}

.pago-efectivo-content, .pago-mixto-content {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.total-pagar, .total-mixto {
  text-align: center;
}

.total-pagar label, .total-mixto label {
  display: block;
  margin-bottom: 10px;
  font-weight: 600;
  color: #555;
}

.amount-display {
  font-size: 32px;
  font-weight: 700;
  color: #28a745;
  background: #e8f5e9;
  padding: 15px;
  border-radius: 10px;
}

.amount-display.negative {
  color: #dc3545;
  background: #f8d7da;
}

.cantidad-cliente label {
  font-weight: 600;
  color: #555;
  margin-bottom: 8px;
  display: block;
}

.vuelto-display {
  text-align: center;
}

.payment-breakdown-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 10px;
}

.payment-method-input {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.payment-method-input label {
  font-weight: 600;
  color: #555;
  display: flex;
  align-items: center;
  gap: 8px;
}

.total-calculado {
  background: #f8f9fa;
  padding: 25px;
  border-radius: 10px;
  margin-top: 5px;
}

.subtotal-line, .diferencia-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-weight: 600;
}

.diferencia-line.error {
  color: #dc3545;
}

.diferencia-line.success {
  color: #28a745;
}

.status-text {
  font-size: 14px;
  margin-left: 5px;
}

.error-message {
  background: #f8d7da;
  color: #721c24;
  padding: 12px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 10px;
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

.btn-confirm, .btn-cancel {
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-confirm {
  background: #28a745;
  color: white;
}

.btn-cancel {
  background: #6c757d;
  color: white;
}

.btn-confirm:hover, .btn-cancel:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.btn-confirm:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.loading-spinner {
  background: white;
  padding: 40px;
  border-radius: 15px;
  text-align: center;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 25px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 3000;
  animation: slideInRight 0.3s ease-out;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.notification.success {
  background: #d4edda;
  color: #155724;
  border: 2px solid #c3e6cb;
}

.notification.error {
  background: #f8d7da;
  color: #721c24;
  border: 2px solid #f5c6cb;
}

.notification.warning {
  background: #fff3cd;
  color: #856404;
  border: 2px solid #ffeaa7;
}

.notification.info {
  background: #d1ecf1;
  color: #0c5460;
  border: 2px solid #bee5eb;
}

.btn-close-notification {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
  color: inherit;
  padding: 5px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.btn-close-notification:hover {
  background: rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
  .detalles-pasaje-container {
    padding: 15px;
  }

  .header-actions {
    flex-direction: column;
  }

  .btn-imagen, .btn-ticket, .btn-secondary {
    width: 100%;
    justify-content: center;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .payment-methods {
    flex-direction: column;
  }

  .form-actions {
    flex-direction: column-reverse;
  }

  .btn-save-all, .btn-cancel-form {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    width: 95%;
    margin: 20px;
  }

  .notification {
    left: 15px;
    right: 15px;
    top: 15px;
  }
}
</style>