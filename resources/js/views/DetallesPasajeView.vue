<template>
  <div class="detalles-pasaje-container">
    <!-- Header -->
    <div class="header-section">
      <h2>Detalles del pasaje</h2>
      <div class="header-actions">
        <button @click="generarPDFTiempoReal" class="btn-pdf" :disabled="!puedeGenerarPDF">
          <i class="fas fa-file-pdf"></i>
          Generar PDF
        </button>
        <button @click="imprimirTicketVenta" class="btn-ticket" :disabled="!puedeGenerarPDF">
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
            <select id="embarcacion" v-model="datosViaje.embarcacion" class="form-select" @change="validarPDF">
              <option value="">Seleccionar embarcación</option>
              <option v-for="embarcacion in embarcaciones" :key="embarcacion" :value="embarcacion">
                {{ embarcacion }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="puertoEmbarque">Puerto de embarque:</label>
            <select id="puertoEmbarque" v-model="datosViaje.puertoEmbarque" class="form-select" @change="validarPDF">
              <option value="">Seleccionar puerto</option>
              <option v-for="puerto in puertosEmbarque" :key="puerto" :value="puerto">
                {{ puerto }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="horaEmbarque">Hora de embarque:</label>
            <select id="horaEmbarque" v-model="datosViaje.horaEmbarque" class="form-select" @change="validarPDF">
              <option value="">Seleccionar hora</option>
              <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
                {{ hora }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="horaSalida">Hora de salida:</label>
            <select id="horaSalida" v-model="datosViaje.horaSalida" class="form-select" @change="validarPDF">
              <option value="">Seleccionar hora</option>
              <option v-for="hora in horasDisponibles" :key="hora" :value="hora">
                {{ hora }}
              </option>
            </select>
          </div>

          <!-- Medio de Pago MEJORADO -->
          <div class="form-group">
            <label>Medio de pago:</label>
            <div class="payment-methods">
              <div class="radio-group" @click="seleccionarMedioPago('efectivo')">
                <input 
                  type="radio" 
                  id="efectivo" 
                  value="efectivo" 
                  v-model="datosViaje.medioPago"
                  @change="validarPDF"
                >
                <label for="efectivo">Efectivo</label>
              </div>
              <div class="radio-group" @click="seleccionarMedioPago('yape')">
                <input 
                  type="radio" 
                  id="yape" 
                  value="yape" 
                  v-model="datosViaje.medioPago"
                  @change="validarPDF"
                >
                <label for="yape">Yape</label>
              </div>
              <div class="radio-group" @click="seleccionarMedioPago('plin')">
                <input 
                  type="radio" 
                  id="plin" 
                  value="plin" 
                  v-model="datosViaje.medioPago"
                  @change="validarPDF"
                >
                <label for="plin">Plin</label>
              </div>
            </div>
            <div class="checkbox-group" @click="togglePagoMixto">
              <input 
                type="checkbox" 
                id="pagoMixto" 
                v-model="datosViaje.pagoMixto"
                @change="validarPDF"
              >
              <label for="pagoMixto">Pago Mixto</label>
            </div>

            <!-- Mostrar resumen del pago seleccionado -->
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

        <!-- Columna Derecha - Datos del Cliente + Notas -->
        <div class="form-column">
          <!-- Indicador de datos bloqueados -->
          <div class="cliente-locked-info">
            <div class="locked-header">
              <i class="fas fa-lock"></i>
              <span>Datos del Cliente (Cargados automáticamente)</span>
            </div>
          </div>

          <!-- Datos del Cliente - CAMPOS BLOQUEADOS -->
          <div class="form-group">
            <label for="nombreCliente">Nombre del Cliente:</label>
            <input 
              id="nombreCliente"
              type="text"
              v-model="datosViaje.nombreCliente"
              class="form-input form-input-locked"
              placeholder="Cargado automáticamente desde el cliente seleccionado"
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
              placeholder="Cargado automáticamente desde el cliente seleccionado"
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
              placeholder="Cargado automáticamente desde el cliente seleccionado"
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
              placeholder="Cargado automáticamente desde el cliente seleccionado"
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
              @input="validarPDF"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Botones de Acción del Formulario -->
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

    <!-- Modal para Pago en Efectivo -->
    <div v-if="mostrarModalEfectivo" class="modal-overlay" @click="cerrarModalEfectivo">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>
            <i class="fas fa-money-bill-wave"></i>
            Pago en Efectivo
          </h3>
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

    <!-- Modal para Pago Mixto -->
    <div v-if="mostrarModalPagoMixto" class="modal-overlay" @click="cerrarModalPagoMixto">
      <div class="modal-content modal-mixto" @click.stop>
        <div class="modal-header">
          <h3>
            <i class="fas fa-coins"></i>
            Configurar Pago Mixto
          </h3>
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
                <label>
                  <i class="fas fa-money-bill-wave"></i>
                  Efectivo:
                </label>
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
                <label>
                  <i class="fab fa-cc-visa"></i>
                  Yape:
                </label>
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
                <label>
                  <i class="fas fa-mobile-alt"></i>
                  Plin:
                </label>
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

    <!-- Mensajes de notificación -->
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
      puedeGenerarPDF: false,
      
      // Modales
      mostrarModalEfectivo: false,
      mostrarModalPagoMixto: false,
      
      // Datos de pago en efectivo
      cantidadCliente: 0,
      vuelto: 0,
      
      // Datos de pago mixto
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
      
      // Lista de embarcaciones
      embarcaciones: [
        'KAORY',
        'DON JULIO',
        'ORIENTE 1',
        'HAYDEE',
        'MACHI MACHIN',
        'MAGIN',
        'TONY',
        'CRISTIAN',
        'ALEAXIA',
        'ROMERO',
        'NR',
        'VALERIA 1',
        'ZOE ALEXA',
        'RAYZA',
        'DOÑA LIDIA'
      ],

      // Puertos de embarque actualizados
      puertosEmbarque: [
        'CALLE PEVAS #456',
        'CALLE PEVAS #408',
        'CALLE REQUENA #155',
        'CALLE ABTAO #1350',
        'PUERTO PRINCIPAL DE NAUTA',
        'CALLE JR LIMA #712 (NAUTA)',
        'PUERTO LA BOCA',
        'PUERTO MAGIN',
        'LA CAPITANIA',
        'PUERTO DIEGUITO',
        'PUERTO SAJUTA',
        'LA CASONA DE SAJUTA',
        'PUERTO RELOJ PÚBLICO',
        'PUERTO GANZO AZUL',
        'LA BALSA MUNICIPAL',
        'FITZCARRALD #377',
        'PUERTO MANAOS',
        'CALLE NAUTA #342',
        'PUERTO PRINCIPAL DE'
      ],

      // Horas disponibles con AM/PM
      horasDisponibles: [
        '06:00 AM', '06:30 AM', '07:00 AM', '07:30 AM', '08:00 AM', '08:30 AM',
        '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM',
        '12:00 PM', '12:30 PM', '01:00 PM', '01:30 PM', '02:00 PM', '02:30 PM',
        '03:00 PM', '03:30 PM', '04:00 PM', '04:30 PM', '05:00 PM', '05:30 PM',
        '06:00 PM', '06:30 PM', '07:00 PM', '07:30 PM', '08:00 PM'
      ],

      // Detalles del pasaje (se cargarán automáticamente)
      detallesPasaje: [],
      
      // Datos del viaje
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
    console.log('🚀 Componente DetallesPasajeView montado')
    // Cargar datos automáticamente cuando se monte el componente
    this.cargarDatosDesdeFormulario()
    // Cargar datos del cliente
    this.cargarDatosCliente()
  },

  methods: {
    // Nuevos métodos para manejo de medios de pago
    seleccionarMedioPago(tipo) {
      this.datosViaje.medioPago = tipo
      this.datosViaje.pagoMixto = false
      this.validarPDF()
      
      // Mostrar modal de efectivo si es necesario
      if (tipo === 'efectivo') {
        this.abrirModalEfectivo()
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
      
      this.validarPDF()
    },

    // Modal de Efectivo
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

    // Modal de Pago Mixto
    abrirModalPagoMixto() {
      this.pagoMixtoTemp = {
        efectivo: 0,
        yape: 0,
        plin: 0
      }
      this.mostrarModalPagoMixto = true
    },

    cerrarModalPagoMixto() {
      this.mostrarModalPagoMixto = false
      this.pagoMixtoTemp = {
        efectivo: 0,
        yape: 0,
        plin: 0
      }
    },

    calcularPagoMixto() {
      // El cálculo se hace automáticamente con computed properties
    },

    confirmarPagoMixto() {
      if (this.diferenciaPago === 0) {
        this.pagoMixtoDetalle = { ...this.pagoMixtoTemp }
        
        // Crear descripción detallada
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
        this.mostrarMensaje('Pago mixto configurado correctamente', 'success')
        this.cerrarModalPagoMixto()
      }
    },

    limpiarPagoMixto() {
      this.pagoMixtoDetalle = {
        efectivo: 0,
        yape: 0,
        plin: 0
      }
      this.datosViaje.detallesPago = ''
    },

    // Métodos auxiliares
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

    // Método para cargar datos del cliente desde sessionStorage
    cargarDatosCliente() {
      console.log('👤 Cargando datos del cliente...')
      
      const datosCliente = sessionStorage.getItem('datosCliente')
      if (datosCliente) {
        try {
          const cliente = JSON.parse(datosCliente)
          console.log('✅ Datos del cliente encontrados:', cliente)
          
          // Cargar los datos en los campos correspondientes
          this.datosViaje.nombreCliente = cliente.nombre || ''
          this.datosViaje.documentoCliente = cliente.documento || ''
          this.datosViaje.contactoCliente = cliente.contacto || ''
          this.datosViaje.nacionalidadCliente = cliente.nacionalidad || 'PERUANA'
          
          this.mostrarMensaje('Datos del cliente cargados automáticamente', 'info')
          this.validarPDF()
        } catch (error) {
          console.error('❌ Error al cargar datos del cliente:', error)
          this.mostrarMensaje('Error al cargar los datos del cliente', 'warning')
        }
      } else {
        console.log('⚠️ No se encontraron datos del cliente en sessionStorage')
        this.mostrarMensaje('No se encontraron datos del cliente. Complete manualmente si es necesario.', 'warning')
      }
    },

    // Validar si se puede generar PDF
    validarPDF() {
      this.puedeGenerarPDF = this.detallesPasaje.length > 0 && 
                            this.datosViaje.embarcacion && 
                            this.datosViaje.puertoEmbarque &&
                            this.datosViaje.horaEmbarque &&
                            this.datosViaje.horaSalida

      console.log('📋 Validación PDF:', {
        tieneDetalles: this.detallesPasaje.length > 0,
        embarcacion: !!this.datosViaje.embarcacion,
        puerto: !!this.datosViaje.puertoEmbarque,
        horaEmbarque: !!this.datosViaje.horaEmbarque,
        horaSalida: !!this.datosViaje.horaSalida,
        puedeGenerar: this.puedeGenerarPDF
      })
    },

    // Imprimir Ticket de Venta (MEJORADO CON DEBUGGING)
    async imprimirTicketVenta() {
      console.log('🎫 Iniciando generación de ticket de venta...')
      
      if (!this.puedeGenerarPDF) {
        this.mostrarMensaje('Complete todos los campos obligatorios para imprimir el ticket', 'warning')
        return
      }

      // Validar que tenemos datos del pasaje
      if (this.detallesPasaje.length === 0) {
        this.mostrarMensaje('No hay detalles de pasaje para generar el ticket', 'error')
        return
      }

      try {
        this.cargando = true
        this.mensajeCarga = 'Generando ticket de venta...'
        
        // Preparar datos completos para el ticket
        const datosCompletos = this.prepararDatosParaTicket()
        
        console.log('🎫 Datos preparados para ticket:', JSON.stringify(datosCompletos, null, 2))

        // Verificar que todos los campos obligatorios estén presentes
        const camposObligatorios = [
          'numero_ticket', 'fecha_emision', 'hora_emision', 
          'nombre_cliente', 'cantidad', 'descripcion', 
          'precio_unitario', 'total', 'embarcacion'
        ]
        
        const camposFaltantes = camposObligatorios.filter(campo => 
          !datosCompletos[campo] || datosCompletos[campo] === 'N/A'
        )
        
        if (camposFaltantes.length > 0) {
          console.error('❌ Campos faltantes:', camposFaltantes)
          this.mostrarMensaje(`Faltan campos obligatorios: ${camposFaltantes.join(', ')}`, 'error')
          return
        }

        console.log('✅ Validación de campos completada, enviando request...')

        // Llamada al endpoint de ticket de venta
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

        console.log('📥 Respuesta del servidor:', {
          status: response.status, 
          statusText: response.statusText,
          headers: Object.fromEntries(response.headers.entries())
        })

        if (response.ok) {
          // Obtener el HTML del ticket
          const htmlContent = await response.text()
          console.log('✅ HTML del ticket recibido, longitud:', htmlContent.length)
          
          // Crear una nueva ventana para mostrar el ticket
          const ticketWindow = window.open('', '_blank', 'width=400,height=600,scrollbars=yes,resizable=yes')
          
          if (ticketWindow) {
            ticketWindow.document.write(htmlContent)
            ticketWindow.document.close()
            
            // Enfocar la ventana del ticket
            ticketWindow.focus()
            
            this.mostrarMensaje('Ticket de venta generado exitosamente', 'success')
          } else {
            throw new Error('No se pudo abrir la ventana del ticket. Verifique que el bloqueador de ventanas emergentes esté desactivado.')
          }
        } else {
          // Error del servidor - obtener detalles
          let errorText = ''
          try {
            const contentType = response.headers.get('content-type')
            if (contentType && contentType.includes('application/json')) {
              const errorJson = await response.json()
              errorText = JSON.stringify(errorJson, null, 2)
              console.error('❌ Error JSON del servidor:', errorJson)
              
              // Si hay errores de validación específicos
              if (errorJson.errors) {
                const erroresValidacion = Object.entries(errorJson.errors)
                  .map(([campo, mensajes]) => `${campo}: ${mensajes.join(', ')}`)
                  .join('\n')
                this.mostrarMensaje(`Errores de validación:\n${erroresValidacion}`, 'error')
                return
              }
            } else {
              errorText = await response.text()
            }
          } catch (parseError) {
            errorText = 'No se pudo obtener detalles del error'
            console.error('❌ Error al parsear respuesta de error:', parseError)
          }
          
          console.error('❌ Error del servidor:', {
            status: response.status,
            statusText: response.statusText,
            errorText: errorText
          })
          
          throw new Error(`Error ${response.status}: ${response.statusText}\nDetalles: ${errorText}`)
        }

      } catch (error) {
        console.error('❌ Error generando ticket:', error)
        this.mostrarMensaje(`Error al generar el ticket: ${error.message}`, 'error')
      } finally {
        this.cargando = false
      }
    },

    // Preparar datos para el ticket de venta
    prepararDatosParaTicket() {
      const ahora = new Date()
      const fechaActual = this.formatearFecha(ahora)
      const horaActual = this.formatearHoraTicket(ahora)
      
      const primerDetalle = this.detallesPasaje[0]
      
      // Validar que tenemos todos los datos necesarios
      if (!primerDetalle) {
        throw new Error('No hay detalles de pasaje disponibles')
      }
      
      // Determinar el medio de pago para el ticket 
      // Basado en los valores que acepta el backend
      let medioPagoTicket = 'efectivo'  // valor por defecto
      let esPagoMixto = false
      
      if (this.datosViaje.pagoMixto) {
        // Para pago mixto, usar "pago mixto" (el backend debe aceptar este valor)
        medioPagoTicket = 'pago mixto'
        esPagoMixto = true
      } else if (this.datosViaje.medioPago) {
        // Si hay un medio de pago específico seleccionado
        switch(this.datosViaje.medioPago.toLowerCase()) {
          case 'efectivo':
            medioPagoTicket = 'efectivo'
            break
          case 'yape':
            medioPagoTicket = 'yape'
            break
          case 'plin':
            medioPagoTicket = 'plin'
            break
          default:
            medioPagoTicket = 'efectivo'
        }
      }
      
      console.log('💳 Medio de pago determinado para ticket:', {
        pagoMixto: this.datosViaje.pagoMixto,
        medioPago: this.datosViaje.medioPago,
        resultado: medioPagoTicket,
        esPagoMixto: esPagoMixto
      })
      
      return {
        // Datos del ticket - campos obligatorios
        numero_ticket: this.generarNumeroTicket(),
        fecha_emision: fechaActual,
        hora_emision: horaActual,
        
        // ESTRUCTURA CLIENTE COMO OBJETO ANIDADO (requerido por backend)
        cliente: {
          nombre: this.datosViaje.nombreCliente || 'CLIENTE',
          documento: this.datosViaje.documentoCliente || 'N/A',
          contacto: this.datosViaje.contactoCliente || 'N/A',
          nacionalidad: this.datosViaje.nacionalidadCliente || 'PERUANA'
        },
        
        // También mantener campos planos por compatibilidad
        nombre_cliente: this.datosViaje.nombreCliente || 'CLIENTE',
        documento_cliente: this.datosViaje.documentoCliente || 'N/A',
        contacto_cliente: this.datosViaje.contactoCliente || 'N/A',
        nacionalidad_cliente: this.datosViaje.nacionalidadCliente || 'PERUANA',
        
        // Datos del pasaje - validados
        cantidad: parseInt(primerDetalle.cantidad) || 1,
        descripcion: primerDetalle.descripcion || 'Pasaje',
        precio_unitario: parseFloat(primerDetalle.precio_unitario || 0).toFixed(2),
        subtotal: parseFloat(primerDetalle.subtotal || 0).toFixed(2),
        total: this.totalGeneral.toFixed(2),
        
        // Datos del viaje - con validaciones
        fecha_viaje: fechaActual,
        embarcacion: this.datosViaje.embarcacion || 'N/A',
        puerto_embarque: this.datosViaje.puertoEmbarque || 'N/A',
        hora_embarque: this.datosViaje.horaEmbarque || 'N/A',
        hora_salida: this.datosViaje.horaSalida || 'N/A',
        
        // MEDIO DE PAGO - PROBANDO DIFERENTES VALORES
        medio_pago: medioPagoTicket,
        pago_mixto: esPagoMixto,
        detalles_pago: this.datosViaje.detallesPago || '',
        
        nota: this.datosViaje.nota || '',
        
        // Campos adicionales que podrían ser requeridos
        destino: primerDetalle.destino || primerDetalle.descripcion || 'N/A',
        ruta: primerDetalle.ruta || primerDetalle.descripcion || 'N/A',
        operador: 'ROCÍO TRAVEL'
      }
    },

    // Generar número de ticket único
    generarNumeroTicket() {
      const ahora = new Date()
      const año = ahora.getFullYear().toString().slice(-2)
      const mes = String(ahora.getMonth() + 1).padStart(2, '0')
      const dia = String(ahora.getDate()).padStart(2, '0')
      const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0')
      
      return `NV-${año}${mes}${dia}${random}`
    },

    // Formatear hora para ticket (formato 24 horas)
    formatearHoraTicket(fecha) {
      const horas = String(fecha.getHours()).padStart(2, '0')
      const minutos = String(fecha.getMinutes()).padStart(2, '0')
      const segundos = String(fecha.getSeconds()).padStart(2, '0')
      
      return `${horas}:${minutos}:${segundos}`
    },

    // Generar PDF en tiempo real SIN GUARDAR
    async generarPDFTiempoReal() {
      console.log('🔥 Iniciando generación PDF tiempo real...')
      
      if (!this.puedeGenerarPDF) {
        this.mostrarMensaje('Complete todos los campos obligatorios: embarcación, puerto, horas de embarque y salida', 'warning')
        return
      }

      try {
        this.cargando = true
        this.mensajeCarga = 'Generando PDF en tiempo real...'
        
        // Preparar datos completos para el PDF
        const datosCompletos = this.prepararDatosParaPDF()
        
        console.log('📤 Enviando datos para PDF:', datosCompletos)

        // Llamada al endpoint de PDF en tiempo real
        const response = await fetch('/api/pasajes/generar-pdf-tiempo-real', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/pdf',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify(datosCompletos)
        })

        console.log('📥 Respuesta del servidor:', response.status, response.statusText)

        if (response.ok) {
          // Descargar PDF
          const blob = await response.blob()
          const url = window.URL.createObjectURL(blob)
          const link = document.createElement('a')
          link.href = url
          
          // Generar nombre de archivo con fecha y hora
          const fecha = new Date()
          const nombreArchivo = `pasaje-${fecha.getFullYear()}${String(fecha.getMonth()+1).padStart(2, '0')}${String(fecha.getDate()).padStart(2, '0')}-${String(fecha.getHours()).padStart(2, '0')}${String(fecha.getMinutes()).padStart(2, '0')}.pdf`
          
          link.download = nombreArchivo
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
          window.URL.revokeObjectURL(url)
          
          this.mostrarMensaje('PDF generado exitosamente', 'success')
        } else {
          const errorText = await response.text()
          console.error('Error del servidor:', errorText)
          throw new Error(`Error ${response.status}: ${response.statusText}`)
        }

      } catch (error) {
        console.error('Error generando PDF:', error)
        this.mostrarMensaje(`Error al generar el PDF: ${error.message}`, 'error')
      } finally {
        this.cargando = false
      }
    },

    // Preparar datos para el PDF con fecha y hora actual
    prepararDatosParaPDF() {
      const ahora = new Date()
      const fechaActual = this.formatearFecha(ahora)
      const horaActual = this.formatearHora(ahora)
      
      const primerDetalle = this.detallesPasaje[0]
      
      return {
        // Datos del PDF
        fecha_emision: fechaActual,
        hora_emision: horaActual,
        operador: 'ROCÍO TRAVEL',
        
        // Datos del cliente
        cliente: {
          nombre: this.datosViaje.nombreCliente || 'CLIENTE',
          documento: this.datosViaje.documentoCliente || '',
          contacto: this.datosViaje.contactoCliente || '',
          nacionalidad: this.datosViaje.nacionalidadCliente || ''
        },
        
        // Datos del pasaje
        cantidad: primerDetalle.cantidad,
        descripcion: primerDetalle.descripcion,
        precio_unitario: primerDetalle.precio_unitario,
        subtotal: primerDetalle.subtotal,
        total: this.totalGeneral,
        
        // Datos del viaje
        embarcacion: this.datosViaje.embarcacion,
        puerto_embarque: this.datosViaje.puertoEmbarque,
        hora_embarque: this.datosViaje.horaEmbarque,
        hora_salida: this.datosViaje.horaSalida,
        medio_pago: this.datosViaje.medioPago,
        pago_mixto: this.datosViaje.pagoMixto,
        detalles_pago: this.datosViaje.detallesPago,
        nota: this.datosViaje.nota,
        
        // Información adicional
        destino: primerDetalle.destino || '',
        ruta: primerDetalle.ruta || primerDetalle.descripcion
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

    // Cargar datos desde el formulario anterior
    cargarDatosDesdeFormulario() {
      console.log('🔍 Cargando datos desde el formulario anterior...')
      
      // Método 1: Desde query parameters (navegación con router)
      const queryParams = this.$route.query
      
      // Método 2: Desde sessionStorage
      const datosSessionStorage = sessionStorage.getItem('datosPasajeNavegacion')
      let datosParsed = null
      
      if (datosSessionStorage) {
        try {
          datosParsed = JSON.parse(datosSessionStorage)
          console.log('📦 Datos desde sessionStorage:', datosParsed)
        } catch (error) {
          console.error('Error al parsear datos de sessionStorage:', error)
        }
      }
      
      // Usar datos de query params o sessionStorage (prioridad a query params)
      const datosPasaje = Object.keys(queryParams).length > 0 ? queryParams : datosParsed
      
      console.log('📋 Datos del pasaje recibidos:', datosPasaje)

      if (datosPasaje && (datosPasaje.cantidad || datosPasaje.descripcion)) {
        // Crear objeto de detalle con los datos recibidos
        const detalle = {
          cantidad: parseInt(datosPasaje.cantidad) || 1,
          descripcion: datosPasaje.descripcion || datosPasaje.ruta || '',
          precio_unitario: parseFloat(datosPasaje.precio_unitario) || 0,
          subtotal: parseFloat(datosPasaje.subtotal) || 0,
          destino: datosPasaje.destino || '',
          ruta: datosPasaje.ruta || ''
        }

        console.log('✅ Detalle creado:', detalle)

        // Añadir a la lista de detalles
        this.detallesPasaje = [detalle]
        
        this.mostrarMensaje('Datos cargados desde el formulario anterior', 'success')
        this.validarPDF() // Validar si ya se puede generar PDF
      } else {
        console.log('⚠️ No se encontraron datos del pasaje')
        this.mostrarMensaje('No se encontraron datos del formulario anterior', 'warning')
      }
    },

    async guardarTodo() {
      if (!this.validarFormularioCompleto()) {
        return
      }

      this.cargando = true
      this.mensajeCarga = 'Guardando datos...'
      try {
        await this.enviarTodosLosDatos()
        this.mostrarMensaje('Todos los datos guardados correctamente', 'success')
        
        // Opcional: redireccionar después de 2 segundos
        setTimeout(() => {
          // this.$router.push('/') // Descomentar si quieres redireccionar
        }, 2000)
      } catch (error) {
        console.error('Error al guardar todo:', error)
        this.mostrarMensaje('Error al guardar todos los datos', 'error')
      } finally {
        this.cargando = false
      }
    },

    validarFormularioCompleto() {
      if (this.detallesPasaje.length === 0) {
        this.mostrarMensaje('No hay detalles de pasaje para guardar', 'warning')
        return false
      }
      
      if (!this.datosViaje.embarcacion) {
        this.mostrarMensaje('Debe seleccionar una embarcación', 'warning')
        return false
      }
      
      if (!this.datosViaje.puertoEmbarque) {
        this.mostrarMensaje('Debe seleccionar un puerto de embarque', 'warning')
        return false
      }
      
      return true
    },

    async enviarTodosLosDatos() {
      const primerDetalle = this.detallesPasaje[0]
      
      // Combinar todos los datos para el modelo Pasaje
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
        medio_pago: this.datosViaje.medioPago,
        pago_mixto: this.datosViaje.pagoMixto,
        detalles_pago: this.datosViaje.detallesPago,
        nota: this.datosViaje.nota,
        nombre_cliente: this.datosViaje.nombreCliente,
        documento_cliente: this.datosViaje.documentoCliente,
        contacto_cliente: this.datosViaje.contactoCliente,
        nacionalidad_cliente: this.datosViaje.nacionalidadCliente
      }

      console.log('📤 Enviando datos completos al PasajeController:', datosCompletos)

      // Llamada al PasajeController (no DetallePasajeController)
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
      console.log('📥 Respuesta del servidor:', result)

      if (!result.success) {
        throw new Error(result.message || 'Error al guardar los datos')
      }

      // Guardar el ID para poder generar PDF
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
      this.puedeGenerarPDF = false
      
      // Limpiar también datos de pago
      this.limpiarPagoMixto()
      this.cantidadCliente = 0
      this.vuelto = 0
      
      // Limpiar también sessionStorage
      sessionStorage.removeItem('datosPasajeNavegacion')
      sessionStorage.removeItem('datosPasajeActuales')
      sessionStorage.removeItem('datosCliente')
      
      this.mostrarMensaje('Formulario limpiado', 'info')
    },

    cancelar() {
      if (confirm('¿Está seguro de cancelar? Se perderán los datos no guardados.')) {
        this.$router.go(-1) // Volver a la página anterior
      }
    },

    // Métodos de mensajes
    mostrarMensaje(texto, tipo = 'info') {
      this.mensaje = texto
      this.tipoMensaje = tipo
      
      // Auto cerrar después de 5 segundos
      setTimeout(() => {
        this.cerrarMensaje()
      }, 5000)
    },

    cerrarMensaje() {
      this.mensaje = ''
    }
  },

  watch: {
    // Observar cambios en los datos del viaje para validar PDF
    datosViaje: {
      handler() {
        this.validarPDF()
      },
      deep: true
    }
  }
}
</script>

<style scoped>
/* Estilos base existentes... */
.detalles-pasaje-container {
  background: #f8f9fa;
  min-height: 100vh;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Header Section */
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding: 20px 0;
  border-bottom: 2px solid #dee2e6;
}

.header-section h2 {
  margin: 0;
  font-size: 28px;
  color: #333;
  font-weight: 600;
}

.header-actions {
  display: flex;
  gap: 12px;
}

/* Botones */
.btn-pdf {
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
  background: linear-gradient(135deg, #dc3545, #c82333);
  color: white;
}

.btn-pdf:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(220,53,69,0.3);
}

.btn-pdf:disabled {
  background: #6c757d;
  cursor: not-allowed;
  opacity: 0.6;
}

/* Botón Ticket */
.btn-ticket {
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
  background: linear-gradient(135deg, #28a745, #1e7e34);
  color: white;
}

.btn-ticket:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(40,167,69,0.3);
}

.btn-ticket:disabled {
  background: #6c757d;
  cursor: not-allowed;
  opacity: 0.6;
}

.btn-secondary {
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
  background: linear-gradient(135deg, #6c757d, #545b62);
  color: white;
}

.btn-secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(108,117,125,0.3);
}

/* Tabla de Detalles */
.detalles-table-section {
  background: white;
  border-radius: 12px;
  padding: 25px;
  margin-bottom: 30px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.table-wrapper {
  overflow-x: auto;
  border-radius: 8px;
  border: 1px solid #dee2e6;
}

.detalles-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.detalles-table th {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 15px 12px;
  text-align: center;
  font-weight: 600;
  border-bottom: 2px solid #5a67d8;
}

.detalles-table td {
  padding: 12px;
  border-bottom: 1px solid #f1f3f4;
  vertical-align: middle;
}

.text-center { 
  text-align: center; 
}

.text-right { 
  text-align: right; 
}

.subtotal {
  font-weight: 600;
  color: #28a745;
}

.no-items {
  text-align: center;
  padding: 50px 20px;
  color: #999;
}

.no-items i {
  font-size: 36px;
  margin-bottom: 15px;
  color: #ccc;
}

.no-items p {
  margin: 0;
  font-size: 16px;
}

/* Total Section */
.total-section {
  margin-top: 20px;
  padding: 20px;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  border-radius: 8px;
  border: 2px solid #28a745;
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
  background: white;
  padding: 10px 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Form Section */
.form-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

/* Estilos para campos bloqueados */
.cliente-locked-info {
  background: linear-gradient(135deg, #fff3cd, #ffeaa7);
  border: 2px solid #ffc107;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 20px;
}

.locked-header {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  color: #856404;
  font-size: 16px;
}

.locked-header i {
  color: #ffc107;
  font-size: 18px;
}

.form-input-locked {
  background: #f8f9fa !important;
  border-color: #e9ecef !important;
  color: #6c757d !important;
  cursor: not-allowed;
  opacity: 0.8;
}

.locked-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #6c757d;
  font-style: italic;
  margin-top: 5px;
}

.locked-indicator i {
  color: #17a2b8;
  font-size: 14px;
}

.form-input, .form-select {
  padding: 12px 15px;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.form-textarea {
  padding: 12px 15px;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 14px;
  resize: vertical;
  min-height: 80px;
  font-family: inherit;
  transition: border-color 0.3s ease;
}

.form-textarea:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

/* Payment Methods MEJORADO */
.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.radio-group {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}

.radio-group:hover {
  background-color: #f8f9fa;
}

.radio-group input[type="radio"] {
  width: 18px;
  height: 18px;
  accent-color: #007bff;
  pointer-events: none;
}

.radio-group label {
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  pointer-events: none;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 6px;
  transition: background-color 0.3s ease;
}

.checkbox-group:hover {
  background-color: #f8f9fa;
}

.checkbox-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #007bff;
  pointer-events: none;
}

.checkbox-group label {
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  pointer-events: none;
}

/* Resumen de pago */
.payment-summary {
  margin-top: 15px;
  padding: 12px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
  border: 2px solid #2196f3;
}

.payment-info {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  color: #1976d2;
  margin-bottom: 8px;
}

.payment-info.mixed {
  color: #8e24aa;
}

.payment-info i {
  font-size: 18px;
}

.payment-breakdown {
  font-size: 13px;
  color: #666;
  margin-left: 28px;
  padding: 4px 0;
}

/* ESTILOS PARA MODALES */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  animation: modalSlideIn 0.3s ease-out;
}

.modal-mixto {
  max-width: 600px;
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
  padding: 20px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 12px;
}

.modal-header h3 i {
  font-size: 24px;
}

.btn-close-modal {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  transition: background-color 0.3s ease;
}

.btn-close-modal:hover {
  background: rgba(255, 255, 255, 0.2);
}

.modal-body {
  padding: 25px;
  max-height: 60vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 20px 25px;
  border-top: 1px solid #e9ecef;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  background: #f8f9fa;
}

/* ESTILOS PARA MODAL DE EFECTIVO */
.pago-efectivo-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.total-pagar, .cantidad-cliente, .vuelto-display {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.total-pagar label, .cantidad-cliente label, .vuelto-display label {
  font-weight: 600;
  color: #333;
  font-size: 16px;
}

.amount-display {
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  padding: 15px 20px;
  border-radius: 10px;
  font-size: 24px;
  font-weight: 700;
  text-align: center;
  box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.amount-display.negative {
  background: linear-gradient(135deg, #dc3545, #c82333);
  box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #dc3545;
  font-size: 14px;
  font-weight: 500;
  margin-top: 8px;
}

.error-message i {
  font-size: 16px;
}

/* ESTILOS PARA MODAL DE PAGO MIXTO */
.pago-mixto-content {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.total-mixto {
  text-align: center;
}

.total-mixto label {
  font-weight: 600;
  color: #333;
  font-size: 18px;
  display: block;
  margin-bottom: 10px;
}

.payment-breakdown-form {
  display: grid;
  gap: 15px;
}

.payment-method-input {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.payment-method-input label {
  font-weight: 600;
  color: #555;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.payment-method-input label i {
  font-size: 18px;
  width: 20px;
  text-align: center;
}

.payment-method-input input {
  padding: 12px 15px;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

.payment-method-input input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.total-calculado {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 12px;
  border: 2px solid #dee2e6;
}

.subtotal-line, .diferencia-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  font-size: 16px;
}

.subtotal-line {
  font-weight: 500;
  color: #333;
  border-bottom: 1px solid #dee2e6;
  margin-bottom: 10px;
}

.diferencia-line {
  font-weight: 700;
  font-size: 18px;
}

.diferencia-line.success {
  color: #28a745;
}

.diferencia-line.error {
  color: #dc3545;
}

.status-text {
  font-size: 14px;
  font-weight: 500;
  margin-left: 5px;
}

.amount {
  font-weight: 700;
}

/* BOTONES DE MODAL */
.btn-confirm, .btn-cancel {
  padding: 12px 24px;
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

.btn-confirm {
  background: linear-gradient(135deg, #28a745, #1e7e34);
  color: white;
}

.btn-confirm:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.btn-confirm:disabled {
  background: #6c757d;
  cursor: not-allowed;
  opacity: 0.6;
}

.btn-cancel {
  background: linear-gradient(135deg, #6c757d, #545b62);
  color: white;
}

.btn-cancel:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 15px;
  justify-content: center;
  padding-top: 20px;
  border-top: 1px solid #e9ecef;
}

.btn-save-all, .btn-cancel-form {
  padding: 12px 25px;
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

.btn-save-all {
  background: linear-gradient(135deg, #28a745, #1e7e34);
  color: white;
}

.btn-cancel-form {
  background: linear-gradient(135deg, #6c757d, #545b62);
  color: white;
}

.btn-save-all:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(40,167,69,0.3);
}

.btn-cancel-form:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(108,117,125,0.3);
}

/* Loading */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.loading-spinner {
  text-align: center;
  color: #007bff;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-spinner p {
  font-size: 16px;
  margin: 0;
  font-weight: 500;
}

/* Notifications */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  display: flex;
  align-items: center;
  gap: 10px;
  z-index: 1001;
  min-width: 300px;
  animation: slideIn 0.3s ease-out;
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

.notification.success {
  background: #d4edda;
  color: #155724;
  border-left: 4px solid #28a745;
}

.notification.error {
  background: #f8d7da;
  color: #721c24;
  border-left: 4px solid #dc3545;
}

.notification.warning {
  background: #fff3cd;
  color: #856404;
  border-left: 4px solid #ffc107;
}

.notification.info {
  background: #cce7ff;
  color: #004085;
  border-left: 4px solid #007bff;
}

.btn-close-notification {
  background: none;
  border: none;
  color: currentColor;
  cursor: pointer;
  padding: 0;
  margin-left: auto;
  opacity: 0.7;
  transition: opacity 0.3s ease;
}

.btn-close-notification:hover {
  opacity: 1;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .form-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .detalles-pasaje-container {
    padding: 15px;
  }
  
  .header-section {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .modal-content {
    width: 95%;
    margin: 10px;
  }
}

@media (max-width: 768px) {
  .form-actions {
    flex-direction: column;
  }
  
  .btn-save-all, .btn-cancel-form {
    width: 100%;
    justify-content: center;
  }
  
  .header-actions {
    flex-direction: column;
    width: 100%;
  }
  
  .btn-pdf, .btn-ticket, .btn-secondary {
    width: 100%;
    justify-content: center;
  }
  
  .detalles-table {
    font-size: 12px;
  }
  
  .detalles-table th,
  .detalles-table td {
    padding: 8px 6px;
  }
  
  .total-section {
    padding: 15px;
  }
  
  .total-label {
    font-size: 16px;
  }
  
  .total-amount {
    font-size: 20px;
    padding: 8px 15px;
  }
  
  .notification {
    right: 10px;
    left: 10px;
    min-width: auto;
  }
  
  .modal-header h3 {
    font-size: 18px;
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .modal-footer {
    flex-direction: column;
    gap: 10px;
  }
  
  .btn-confirm, .btn-cancel {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .detalles-pasaje-container {
    padding: 10px;
  }
  
  .header-section h2 {
    font-size: 22px;
  }
  
  .form-section {
    padding: 20px;
  }
  
  .detalles-table-section {
    padding: 15px;
  }
  
  .payment-methods {
    gap: 8px;
  }
  
  .radio-group, .checkbox-group {
    font-size: 13px;
  }
  
  .modal-content {
    width: 98%;
    margin: 5px;
  }
  
  .modal-header {
    padding: 15px 20px;
  }
  
  .modal-body {
    padding: 15px;
  }
  
  .amount-display {
    font-size: 20px;
    padding: 12px 15px;
  }
  
  .payment-breakdown-form {
    gap: 12px;
  }
}
</style>