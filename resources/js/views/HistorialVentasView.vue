<template>
  <div class="historial-container">
    <!-- Header -->
    <div class="header">
      <h2>Ver nota de venta - Rocío Travel</h2>
      <div class="stats">
        <div class="stat">
          <i class="fas fa-ticket-alt"></i>
          <div class="stat-content">
            <span>{{ totalPasajes }}</span>
            <small>TOTAL PASAJES</small>
          </div>
        </div>
        <div class="stat">
          <i class="fas fa-dollar-sign"></i>
          <div class="stat-content">
            <span>S/ {{ totalVendido.toFixed(2) }}</span>
            <small>TOTAL VENDIDO</small>
          </div>
        </div>
        <div class="stat ganancia">
          <i class="fas fa-hand-holding-usd"></i>
          <div class="stat-content">
            <span>S/ {{ totalGanancia.toFixed(2) }}</span>
            <small>MI GANANCIA</small>
          </div>
        </div>
      </div>

      <!-- Acumuladores de medios de pago -->
      <div class="pagos-stats">
        <div class="pago-item efectivo">
          <i class="fas fa-money-bill-wave"></i>
          <div class="pago-content">
            <span>S/ {{ totalEfectivoNeto.toFixed(2) }}</span>
            <small>EFECTIVO (PRECIO ORIGINAL)</small>
          </div>
        </div>
        <div class="pago-item yape">
          <i class="fas fa-mobile-alt"></i>
          <div class="pago-content">
            <span>S/ {{ totalYapeNeto.toFixed(2) }}</span>
            <small>YAPE (PRECIO ORIGINAL)</small>
          </div>
        </div>
        <div class="pago-item plin">
          <i class="fas fa-mobile-alt"></i>
          <div class="pago-content">
            <span>S/ {{ totalPlinNeto.toFixed(2) }}</span>
            <small>PLIN (PRECIO ORIGINAL)</small>
          </div>
        </div>
        <div class="pago-item mixto">
          <i class="fas fa-coins"></i>
          <div class="pago-content">
            <span>S/ {{ totalMixtoNeto.toFixed(2) }}</span>
            <small>PAGO MIXTO (PRECIO ORIGINAL)</small>
          </div>
        </div>
        <button @click="exportarCSV" class="btn-export" :disabled="ventas.length === 0">
          <i class="fas fa-download"></i>
          Exportar Excel
        </button>
      </div>
    </div>

    <!-- Filtros simples -->
    <div class="filtros">
      <div class="filtro-row">
        <div class="campo">
          <label>Fecha inicio</label>
          <input type="date" v-model="filtros.fechaInicio" @change="buscarAutomatico" />
        </div>
        <div class="campo">
          <label>Fecha fin</label>
          <input type="date" v-model="filtros.fechaFin" @change="buscarAutomatico" />
        </div>
        <button @click="limpiarFiltros" class="btn-limpiar">×</button>
        <button @click="buscar" class="btn-buscar" :disabled="cargando">
          <i class="fas fa-search" :class="{ spinning: cargando }"></i>
          Buscar
        </button>
      </div>
      
      <div class="filtro-row">
        <div class="campo">
          <label>Núm. de Doc</label>
          <input type="text" v-model="filtros.documento" placeholder="DNI" @input="buscarAutomatico" @keyup.enter="buscar" />
        </div>
        <div class="campo">
          <label>Nombre:</label>
          <input type="text" v-model="filtros.nombre" placeholder="Cliente" @input="buscarAutomatico" @keyup.enter="buscar" />
        </div>
        <div class="campo">
          <label>Estado:</label>
          <select v-model="filtros.estado" @change="buscarAutomatico">
            <option value="">Todos los estados</option>
            <option value="activo">Activo</option>
            <option value="anulado">Anulado</option>
          </select>
        </div>
        <button @click="cargarDatos" class="btn-refresh" :disabled="cargando">
          <i class="fas fa-sync-alt" :class="{ spinning: cargando }"></i>
          Actualizar
        </button>
      </div>
    </div>

    <!-- Tabla -->
    <div class="tabla-container">
      <div v-if="cargando" class="loading">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Cargando ventas...</p>
      </div>

      <table class="tabla">
        <thead>
          <tr>
            <th @click="ordenar('created_at')">Fecha</th>
            <th @click="ordenar('nombre_cliente')">Cliente</th>
            <th>DNI</th>
            <th>Cantidad</th>
            <th>Medio de Pago</th>
            <th @click="ordenar('subtotal')">Total Vendido</th>
            <th>Mi Ganancia</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="ventasFiltradas.length === 0 && !cargando">
            <td colspan="9" class="sin-datos">
              <i class="fas fa-search"></i>
              <p>No se encontraron registros</p>
              <small>Intenta ajustar los filtros de búsqueda</small>
            </td>
          </tr>
          
          <tr v-for="venta in ventasPaginadas" 
              :key="venta.id" 
              @click="seleccionar(venta)"
              :class="{ 
                'seleccionada': ventaSeleccionada?.id === venta.id,
                'anulada': venta.estado === 'anulado' 
              }">
            <td>
              <div class="fecha">
                <span>{{ fecha(venta.created_at) }}</span>
                <small>{{ hora(venta.created_at) }}</small>
              </div>
            </td>
            <td>
              <div class="cliente">
                <span>{{ venta.nombre_cliente || 'N/A' }}</span>
                <small v-if="venta.nacionalidad_cliente !== 'PERUANA'">
                  {{ venta.nacionalidad_cliente }}
                </small>
              </div>
            </td>
            <td>{{ venta.documento_cliente || 'N/A' }}</td>
            <td><strong>{{ venta.cantidad || 1 }}</strong></td>
            <td>
              <span class="badge-pago" :class="venta.medio_pago || 'efectivo'">
                {{ textoPago(venta.medio_pago) }}
              </span>
            </td>
            <td class="monto">S/ {{ parseFloat(venta.subtotal || 0).toFixed(2) }}</td>
            <td class="ganancia-monto">S/ {{ calcularGananciaVenta(venta).toFixed(2) }}</td>
            <td>
              <span class="badge-estado" :class="venta.estado || 'activo'">
                {{ textoEstado(venta.estado) }}
              </span>
            </td>
            <td>
              <div class="acciones">
                <button @click.stop="verDetalle(venta)" class="btn-accion info">
                  <i class="fas fa-info-circle"></i>
                </button>
                <button @click.stop="visualizarTicket(venta)" 
                        class="btn-accion view-ticket" 
                        :disabled="venta.estado === 'anulado'"
                        title="Visualizar Ticket">
                  <i class="fas fa-receipt"></i>
                </button>
                <button @click.stop="confirmarAnular(venta)" 
                        class="btn-accion cancel" 
                        :disabled="venta.estado === 'anulado'">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Paginación -->
      <div v-if="totalPaginas > 1" class="paginacion">
        <button @click="pagina--" :disabled="pagina <= 1">‹</button>
        <span>{{ pagina }} / {{ totalPaginas }}</span>
        <button @click="pagina++" :disabled="pagina >= totalPaginas">›</button>
      </div>
    </div>

    <!-- Modal detalle -->
    <div v-if="mostrarDetalle" class="modal" @click="cerrarDetalle">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Detalle del Pasaje #{{ ventaSeleccionada?.id }}</h3>
          <button @click="cerrarDetalle">×</button>
        </div>
        <div class="modal-body" v-if="ventaSeleccionada">
          <div class="detalle-grid">
            <div><strong>Cliente:</strong> {{ ventaSeleccionada.nombre_cliente }}</div>
            <div><strong>Documento:</strong> {{ ventaSeleccionada.documento_cliente }}</div>
            <div><strong>Contacto:</strong> {{ ventaSeleccionada.contacto_cliente || 'N/A' }}</div>
            <div><strong>Cantidad:</strong> {{ ventaSeleccionada.cantidad || 1 }} persona(s)</div>
            <div><strong>Descripción:</strong> {{ ventaSeleccionada.descripcion || 'N/A' }}</div>
            <div><strong>Embarcación:</strong> {{ ventaSeleccionada.embarcacion || 'N/A' }}</div>
            <div><strong>Puerto:</strong> {{ ventaSeleccionada.puerto_embarque || 'N/A' }}</div>
            <div><strong>Hora embarque:</strong> {{ ventaSeleccionada.hora_embarque || 'N/A' }}</div>
            <div><strong>Total Vendido:</strong> <span class="total">S/ {{ parseFloat(ventaSeleccionada.subtotal || 0).toFixed(2) }}</span></div>
            <div><strong>Mi Ganancia:</strong> <span class="ganancia-detalle">S/ {{ calcularGananciaVenta(ventaSeleccionada).toFixed(2) }}</span></div>
            <div><strong>Estado:</strong> 
              <span class="badge-estado" :class="ventaSeleccionada.estado || 'activo'">
                {{ textoEstado(ventaSeleccionada.estado) }}
              </span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="cerrarDetalle" class="btn-secondary">Cerrar</button>
          <button v-if="ventaSeleccionada?.estado !== 'anulado'" 
                  @click="imprimir(ventaSeleccionada)" 
                  class="btn-primary">
            Imprimir Ticket
          </button>
        </div>
      </div>
    </div>

    <!-- Modal confirmar anulación -->
    <div v-if="mostrarConfirmacion" class="modal" @click="cerrarConfirmacion">
      <div class="modal-content confirmacion" @click.stop>
        <div class="modal-header">
          <h3>Confirmar Anulación</h3>
          <button @click="cerrarConfirmacion">×</button>
        </div>
        <div class="modal-body">
          <div class="confirmacion-content">
            <i class="fas fa-exclamation-triangle"></i>
            <h4>¿Estás seguro de anular este pasaje?</h4>
            <p>Cliente: {{ ventaParaAnular?.nombre_cliente }}</p>
            <p>Documento: {{ ventaParaAnular?.documento_cliente }}</p>
            <p>Cantidad: {{ ventaParaAnular?.cantidad || 1 }} pasaje(s)</p>
            <p>Total: S/ {{ parseFloat(ventaParaAnular?.subtotal || 0).toFixed(2) }}</p>
            <small>Esta acción no se puede deshacer</small>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="cerrarConfirmacion" class="btn-secondary">Cancelar</button>
          <button @click="anularVenta" class="btn-danger" :disabled="anulando">
            <i v-if="anulando" class="fas fa-spinner fa-spin"></i>
            {{ anulando ? 'Anulando...' : 'Anular Pasaje' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Notificación -->
    <div v-if="notificacion.show" :class="['notif', notificacion.tipo]">
      {{ notificacion.mensaje }}
      <button @click="notificacion.show = false">×</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HistorialVentasView',
  data() {
    return {
      cargando: false,
      ventas: [],
      ventasOriginales: [],
      ventaSeleccionada: null,
      mostrarDetalle: false,
      mostrarConfirmacion: false,
      ventaParaAnular: null,
      anulando: false,
      pagina: 1,
      porPagina: 15,
      gananciaPorPasaje: 10, // Ganancia fija de S/ 10 por pasaje
      
      filtros: {
        fechaInicio: '',
        fechaFin: '',
        documento: '',
        nombre: '',
        estado: ''
      },
      
      notificacion: {
        show: false,
        mensaje: '',
        tipo: 'success'
      }
    }
  },

  computed: {
    ventasFiltradas() {
      let resultados = [...this.ventasOriginales]
      
      if (this.filtros.fechaInicio) {
        resultados = resultados.filter(v => 
          new Date(v.created_at) >= new Date(this.filtros.fechaInicio)
        )
      }
      
      if (this.filtros.fechaFin) {
        resultados = resultados.filter(v => 
          new Date(v.created_at) <= new Date(this.filtros.fechaFin + 'T23:59:59')
        )
      }
      
      if (this.filtros.documento) {
        const doc = this.filtros.documento.toLowerCase()
        resultados = resultados.filter(v => 
          (v.documento_cliente || '').toLowerCase().includes(doc)
        )
      }
      
      if (this.filtros.nombre) {
        const nombre = this.filtros.nombre.toLowerCase()
        resultados = resultados.filter(v => 
          (v.nombre_cliente || '').toLowerCase().includes(nombre)
        )
      }
      
      if (this.filtros.estado) {
        resultados = resultados.filter(v => 
          (v.estado || 'activo') === this.filtros.estado
        )
      }
      
      return resultados
    },

    // Total de pasajes vendidos (suma de cantidades)
    totalPasajes() {
      return this.ventasFiltradas
        .filter(v => v.estado !== 'anulado')
        .reduce((total, v) => total + parseInt(v.cantidad || 1), 0)
    },

    // Total vendido (lo que cobraste a los clientes)
    totalVendido() {
      return this.ventasFiltradas
        .filter(v => v.estado !== 'anulado')
        .reduce((total, v) => total + parseFloat(v.subtotal || 0), 0)
    },

    // Tu ganancia total (cantidad de pasajes × S/ 10)
    totalGanancia() {
      return this.ventasFiltradas
        .filter(v => v.estado !== 'anulado')
        .reduce((total, v) => total + this.calcularGananciaVenta(v), 0)
    },

    // Acumuladores por medio de pago - Muestran solo el PRECIO ORIGINAL (sin tu ganancia)
    // Fórmula: Total cobrado - (cantidad de pasajes × S/ 10)
    totalEfectivoNeto() {
      return this.ventasFiltradas
        .filter(v => v.estado !== 'anulado' && (v.medio_pago === 'efectivo' || !v.medio_pago))
        .reduce((total, v) => {
          const totalCobrado = parseFloat(v.subtotal || 0)
          const ganancia = this.calcularGananciaVenta(v)
          return total + (totalCobrado - ganancia)
        }, 0)
    },

    totalYapeNeto() {
      return this.ventasFiltradas
        .filter(v => v.estado !== 'anulado' && v.medio_pago === 'yape')
        .reduce((total, v) => {
          const totalCobrado = parseFloat(v.subtotal || 0)
          const ganancia = this.calcularGananciaVenta(v)
          return total + (totalCobrado - ganancia)
        }, 0)
    },

    totalPlinNeto() {
      return this.ventasFiltradas
        .filter(v => v.estado !== 'anulado' && v.medio_pago === 'plin')
        .reduce((total, v) => {
          const totalCobrado = parseFloat(v.subtotal || 0)
          const ganancia = this.calcularGananciaVenta(v)
          return total + (totalCobrado - ganancia)
        }, 0)
    },

    totalMixtoNeto() {
      return this.ventasFiltradas
        .filter(v => v.estado !== 'anulado' && v.medio_pago === 'mixto')
        .reduce((total, v) => {
          const totalCobrado = parseFloat(v.subtotal || 0)
          const ganancia = this.calcularGananciaVenta(v)
          return total + (totalCobrado - ganancia)
        }, 0)
    },

    totalPaginas() {
      return Math.ceil(this.ventasFiltradas.length / this.porPagina)
    },

    ventasPaginadas() {
      const inicio = (this.pagina - 1) * this.porPagina
      return this.ventasFiltradas.slice(inicio, inicio + this.porPagina)
    }
  },

  watch: {
    ventasFiltradas() {
      this.pagina = 1
    }
  },

  mounted() {
    this.cargarDatos()
  },

  methods: {
    // Calcular ganancia de una venta específica
    calcularGananciaVenta(venta) {
      const cantidad = parseInt(venta.cantidad || 1)
      return this.gananciaPorPasaje * cantidad
    },

    async cargarDatos() {
      if (this.cargando) return
      
      try {
        this.cargando = true
        
        const response = await fetch('/api/historial-ventas', {
          headers: { 'Accept': 'application/json' }
        })
        
        if (!response.ok) {
          throw new Error(`Error ${response.status}`)
        }
        
        const data = await response.json()
        this.ventasOriginales = data.data || data || []
        this.ventas = [...this.ventasOriginales]
        this.pagina = 1
        
        this.mostrarNotif(`${this.ventasOriginales.length} registros cargados`, 'success')
        
      } catch (error) {
        console.error('Error:', error)
        this.mostrarNotif('Error al cargar datos', 'error')
        this.ventasOriginales = []
        this.ventas = []
      } finally {
        this.cargando = false
      }
    },

    buscarAutomatico() {
      this.pagina = 1
    },

    buscar() {
      this.pagina = 1
      const total = this.ventasFiltradas.length
      this.mostrarNotif(`${total} registros encontrados`, 'info')
    },

    limpiarFiltros() {
      this.filtros = {
        fechaInicio: '',
        fechaFin: '',
        documento: '',
        nombre: '',
        estado: ''
      }
      this.pagina = 1
    },

    ordenar(campo) {
      this.ventasOriginales.sort((a, b) => {
        let valA = a[campo] || ''
        let valB = b[campo] || ''
        
        if (campo === 'created_at') {
          valA = new Date(valA)
          valB = new Date(valB)
        } else if (campo === 'subtotal') {
          valA = parseFloat(valA) || 0
          valB = parseFloat(valB) || 0
        }
        
        return valA > valB ? -1 : 1
      })
    },

    seleccionar(venta) {
      this.ventaSeleccionada = venta
    },

    verDetalle(venta) {
      this.ventaSeleccionada = venta
      this.mostrarDetalle = true
    },

    cerrarDetalle() {
      this.mostrarDetalle = false
      this.ventaSeleccionada = null
    },

    confirmarAnular(venta) {
      if (venta.estado === 'anulado') return
      this.ventaParaAnular = venta
      this.mostrarConfirmacion = true
    },

    cerrarConfirmacion() {
      this.mostrarConfirmacion = false
      this.ventaParaAnular = null
      this.anulando = false
    },

    async anularVenta() {
      if (!this.ventaParaAnular || this.anulando) return
      
      try {
        this.anulando = true
        
        const response = await fetch(`/api/historial-ventas/${this.ventaParaAnular.id}/anular`, {
          method: 'PUT',
          headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify({ 
            motivo_anulacion: 'Anulado desde historial de ventas por usuario'
          })
        })
        
        const data = await response.json()
        
        if (!response.ok) {
          throw new Error(data.message || `Error ${response.status}`)
        }
        
        const index = this.ventasOriginales.findIndex(v => v.id === this.ventaParaAnular.id)
        if (index !== -1) {
          this.ventasOriginales[index] = data.data || {
            ...this.ventasOriginales[index],
            estado: 'anulado',
            motivo_anulacion: 'Anulado desde historial de ventas por usuario'
          }
        }
        
        this.mostrarNotif(data.message || 'Pasaje anulado correctamente', 'success')
        this.cerrarConfirmacion()
        
      } catch (error) {
        console.error('Error:', error)
        this.mostrarNotif(error.message || 'Error al anular el pasaje', 'error')
      } finally {
        this.anulando = false
      }
    },

    async visualizarTicket(venta) {
      if (venta.estado === 'anulado') {
        this.mostrarNotif('No se puede visualizar el ticket de una venta anulada', 'warning')
        return
      }
      
      try {
        const response = await fetch(`/api/historial-ventas/${venta.id}/generar-ticket`, {
          method: 'POST',
          headers: { 
            'Content-Type': 'application/json',
            'Accept': 'text/html'
          }
        })
        
        if (response.ok) {
          const html = await response.text()
          
          const ventana = window.open('', '_blank', 'width=400,height=600,scrollbars=yes,resizable=yes')
          ventana.document.write(html)
          ventana.document.close()
          ventana.focus()
          
          this.mostrarNotif('Ticket visualizado correctamente', 'success')
        } else {
          throw new Error('Error al generar el ticket')
        }
      } catch (error) {
        console.error('Error:', error)
        this.mostrarNotif('Error al visualizar el ticket', 'error')
      }
    },

    imprimir(venta) {
      this.visualizarTicket(venta)
    },

    exportarCSV() {
      if (this.ventasFiltradas.length === 0) return
      
      // Encabezado del resumen
      const resumen = [
        'RESUMEN DE VENTAS - ROCIO TRAVEL',
        '',
        `Total Pasajes:,${this.totalPasajes}`,
        `Total Vendido:,S/ ${this.totalVendido.toFixed(2)}`,
        `Mi Ganancia:,S/ ${this.totalGanancia.toFixed(2)}`,
        '',
        'DESGLOSE POR MEDIO DE PAGO (PRECIO ORIGINAL)',
        `Efectivo:,S/ ${this.totalEfectivoNeto.toFixed(2)}`,
        `Yape:,S/ ${this.totalYapeNeto.toFixed(2)}`,
        `Plin:,S/ ${this.totalPlinNeto.toFixed(2)}`,
        `Pago Mixto:,S/ ${this.totalMixtoNeto.toFixed(2)}`,
        '',
        'DETALLE DE VENTAS',
        ''
      ]
      
      const headers = ['Fecha', 'Cliente', 'DNI', 'Cantidad', 'Medio Pago', 'Total Vendido', 'Mi Ganancia', 'Estado']
      const filas = this.ventasFiltradas.map(v => [
        this.fecha(v.created_at),
        `"${v.nombre_cliente || 'N/A'}"`,
        v.documento_cliente || 'N/A',
        v.cantidad || 1,
        this.textoPago(v.medio_pago),
        parseFloat(v.subtotal || 0).toFixed(2),
        this.calcularGananciaVenta(v).toFixed(2),
        this.textoEstado(v.estado)
      ].join(','))
      
      const csv = [...resumen, headers.join(','), ...filas].join('\n')
      const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' })
      const url = URL.createObjectURL(blob)
      
      const link = document.createElement('a')
      link.href = url
      link.download = `historial-ventas-${new Date().toISOString().split('T')[0]}.csv`
      link.click()
      
      URL.revokeObjectURL(url)
      this.mostrarNotif('Archivo descargado con resumen completo', 'success')
    },

    fecha(f) {
      if (!f) return 'N/A'
      return new Date(f).toLocaleDateString('es-PE')
    },

    hora(f) {
      if (!f) return 'N/A'
      return new Date(f).toLocaleTimeString('es-PE', { 
        hour: '2-digit', 
        minute: '2-digit' 
      })
    },

    textoEstado(estado) {
      const estados = {
        activo: 'Activo',
        anulado: 'Anulado'
      }
      return estados[estado] || 'Activo'
    },

    textoPago(medio) {
      const medios = {
        efectivo: 'Efectivo',
        yape: 'Yape',
        plin: 'Plin',
        mixto: 'Pago Mixto'
      }
      return medios[medio] || 'Efectivo'
    },

    mostrarNotif(mensaje, tipo = 'success') {
      this.notificacion = { show: true, mensaje, tipo }
      setTimeout(() => { this.notificacion.show = false }, 3000)
    }
  }
}
</script>

<style scoped>
.historial-container {
  padding: 20px;
  background: #f8f9fa;
  min-height: 100vh;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  max-width: 1600px;
  margin: 0 auto;
}

.header {
  text-align: center;
  margin-bottom: 30px;
}

.header h2 {
  margin: 0 0 20px 0;
  color: #333;
  font-size: 28px;
}

.stats {
  display: flex;
  justify-content: center;
  gap: 15px;
  align-items: stretch;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.stat {
  background: white;
  padding: 18px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  min-width: 140px;
  flex: 1;
  max-width: 200px;
}

.stat i {
  font-size: 30px;
  color: #007bff;
}

.stat.ganancia i {
  color: #ffc107;
}

.stat-content {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.stat span {
  font-size: 19px;
  font-weight: 700;
  color: #333;
  line-height: 1.2;
}

.stat small {
  font-size: 10px;
  color: #666;
  text-transform: uppercase;
  margin-top: 4px;
}

.pagos-stats {
  display: flex;
  justify-content: center;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
  margin-top: 15px;
}

.pago-item {
  background: white;
  padding: 15px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 3px 5px rgba(0,0,0,0.08);
  min-width: 150px;
  border-left: 4px solid #007bff;
}

.pago-item.efectivo {
  border-left-color: #28a745;
}

.pago-item.efectivo i {
  color: #28a745;
}

.pago-item.yape {
  border-left-color: #722F37;
}

.pago-item.yape i {
  color: #722F37;
}

.pago-item.plin {
  border-left-color: #007bff;
}

.pago-item.plin i {
  color: #007bff;
}

.pago-item.mixto {
  border-left-color: #6f42c1;
}

.pago-item.mixto i {
  color: #6f42c1;
}

.pago-item i {
  font-size: 24px;
}

.pago-content {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.pago-content span {
  font-size: 16px;
  font-weight: 700;
  color: #333;
  line-height: 1.2;
}

.pago-content small {
  font-size: 10px;
  color: #666;
  text-transform: uppercase;
  margin-top: 3px;
}

.btn-export {
  background: #28a745;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-export:hover:not(:disabled) {
  background: #218838;
}

.btn-export:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.filtros {
  background: white;
  padding: 25px;
  border-radius: 10px;
  margin-bottom: 25px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.filtro-row {
  display: flex;
  justify-content: center;
  gap: 15px;
  align-items: end;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.filtro-row:last-child {
  margin-bottom: 0;
}

.campo {
  flex: 1;
  min-width: 180px;
  max-width: 250px;
}

.campo label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
  color: #555;
  font-size: 14px;
}

.campo input,
.campo select {
  width: 100%;
  padding: 10px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.3s;
}

.campo input:focus,
.campo select:focus {
  border-color: #007bff;
  outline: none;
}

.btn-limpiar {
  background: #dc3545;
  color: white;
  border: none;
  width: 45px;
  height: 42px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 20px;
  font-weight: bold;
  transition: background 0.3s;
}

.btn-limpiar:hover {
  background: #c82333;
}

.btn-buscar,
.btn-refresh {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-buscar:hover:not(:disabled) {
  background: #0056b3;
}

.btn-refresh {
  background: #17a2b8;
}

.btn-refresh:hover:not(:disabled) {
  background: #138496;
}

.btn-buscar:disabled,
.btn-refresh:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.spinning {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.tabla-container {
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  position: relative;
}

.loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255,255,255,0.95);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.loading i {
  font-size: 3rem;
  color: #007bff;
  margin-bottom: 15px;
}

.tabla {
  width: 100%;
  border-collapse: collapse;
}

.tabla th {
  background: #f8f9fa;
  padding: 15px 8px;
  text-align: center;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
  cursor: pointer;
  transition: background 0.3s;
  font-size: 13px;
}

.tabla th:hover {
  background: #e9ecef;
}

.tabla td {
  padding: 12px 8px;
  border-bottom: 1px solid #dee2e6;
  text-align: center;
  font-size: 13px;
}

.tabla tr:hover {
  background: #f8f9fa;
}

.tabla tr.seleccionada {
  background: #e3f2fd;
  border-left: 4px solid #2196f3;
}

.tabla tr.anulada {
  background: #ffebee;
  opacity: 0.7;
}

.fecha {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.fecha span {
  font-weight: 600;
}

.fecha small {
  color: #666;
  font-size: 11px;
}

.cliente {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.cliente small {
  color: #666;
  font-size: 11px;
}

.monto {
  font-weight: 700;
  color: #007bff;
}

.ganancia-monto {
  font-weight: 700;
  color: #ffc107;
}

.badge-pago,
.badge-estado {
  padding: 5px 10px;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  display: inline-block;
}

.badge-pago.efectivo {
  background: #d4edda;
  color: #155724;
}

.badge-pago.yape {
  background: #722F37;
  color: white;
}

.badge-pago.plin {
  background: #007bff;
  color: white;
}

.badge-pago.mixto {
  background: #6f42c1;
  color: white;
}

.badge-estado.activo {
  background: #d4edda;
  color: #155724;
}

.badge-estado.anulado {
  background: #f8d7da;
  color: #721c24;
}

.acciones {
  display: flex;
  gap: 5px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-accion {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: transform 0.2s, opacity 0.3s;
}

.btn-accion:hover:not(:disabled) {
  transform: scale(1.1);
}

.btn-accion.info {
  background: #6f42c1;
  color: white;
}

.btn-accion.view-ticket {
  background: #ffc107;
  color: #212529;
}

.btn-accion.cancel {
  background: #dc3545;
  color: white;
}

.btn-accion:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.sin-datos {
  text-align: center;
  padding: 60px 20px;
  color: #6c757d;
}

.sin-datos i {
  font-size: 4rem;
  margin-bottom: 20px;
  color: #dee2e6;
}

.sin-datos p {
  font-size: 18px;
  font-weight: 600;
  margin: 10px 0;
}

.paginacion {
  padding: 20px;
  background: #f8f9fa;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
}

.paginacion button {
  background: #007bff;
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 20px;
  transition: background 0.3s;
}

.paginacion button:hover:not(:disabled) {
  background: #0056b3;
}

.paginacion button:disabled {
  background: #6c757d;
  cursor: not-allowed;
  opacity: 0.5;
}

.paginacion span {
  font-weight: 600;
  font-size: 16px;
}

.modal {
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
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 700px;
  width: 100%;
  max-height: 85vh;
  overflow-y: auto;
  box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.modal-content.confirmacion {
  max-width: 500px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px;
  border-bottom: 2px solid #dee2e6;
}

.modal-header h3 {
  margin: 0;
  color: #333;
  font-size: 22px;
}

.modal-header button {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #6c757d;
  transition: color 0.3s;
  line-height: 1;
}

.modal-header button:hover {
  color: #dc3545;
}

.modal-body {
  padding: 30px 25px;
}

.detalle-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.detalle-grid > div {
  padding: 12px;
  background: #f8f9fa;
  border-radius: 6px;
}

.detalle-grid strong {
  color: #555;
  display: block;
  margin-bottom: 5px;
  font-size: 13px;
}

.detalle-grid .total {
  font-weight: 700;
  color: #007bff;
  font-size: 18px;
}

.detalle-grid .ganancia-detalle {
  font-weight: 700;
  color: #ffc107;
  font-size: 18px;
}

.confirmacion-content {
  text-align: center;
  padding: 20px 0;
}

.confirmacion-content i {
  font-size: 4rem;
  color: #dc3545;
  margin-bottom: 25px;
}

.confirmacion-content h4 {
  margin: 0 0 25px 0;
  color: #333;
  font-size: 20px;
}

.confirmacion-content p {
  margin: 10px 0;
  color: #666;
  font-size: 15px;
}

.confirmacion-content small {
  color: #999;
  font-style: italic;
  display: block;
  margin-top: 20px;
}

.modal-footer {
  display: flex;
  justify-content: center;
  gap: 15px;
  padding: 25px;
  border-top: 2px solid #dee2e6;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
  padding: 12px 30px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-secondary:hover {
  background: #5a6268;
}

.btn-primary {
  background: #007bff;
  color: white;
  border: none;
  padding: 12px 30px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-danger {
  background: #dc3545;
  color: white;
  border: none;
  padding: 12px 30px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
}

.btn-danger:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.notif {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  padding: 15px 25px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 15px;
  z-index: 1001;
  min-width: 300px;
  max-width: 500px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateX(-50%) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }
}

.notif.success {
  background: #d4edda;
  color: #155724;
  border: 2px solid #c3e6cb;
}

.notif.error {
  background: #f8d7da;
  color: #721c24;
  border: 2px solid #f5c6cb;
}

.notif.info {
  background: #d1ecf1;
  color: #0c5460;
  border: 2px solid #bee5eb;
}

.notif.warning {
  background: #fff3cd;
  color: #856404;
  border: 2px solid #ffeaa7;
}

.notif button {
  background: none;
  border: none;
  color: currentColor;
  cursor: pointer;
  font-size: 18px;
  font-weight: bold;
  transition: opacity 0.3s;
}

.notif button:hover {
  opacity: 0.7;
}

@media (max-width: 1200px) {
  .stats {
    gap: 12px;
  }

  .stat {
    min-width: 130px;
    max-width: 180px;
  }

  .pagos-stats {
    gap: 10px;
  }

  .pago-item {
    min-width: 140px;
  }
}

@media (max-width: 992px) {
  .historial-container {
    padding: 15px;
  }

  .header h2 {
    font-size: 24px;
  }

  .stats {
    flex-direction: column;
    align-items: center;
  }

  .stat {
    width: 100%;
    max-width: 350px;
  }

  .pagos-stats {
    flex-direction: column;
    align-items: center;
  }

  .pago-item {
    width: 100%;
    max-width: 350px;
    min-width: 150px;
  }

  .btn-export {
    width: 100%;
    max-width: 350px;
    justify-content: center;
  }

  .detalle-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .filtro-row {
    flex-direction: column;
    align-items: stretch;
  }

  .campo {
    max-width: none;
  }

  .btn-limpiar,
  .btn-buscar,
  .btn-refresh {
    width: 100%;
  }

  .tabla-container {
    overflow-x: auto;
  }

  .tabla {
    min-width: 950px;
  }

  .modal-content {
    margin: 10px;
    max-height: 90vh;
  }

  .modal-footer {
    flex-direction: column;
  }

  .btn-secondary,
  .btn-primary,
  .btn-danger {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .historial-container {
    padding: 10px;
  }

  .header h2 {
    font-size: 20px;
  }

  .stat {
    padding: 15px;
  }

  .stat i {
    font-size: 24px;
  }

  .stat span {
    font-size: 16px;
  }

  .pago-item {
    padding: 12px;
  }

  .pago-item i {
    font-size: 20px;
  }

  .filtros {
    padding: 15px;
  }

  .tabla th,
  .tabla td {
    padding: 8px 5px;
    font-size: 11px;
  }

  .btn-accion {
    width: 28px;
    height: 28px;
    font-size: 12px;
  }

  .confirmacion-content i {
    font-size: 3rem;
  }

  .confirmacion-content h4 {
    font-size: 18px;
  }

  .notif {
    min-width: auto;
    left: 10px;
    right: 10px;
    transform: none;
  }
}
</style>