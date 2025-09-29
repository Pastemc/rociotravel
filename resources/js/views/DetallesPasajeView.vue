<template>
  <div class="detalles-pasaje-container">
    <!-- Header -->
    <div class="header-section">
      <h2>Detalles del pasaje</h2>
      <div class="header-actions">
        <button @click="generarImagenTiempoReal" class="btn-imagen" :disabled="!puedeGenerarImagen">
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
            <select id="embarcacion" v-model="datosViaje.embarcacion" class="form-select" @change="validarImagen">
              <option value="">Seleccionar embarcación</option>
              <option v-for="embarcacion in embarcaciones" :key="embarcacion" :value="embarcacion">
                {{ embarcacion }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="puertoEmbarque">Puerto de embarque:</label>
            <select id="puertoEmbarque" v-model="datosViaje.puertoEmbarque" class="form-select" @change="validarImagen">
              <option value="">Seleccionar puerto</option>
              <option v-for="puerto in puertosEmbarque" :key="puerto" :value="puerto">
                {{ puerto }}
              </option>
            </select>
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
              @input="validarImagen"
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
      puedeGenerarImagen: false,
      
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
      
      // Lista de embarcaciones - valores por defecto + carga desde API
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

      // Puertos de embarque - valores por defecto + carga desde API
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
    
    // Asegurar que las listas están disponibles inmediatamente
    this.inicializarListas()
    
    // Cargar datos automáticamente cuando se monte el componente
    this.cargarDatosDesdeFormulario()
    
    // Cargar datos del cliente
    this.cargarDatosCliente()
    
    // Verificar nuevos datos del formulario
    this.verificarNuevosDatosFormulario()
    
    // Intentar cargar datos adicionales de APIs (opcional)
    this.cargarEmbarcacionesYPuertos()
  },

  methods: {
    // NUEVO: Inicializar listas con valores por defecto
    inicializarListas() {
      console.log('📋 Inicializando listas de embarcaciones y puertos...')
      
      // Verificar si las listas están vacías y llenarlas
      if (this.embarcaciones.length === 0) {
        this.embarcaciones = [
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
        ]
      }

      if (this.puertosEmbarque.length === 0) {
        this.puertosEmbarque = [
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
        ]
      }

      console.log('✅ Listas inicializadas:', {
        embarcaciones: this.embarcaciones.length,
        puertos: this.puertosEmbarque.length
      })
    },

    // CORREGIDO: Cargar embarcaciones y puertos desde la API
    async cargarEmbarcacionesYPuertos() {
      try {
        console.log('📡 Intentando cargar embarcaciones y puertos desde la API...')
        
        // Intentar cargar embarcaciones
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
              const embarcacionesAPI = resultEmbarcaciones.data.map(embarcacion => embarcacion.nombre)
              
              // Agregar nuevas embarcaciones que no estén en la lista original
              embarcacionesAPI.forEach(embarcacion => {
                if (!this.embarcaciones.includes(embarcacion)) {
                  this.embarcaciones.push(embarcacion)
                }
              })
              
              console.log('✅ Embarcaciones actualizadas desde API')
            }
          }
        } catch (errorEmbarcaciones) {
          console.log('⚠️ API de embarcaciones no disponible, usando valores por defecto')
        }

        // Intentar cargar puertos de embarque
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
              const puertosAPI = resultPuertos.data.map(puerto => puerto.nombre)
              
              // Agregar nuevos puertos que no estén en la lista original
              puertosAPI.forEach(puerto => {
                if (!this.puertosEmbarque.includes(puerto)) {
                  this.puertosEmbarque.push(puerto)
                }
              })
              
              console.log('✅ Puertos actualizados desde API')
            }
          }
        } catch (errorPuertos) {
          console.log('⚠️ API de puertos no disponible, usando valores por defecto')
        }

        // Verificar si hay nuevos datos desde el formulario de agregación
        this.verificarNuevosDatosFormulario()
        
      } catch (error) {
        console.log('ℹ️ APIs no disponibles, continuando con valores por defecto')
        // Los valores por defecto ya están cargados, así que continuamos normalmente
        this.verificarNuevosDatosFormulario()
      }
    },

    // MEJORADO: Verificar y agregar datos del formulario de agregación
    verificarNuevosDatosFormulario() {
      console.log('🔍 Verificando nuevos datos desde formulario...')
      
      let huboActualizacion = false
      
      // Verificar si hay una nueva embarcación desde el formulario
      // CORREGIDO: Usar los nombres correctos que guarda AgregarDetalleView
      const nuevaEmbarcacion = sessionStorage.getItem('nuevaEmbarcacion') || 
                              sessionStorage.getItem('nuevaEmbarcacionAgregada')
      if (nuevaEmbarcacion && nuevaEmbarcacion.trim() !== '') {
        if (!this.embarcaciones.includes(nuevaEmbarcacion)) {
          this.embarcaciones.push(nuevaEmbarcacion)
          huboActualizacion = true
        }
        // Seleccionar automáticamente la nueva embarcación
        this.datosViaje.embarcacion = nuevaEmbarcacion
        console.log('🚢 Nueva embarcación agregada automáticamente:', nuevaEmbarcacion)
        this.mostrarMensaje(`Embarcación "${nuevaEmbarcacion}" agregada automáticamente`, 'success')
        // Limpiar ambos sessionStorage
        sessionStorage.removeItem('nuevaEmbarcacion')
        sessionStorage.removeItem('nuevaEmbarcacionAgregada')
      }

      // Verificar si hay un nuevo puerto desde el formulario
      // CORREGIDO: Usar los nombres correctos que guarda AgregarDetalleView
      const nuevoPuerto = sessionStorage.getItem('nuevoPuertoEmbarque') || 
                         sessionStorage.getItem('nuevoPuertoAgregado')
      if (nuevoPuerto && nuevoPuerto.trim() !== '') {
        if (!this.puertosEmbarque.includes(nuevoPuerto)) {
          this.puertosEmbarque.push(nuevoPuerto)
          huboActualizacion = true
        }
        // Seleccionar automáticamente el nuevo puerto
        this.datosViaje.puertoEmbarque = nuevoPuerto
        console.log('⚓ Nuevo puerto agregado automáticamente:', nuevoPuerto)
        this.mostrarMensaje(`Puerto "${nuevoPuerto}" agregado automáticamente`, 'success')
        // Limpiar ambos sessionStorage
        sessionStorage.removeItem('nuevoPuertoEmbarque')
        sessionStorage.removeItem('nuevoPuertoAgregado')
      }

      // Validar imagen después de agregar nuevos datos
      if (huboActualizacion) {
        this.validarImagen()
        // Forzar actualización del DOM
        this.$forceUpdate()
      }

      console.log('📊 Estado actual de listas:', {
        embarcaciones: this.embarcaciones.length,
        puertos: this.puertosEmbarque.length,
        embarcacionSeleccionada: this.datosViaje.embarcacion,
        puertoSeleccionado: this.datosViaje.puertoEmbarque
      })
    },

    // Nuevos métodos para manejo de medios de pago
    seleccionarMedioPago(tipo) {
      this.datosViaje.medioPago = tipo
      this.datosViaje.pagoMixto = false
      this.limpiarPagoMixto()
      this.validarImagen()
      
      // Configurar detalle según tipo de pago
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
        
        // CORREGIDO: Asegurar que el pago mixto se marque correctamente
        this.datosViaje.pagoMixto = true
        this.datosViaje.medioPago = 'mixto'
        
        this.mostrarMensaje('Pago mixto configurado correctamente', 'success')
        this.cerrarModalPagoMixto()
        
        // Validar después de configurar
        this.validarImagen()
        
        console.log('✅ Pago mixto confirmado:', {
          pagoMixto: this.datosViaje.pagoMixto,
          medioPago: this.datosViaje.medioPago,
          detalles: this.datosViaje.detallesPago,
          montos: this.pagoMixtoDetalle
        })
      }
    },

    limpiarPagoMixto() {
      this.pagoMixtoDetalle = {
        efectivo: 0,
        yape: 0,
        plin: 0
      }
      if (!this.datosViaje.medioPago) {
        this.datosViaje.detallesPago = ''
      }
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
          this.validarImagen()
        } catch (error) {
          console.error('❌ Error al cargar datos del cliente:', error)
          this.mostrarMensaje('Error al cargar los datos del cliente', 'warning')
        }
      } else {
        console.log('⚠️ No se encontraron datos del cliente en sessionStorage')
        this.mostrarMensaje('No se encontraron datos del cliente. Complete manualmente si es necesario.', 'warning')
      }
    },

    // ACTUALIZADO: Validar si se puede generar imagen
    validarImagen() {
      const camposBasicos = this.detallesPasaje.length > 0 && 
                           this.datosViaje.embarcacion && 
                           this.datosViaje.puertoEmbarque &&
                           this.datosViaje.horaEmbarque &&
                           this.datosViaje.horaSalida

      // Validar medio de pago
      let medioPagoValido = false
      
      if (this.datosViaje.pagoMixto) {
        // Para pago mixto, verificar que esté configurado
        medioPagoValido = this.datosViaje.detallesPago && 
                         this.datosViaje.detallesPago.trim() !== '' &&
                         (this.pagoMixtoDetalle.efectivo > 0 || 
                          this.pagoMixtoDetalle.yape > 0 || 
                          this.pagoMixtoDetalle.plin > 0)
      } else {
        // Para pago simple, verificar que haya medio de pago
        medioPagoValido = this.datosViaje.medioPago && 
                         this.datosViaje.medioPago.trim() !== ''
      }

      this.puedeGenerarImagen = camposBasicos && medioPagoValido

      console.log('📋 Validación Imagen:', {
        tieneDetalles: this.detallesPasaje.length > 0,
        embarcacion: !!this.datosViaje.embarcacion,
        puerto: !!this.datosViaje.puertoEmbarque,
        horaEmbarque: !!this.datosViaje.horaEmbarque,
        horaSalida: !!this.datosViaje.horaSalida,
        pagoMixto: this.datosViaje.pagoMixto,
        medioPago: this.datosViaje.medioPago,
        detallesPago: this.datosViaje.detallesPago,
        medioPagoValido: medioPagoValido,
        puedeGenerar: this.puedeGenerarImagen
      })
    },

    // CORREGIDO: Generar imagen en tiempo real - descarga automática JPG
    async generarImagenTiempoReal() {
      console.log('🖼️ Iniciando generación de imagen tiempo real...')
      
      if (!this.puedeGenerarImagen) {
        this.mostrarMensaje('Complete todos los campos obligatorios: embarcación, puerto, horas y medio de pago', 'warning')
        return
      }

      try {
        this.cargando = true
        this.mensajeCarga = 'Generando imagen del pasaje...'
        
        const datosCompletos = this.prepararDatosParaImagen()
        
        console.log('📤 Enviando datos para imagen:', datosCompletos)

        // Lista de rutas a intentar en orden de prioridad
        const rutasParaProbar = [
          '/imagen-boleta',                           // Ruta temporal funcional
          '/api/pasajes/generar-imagen-tiempo-real',  // Ruta API principal
          '/generar-imagen-pasaje'                    // Ruta directa
        ]

        let response = null
        let rutaUsada = null

        // Intentar cada ruta hasta que una funcione
        for (const ruta of rutasParaProbar) {
          try {
            console.log(`🔄 Intentando ruta: ${ruta}`)
            
            response = await fetch(ruta, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': '*/*',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
              },
              body: JSON.stringify(datosCompletos)
            })

            if (response.ok) {
              rutaUsada = ruta
              break
            } else if (response.status === 405) {
              console.log(`❌ Ruta ${ruta} no permite POST, probando siguiente...`)
              continue
            } else {
              console.log(`⚠️ Ruta ${ruta} respondió con status ${response.status}`)
              continue
            }
          } catch (error) {
            console.log(`❌ Error en ruta ${ruta}:`, error.message)
            continue
          }
        }

        if (!response || !response.ok) {
          throw new Error('Ninguna ruta de generación de imagen está disponible. Verifica la configuración del servidor.')
        }

        console.log(`✅ Imagen generada usando ruta: ${rutaUsada}`)

        // Procesar la respuesta y convertir a JPG
        const contentType = response.headers.get('content-type')
        
        if (contentType && contentType.includes('text/html')) {
          // Respuesta HTML - convertir a imagen JPG
          const htmlContent = await response.text()
          console.log('📄 HTML recibido, convirtiendo a JPG...')
          
          await this.convertirHtmlAImagenJPG(htmlContent)
        } else if (contentType && contentType.includes('application/json')) {
          // Respuesta JSON con base64
          const result = await response.json()
          
          if (result.success && result.imagen_base64) {
            this.procesarImagenBase64(result.imagen_base64)
          } else {
            throw new Error(result.message || 'Error al generar la imagen')
          }
        } else if (contentType && (contentType.includes('image/jpeg') || contentType.includes('image/png'))) {
          // Respuesta directa de imagen
          const blob = await response.blob()
          this.descargarImagen(blob)
        } else {
          throw new Error('Tipo de respuesta no reconocido')
        }

      } catch (error) {
        console.error('Error generando imagen:', error)
        this.mostrarMensaje(`Error al generar la imagen: ${error.message}`, 'error')
      } finally {
        this.cargando = false
      }
    },

    // NUEVO: Convertir HTML a imagen JPG y descargar automáticamente
    async convertirHtmlAImagenJPG(htmlContent) {
      try {
        // Crear un iframe oculto para renderizar el HTML
        const iframe = document.createElement('iframe')
        iframe.style.position = 'absolute'
        iframe.style.left = '-9999px'
        iframe.style.width = '800px'
        iframe.style.height = '1000px'
        iframe.style.border = 'none'
        iframe.style.background = 'white'
        document.body.appendChild(iframe)
        
        // Escribir el HTML en el iframe
        iframe.contentDocument.open()
        iframe.contentDocument.write(htmlContent)
        iframe.contentDocument.close()
        
        // Esperar a que se cargue completamente
        await new Promise(resolve => {
          iframe.onload = resolve
          setTimeout(resolve, 2000) // Timeout de seguridad
        })
        
        // Verificar si html2canvas está disponible
        if (window.html2canvas) {
          console.log('📷 Usando html2canvas para generar JPG...')
          
          const canvas = await html2canvas(iframe.contentDocument.body, {
            backgroundColor: '#ffffff',
            scale: 2, // Mayor calidad
            useCORS: true,
            allowTaint: true,
            width: 800,
            height: 1000
          })
          
          // Convertir canvas a blob JPG
          canvas.toBlob((blob) => {
            this.descargarImagenJPG(blob)
            document.body.removeChild(iframe)
          }, 'image/jpeg', 0.9) // Calidad 90%
          
        } else {
          console.log('📷 html2canvas no disponible, usando método alternativo...')
          
          // Método alternativo: crear imagen desde el DOM
          await this.crearImagenDesdeDOM(iframe.contentDocument.body)
          document.body.removeChild(iframe)
        }
        
      } catch (error) {
        console.error('Error convirtiendo HTML a JPG:', error)
        this.mostrarMensaje('Error al convertir la boleta a imagen JPG', 'error')
      }
    },

    // NUEVO: Crear imagen desde DOM (método alternativo)
    async crearImagenDesdeDOM(elemento) {
      try {
        // Crear un canvas manualmente
        const canvas = document.createElement('canvas')
        const ctx = canvas.getContext('2d')
        
        // Configurar tamaño del canvas
        canvas.width = 800
        canvas.height = 1000
        
        // Fondo blanco
        ctx.fillStyle = '#ffffff'
        ctx.fillRect(0, 0, canvas.width, canvas.height)
        
        // Crear imagen SVG del contenido
        const svgData = `
          <svg xmlns="http://www.w3.org/2000/svg" width="800" height="1000">
            <foreignObject width="100%" height="100%">
              <div xmlns="http://www.w3.org/1999/xhtml" style="font-family: Arial, sans-serif; padding: 20px;">
                ${elemento.innerHTML}
              </div>
            </foreignObject>
          </svg>
        `
        
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' })
        const svgUrl = URL.createObjectURL(svgBlob)
        
        const img = new Image()
        img.onload = () => {
          ctx.drawImage(img, 0, 0)
          
          // Convertir a JPG
          canvas.toBlob((blob) => {
            this.descargarImagenJPG(blob)
            URL.revokeObjectURL(svgUrl)
          }, 'image/jpeg', 0.9)
        }
        
        img.onerror = () => {
          console.error('Error cargando imagen SVG')
          this.mostrarMensaje('Error al procesar la imagen', 'error')
          URL.revokeObjectURL(svgUrl)
        }
        
        img.src = svgUrl
        
      } catch (error) {
        console.error('Error en método alternativo:', error)
        this.mostrarMensaje('Error al crear imagen desde DOM', 'error')
      }
    },

    // NUEVO: Descargar imagen JPG con nombre personalizado
    descargarImagenJPG(blob) {
      try {
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        
        // Crear nombre de archivo con datos del cliente y fecha
        const fecha = new Date()
        const fechaStr = `${fecha.getFullYear()}${String(fecha.getMonth()+1).padStart(2, '0')}${String(fecha.getDate()).padStart(2, '0')}`
        const horaStr = `${String(fecha.getHours()).padStart(2, '0')}${String(fecha.getMinutes()).padStart(2, '0')}`
        
        const nombreCliente = this.datosViaje.nombreCliente 
          ? this.datosViaje.nombreCliente.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 20)
          : 'Cliente'
        
        const nombreArchivo = `Boleta_${nombreCliente}_${fechaStr}_${horaStr}.jpg`
        
        link.download = nombreArchivo
        link.style.display = 'none'
        
        // Agregar al DOM, hacer clic y remover
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        
        // Limpiar URL
        setTimeout(() => {
          window.URL.revokeObjectURL(url)
        }, 1000)
        
        this.mostrarMensaje(`Imagen JPG descargada: ${nombreArchivo}`, 'success')
        
        console.log('✅ Imagen JPG descargada exitosamente:', nombreArchivo)
        
      } catch (error) {
        console.error('Error descargando imagen JPG:', error)
        this.mostrarMensaje('Error al descargar la imagen JPG', 'error')
      }
    },

    // NUEVO: Procesar imagen base64
    procesarImagenBase64(base64Data) {
      try {
        const byteCharacters = atob(base64Data)
        const byteNumbers = new Array(byteCharacters.length)
        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i)
        }
        const byteArray = new Uint8Array(byteNumbers)
        const blob = new Blob([byteArray], { type: 'image/jpeg' })
        
        this.descargarImagen(blob)
      } catch (error) {
        console.error('Error procesando imagen base64:', error)
        this.mostrarMensaje('Error al procesar la imagen generada', 'error')
      }
    },

    // NUEVO: Convertir HTML a imagen usando canvas
    async convertirHtmlAImagen(htmlContent) {
      try {
        // Crear un iframe temporal para renderizar el HTML
        const iframe = document.createElement('iframe')
        iframe.style.position = 'absolute'
        iframe.style.left = '-9999px'
        iframe.style.width = '800px'
        iframe.style.height = '600px'
        document.body.appendChild(iframe)
        
        // Escribir el HTML en el iframe
        iframe.contentDocument.write(htmlContent)
        iframe.contentDocument.close()
        
        // Esperar a que se cargue
        await new Promise(resolve => {
          iframe.onload = resolve
          setTimeout(resolve, 1000) // Timeout de seguridad
        })
        
        // Intentar usar html2canvas si está disponible
        if (window.html2canvas) {
          const canvas = await html2canvas(iframe.contentDocument.body)
          canvas.toBlob((blob) => {
            this.descargarImagen(blob)
            document.body.removeChild(iframe)
          }, 'image/jpeg', 0.9)
        } else {
          // Si no hay html2canvas, mostrar el HTML en una nueva ventana
          const newWindow = window.open('', '_blank', 'width=800,height=600')
          newWindow.document.write(htmlContent)
          newWindow.document.close()
          this.mostrarMensaje('Boleta abierta en nueva ventana. Use Ctrl+P para imprimir.', 'info')
          document.body.removeChild(iframe)
        }
        
      } catch (error) {
        console.error('Error convirtiendo HTML a imagen:', error)
        this.mostrarMensaje('Error al convertir la boleta a imagen', 'error')
      }
    },

    // NUEVO: Método auxiliar para descargar imagen
    descargarImagen(blob) {
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      
      const fecha = new Date()
      const nombreArchivo = `pasaje-${fecha.getFullYear()}${String(fecha.getMonth()+1).padStart(2, '0')}${String(fecha.getDate()).padStart(2, '0')}-${String(fecha.getHours()).padStart(2, '0')}${String(fecha.getMinutes()).padStart(2, '0')}.jpg`
      
      link.download = nombreArchivo
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
      
      this.mostrarMensaje('Imagen generada y descargada exitosamente', 'success')
    },

    // NUEVO: Preparar datos para la imagen - CORREGIDO PARA PAGO MIXTO
    prepararDatosParaImagen() {
      const ahora = new Date()
      const fechaActual = this.formatearFecha(ahora)
      const horaActual = this.formatearHora(ahora)
      const primerDetalle = this.detallesPasaje[0]
      
      // CORREGIDO: Determinar medio de pago para imagen
      let medioPagoImagen = 'efectivo'
      let esPagoMixto = false
      let detallesPago = ''
      
      console.log('💰 Preparando datos de pago:', {
        pagoMixto: this.datosViaje.pagoMixto,
        medioPago: this.datosViaje.medioPago,
        detallesPago: this.datosViaje.detallesPago
      })
      
      if (this.datosViaje.pagoMixto === true) {
        medioPagoImagen = 'Pago Mixto'
        esPagoMixto = true
        detallesPago = this.datosViaje.detallesPago || 'Pago Mixto'
      } else if (this.datosViaje.medioPago) {
        medioPagoImagen = this.getPaymentLabel(this.datosViaje.medioPago)
        detallesPago = this.datosViaje.detallesPago || `${medioPagoImagen}: S/ ${this.totalGeneral.toFixed(2)}`
      }
      
      const datosParaImagen = {
        // Datos de emisión
        fecha_emision: fechaActual,
        hora_emision: horaActual,
        numero_boleta: this.generarNumeroBoleta(),
        operador: 'ROCÍO TRAVEL',
        
        // Datos del cliente
        cliente: {
          nombre: this.datosViaje.nombreCliente || 'CLIENTE',
          documento: this.datosViaje.documentoCliente || '',
          contacto: this.datosViaje.contactoCliente || '',
          nacionalidad: this.datosViaje.nacionalidadCliente || 'PERUANA'
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
        
        // CORREGIDO: Datos de pago
        medio_pago: medioPagoImagen,
        pago_mixto: esPagoMixto,
        detalles_pago: detallesPago,
        
        // Datos adicionales
        nota: this.datosViaje.nota,
        destino: primerDetalle.destino || '',
        ruta: primerDetalle.ruta || primerDetalle.descripcion,
        
        // Para la vista blade
        tipo_documento: 'BOLETA DE VENTA',
        fecha_viaje: fechaActual
      }
      
      console.log('📋 Datos preparados para imagen:', datosParaImagen)
      return datosParaImagen
    },

    // NUEVO: Generar número de boleta
    generarNumeroBoleta() {
      const ahora = new Date()
      const año = ahora.getFullYear().toString().slice(-2)
      const mes = String(ahora.getMonth() + 1).padStart(2, '0')
      const dia = String(ahora.getDate()).padStart(2, '0')
      const hora = String(ahora.getHours()).padStart(2, '0')
      const minuto = String(ahora.getMinutes()).padStart(2, '0')
      const random = Math.floor(Math.random() * 999).toString().padStart(3, '0')
      
      return `BOL-${año}${mes}${dia}-${hora}${minuto}${random}`
    },

    // Imprimir Ticket de Venta - CORREGIDO PARA PAGO MIXTO
    async imprimirTicketVenta() {
      console.log('🎫 Iniciando generación de ticket de venta...')
      
      if (!this.puedeGenerarImagen) {
        this.mostrarMensaje('Complete todos los campos obligatorios para imprimir el ticket', 'warning')
        return
      }

      if (this.detallesPasaje.length === 0) {
        this.mostrarMensaje('No hay detalles de pasaje para generar el ticket', 'error')
        return
      }

      try {
        this.cargando = true
        this.mensajeCarga = 'Generando ticket de venta...'
        
        const datosCompletos = this.prepararDatosParaTicket()
        
        console.log('🎫 Datos preparados para ticket:', JSON.stringify(datosCompletos, null, 2))

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
          const ticketWindow = window.open('', '_blank', 'width=400,height=600,scrollbars=yes,resizable=yes')
          
          if (ticketWindow) {
            ticketWindow.document.write(htmlContent)
            ticketWindow.document.close()
            ticketWindow.focus()
            this.mostrarMensaje('Ticket de venta generado exitosamente', 'success')
          } else {
            throw new Error('No se pudo abrir la ventana del ticket.')
          }
        } else {
          const errorText = await response.text()
          console.error('Error del servidor:', errorText)
          throw new Error(`Error ${response.status}: ${response.statusText}`)
        }

      } catch (error) {
        console.error('❌ Error generando ticket:', error)
        this.mostrarMensaje(`Error al generar el ticket: ${error.message}`, 'error')
      } finally {
        this.cargando = false
      }
    },

    // Preparar datos para el ticket de venta - CORREGIDO
    prepararDatosParaTicket() {
      const ahora = new Date()
      const fechaActual = this.formatearFecha(ahora)
      const horaActual = this.formatearHoraTicket(ahora)
      const primerDetalle = this.detallesPasaje[0]
      
      if (!primerDetalle) {
        throw new Error('No hay detalles de pasaje disponibles')
      }
      
      // Determinar medio de pago para el ticket
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
        fecha_emision: fechaActual,
        hora_emision: horaActual,
        
        cliente: {
          nombre: this.datosViaje.nombreCliente || 'CLIENTE',
          documento: this.datosViaje.documentoCliente || 'N/A',
          contacto: this.datosViaje.contactoCliente || 'N/A',
          nacionalidad: this.datosViaje.nacionalidadCliente || 'PERUANA'
        },
        
        nombre_cliente: this.datosViaje.nombreCliente || 'CLIENTE',
        documento_cliente: this.datosViaje.documentoCliente || 'N/A',
        contacto_cliente: this.datosViaje.contactoCliente || 'N/A',
        nacionalidad_cliente: this.datosViaje.nacionalidadCliente || 'PERUANA',
        
        cantidad: parseInt(primerDetalle.cantidad) || 1,
        descripcion: primerDetalle.descripcion || 'Pasaje',
        precio_unitario: parseFloat(primerDetalle.precio_unitario || 0).toFixed(2),
        subtotal: parseFloat(primerDetalle.subtotal || 0).toFixed(2),
        total: this.totalGeneral.toFixed(2),
        
        fecha_viaje: fechaActual,
        embarcacion: this.datosViaje.embarcacion || 'N/A',
        puerto_embarque: this.datosViaje.puertoEmbarque || 'N/A',
        hora_embarque: this.datosViaje.horaEmbarque || 'N/A',
        hora_salida: this.datosViaje.horaSalida || 'N/A',
        
        medio_pago: medioPagoTicket,
        pago_mixto: esPagoMixto,
        detalles_pago: detallesPago,
        
        nota: this.datosViaje.nota || '',
        destino: primerDetalle.destino || primerDetalle.descripcion || 'N/A',
        ruta: primerDetalle.ruta || primerDetalle.descripcion || 'N/A',
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

    generarNumeroTicket() {
      const ahora = new Date()
      const año = ahora.getFullYear().toString().slice(-2)
      const mes = String(ahora.getMonth() + 1).padStart(2, '0')
      const dia = String(ahora.getDate()).padStart(2, '0')
      const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0')
      
      return `NV-${año}${mes}${dia}${random}`
    },

    formatearHoraTicket(fecha) {
      const horas = String(fecha.getHours()).padStart(2, '0')
      const minutos = String(fecha.getMinutes()).padStart(2, '0')
      const segundos = String(fecha.getSeconds()).padStart(2, '0')
      
      return `${horas}:${minutos}:${segundos}`
    },

    // Cargar datos desde el formulario anterior
    cargarDatosDesdeFormulario() {
      console.log('🔍 Cargando datos desde el formulario anterior...')
      
      const queryParams = this.$route.query
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
      
      const datosPasaje = Object.keys(queryParams).length > 0 ? queryParams : datosParsed
      
      console.log('📋 Datos del pasaje recibidos:', datosPasaje)

      if (datosPasaje && (datosPasaje.cantidad || datosPasaje.descripcion)) {
        const detalle = {
          cantidad: parseInt(datosPasaje.cantidad) || 1,
          descripcion: datosPasaje.descripcion || datosPasaje.ruta || '',
          precio_unitario: parseFloat(datosPasaje.precio_unitario) || 0,
          subtotal: parseFloat(datosPasaje.subtotal) || 0,
          destino: datosPasaje.destino || '',
          ruta: datosPasaje.ruta || ''
        }

        console.log('✅ Detalle creado:', detalle)
        this.detallesPasaje = [detalle]
        
        this.mostrarMensaje('Datos cargados desde el formulario anterior', 'success')
        this.validarImagen()
      } else {
        console.log('⚠️ No se encontraron datos del pasaje')
        this.mostrarMensaje('No se encontraron datos del formulario anterior', 'warning')
      }
    },

    // Guardar todo - CORREGIDO PARA PAGO MIXTO
    async guardarTodo() {
      console.log('💾 Iniciando guardado completo...')
      
      if (!this.validarFormularioCompleto()) {
        return
      }

      this.cargando = true
      this.mensajeCarga = 'Guardando datos...'
      try {
        await this.enviarTodosLosDatos()
        this.mostrarMensaje('Todos los datos guardados correctamente', 'success')
        
        setTimeout(() => {
          // this.$router.push('/') // Opcional
        }, 2000)
      } catch (error) {
        console.error('Error al guardar todo:', error)
        this.mostrarMensaje('Error al guardar todos los datos', 'error')
      } finally {
        this.cargando = false
      }
    },

    // Validar formulario completo - CORREGIDO PARA PAGO MIXTO
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

      if (!this.datosViaje.horaEmbarque) {
        this.mostrarMensaje('Debe seleccionar una hora de embarque', 'warning')
        return false
      }

      if (!this.datosViaje.horaSalida) {
        this.mostrarMensaje('Debe seleccionar una hora de salida', 'warning')
        return false
      }

      // Validar medio de pago
      if (this.datosViaje.pagoMixto) {
        if (!this.datosViaje.detallesPago || this.datosViaje.detallesPago.trim() === '') {
          this.mostrarMensaje('Debe configurar el pago mixto correctamente', 'warning')
          return false
        }
        if (!(this.pagoMixtoDetalle.efectivo > 0 || this.pagoMixtoDetalle.yape > 0 || this.pagoMixtoDetalle.plin > 0)) {
          this.mostrarMensaje('Debe configurar al menos un método de pago en el pago mixto', 'warning')
          return false
        }
      } else {
        if (!this.datosViaje.medioPago || this.datosViaje.medioPago.trim() === '') {
          this.mostrarMensaje('Debe seleccionar un medio de pago', 'warning')
          return false
        }
      }
      
      return true
    },

    // Enviar todos los datos - CORREGIDO PARA PAGO MIXTO
    async enviarTodosLosDatos() {
      const primerDetalle = this.detallesPasaje[0]
      
      // Determinar medio de pago para guardar
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

      console.log('📤 Enviando datos completos al PasajeController:', datosCompletos)

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

    // NUEVO: Refrescar listas periódicamente
    refresharListas() {
      this.cargarEmbarcacionesYPuertos()
    },

    // Métodos de mensajes
    mostrarMensaje(texto, tipo = 'info') {
      this.mensaje = texto
      this.tipoMensaje = tipo
      
      setTimeout(() => {
        this.cerrarMensaje()
      }, 5000)
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
    },
    
    // NUEVO: Watch para listas - forzar reactividad
    embarcaciones: {
      handler(newVal) {
        console.log('Embarcaciones actualizadas:', newVal.length)
      },
      immediate: true
    },
    
    puertosEmbarque: {
      handler(newVal) {
        console.log('Puertos actualizados:', newVal.length)
      },
      immediate: true
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

/* Botón Imagen (anteriormente PDF) */
.btn-imagen {
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
  background: linear-gradient(135deg, #17a2b8, #138496);
  color: white;
}

.btn-imagen:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(23,162,184,0.3);
}

.btn-imagen:disabled {
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
  
  .btn-imagen, .btn-ticket, .btn-secondary {
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