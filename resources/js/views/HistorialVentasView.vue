<template>
  <div class="historial-container">
    <!-- Navegación rápida -->
    <div class="nav-quick">
      <button @click="$router.push('/')" class="btn-nav">
        <i class="fas fa-home"></i> Inicio
      </button>
      <button @click="$router.go(-1)" class="btn-nav">
        <i class="fas fa-arrow-left"></i> Volver
      </button>
    </div>

    <!-- Header -->
    <div class="header">
      <h2>Ver nota de venta - Rocío Travel</h2>
      <div class="stats">
        <div class="stat">
          <i class="fas fa-ticket-alt"></i>
          <span>{{ ventas.length }}</span>
          <small>TOTAL VENTAS</small>
        </div>
        <div class="stat">
          <i class="fas fa-dollar-sign"></i>
          <span>S/ {{ totalIngresos.toFixed(2) }}</span>
          <small>INGRESOS ACTIVOS</small>
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
          <input type="date" v-model="filtros.fechaInicio" />
        </div>
        <div class="campo">
          <label>Fecha fin</label>
          <input type="date" v-model="filtros.fechaFin" />
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
          <input type="text" v-model="filtros.documento" placeholder="DNI" @keyup.enter="buscar" />
        </div>
        <div class="campo">
          <label>Nombre:</label>
          <input type="text" v-model="filtros.nombre" placeholder="Cliente" @keyup.enter="buscar" />
        </div>
        <div class="campo">
          <label>Estado:</label>
          <select v-model="filtros.estado">
            <option value="">Todos los estados</option>
            <option value="activo">Activo</option>
            <option value="completado">Completado</option>
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
            <th>Medio de Pago</th>
            <th @click="ordenar('subtotal')">Total</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="ventas.length === 0 && !cargando">
            <td colspan="7" class="sin-datos">
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
            <td>
              <span class="badge-pago" :class="venta.medio_pago || 'efectivo'">
                {{ textoPago(venta.medio_pago) }}
              </span>
            </td>
            <td class="monto">S/ {{ parseFloat(venta.subtotal || 0).toFixed(2) }}</td>
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
                <button @click.stop="imprimir(venta)" 
                        class="btn-accion print" 
                        :disabled="venta.estado === 'anulado'">
                  <i class="fas fa-print"></i>
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
            <div><strong>Total:</strong> <span class="total">S/ {{ parseFloat(ventaSeleccionada.subtotal || 0).toFixed(2) }}</span></div>
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
      ventaSeleccionada: null,
      mostrarDetalle: false,
      pagina: 1,
      porPagina: 15,
      
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
    totalIngresos() {
      return this.ventas
        .filter(v => v.estado !== 'anulado')
        .reduce((total, v) => total + parseFloat(v.subtotal || 0), 0)
    },

    totalPaginas() {
      return Math.ceil(this.ventas.length / this.porPagina)
    },

    ventasPaginadas() {
      const inicio = (this.pagina - 1) * this.porPagina
      return this.ventas.slice(inicio, inicio + this.porPagina)
    }
  },

  mounted() {
    this.cargarDatos()
  },

  methods: {
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
        this.ventas = data.data || data || []
        this.pagina = 1
        
        this.mostrarNotif(`${this.ventas.length} registros cargados`, 'success')
        
      } catch (error) {
        console.error('Error:', error)
        this.mostrarNotif('Error al cargar datos', 'error')
        this.ventas = []
      } finally {
        this.cargando = false
      }
    },

    buscar() {
      // Filtrar datos localmente para evitar cuelgues
      let resultados = [...this.ventas]
      
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
      
      this.ventas = resultados
      this.pagina = 1
      this.mostrarNotif(`${resultados.length} registros encontrados`, 'info')
    },

    limpiarFiltros() {
      this.filtros = {
        fechaInicio: '',
        fechaFin: '',
        documento: '',
        nombre: '',
        estado: ''
      }
      this.cargarDatos()
    },

    ordenar(campo) {
      this.ventas.sort((a, b) => {
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

    async imprimir(venta) {
      if (venta.estado === 'anulado') return
      
      try {
        const datos = {
          numero_ticket: `TK-${String(venta.id).padStart(6, '0')}`,
          fecha_emision: this.fecha(new Date()),
          cliente: {
            nombre: venta.nombre_cliente || 'CLIENTE',
            documento: venta.documento_cliente || '',
            contacto: venta.contacto_cliente || ''
          },
          cantidad: venta.cantidad || 1,
          descripcion: venta.descripcion || 'Pasaje',
          subtotal: parseFloat(venta.subtotal || 0).toFixed(2)
        }
        
        const response = await fetch('/api/pasajes/generar-ticket-venta', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(datos)
        })
        
        if (response.ok) {
          const html = await response.text()
          const ventana = window.open('', '_blank', 'width=400,height=600')
          ventana.document.write(html)
          ventana.document.close()
          
          setTimeout(() => {
            ventana.print()
            ventana.close()
          }, 500)
          
          this.mostrarNotif('Ticket enviado a impresión', 'success')
        }
      } catch (error) {
        this.mostrarNotif('Error al imprimir', 'error')
      }
    },

    exportarCSV() {
      if (this.ventas.length === 0) return
      
      const headers = ['Fecha', 'Cliente', 'DNI', 'Total', 'Estado']
      const filas = this.ventas.map(v => [
        this.fecha(v.created_at),
        `"${v.nombre_cliente || 'N/A'}"`,
        v.documento_cliente || 'N/A',
        parseFloat(v.subtotal || 0).toFixed(2),
        this.textoEstado(v.estado)
      ].join(','))
      
      const csv = [headers.join(','), ...filas].join('\n')
      const blob = new Blob([csv], { type: 'text/csv' })
      const url = URL.createObjectURL(blob)
      
      const link = document.createElement('a')
      link.href = url
      link.download = `historial-${new Date().toISOString().split('T')[0]}.csv`
      link.click()
      
      URL.revokeObjectURL(url)
      this.mostrarNotif('Archivo descargado', 'success')
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
        completado: 'Completado',
        anulado: 'Anulado',
        pendiente: 'Pendiente'
      }
      return estados[estado] || 'Activo'
    },

    textoPago(medio) {
      const medios = {
        efectivo: 'Efectivo',
        yape: 'Yape',
        plin: 'Plin',
        transferencia: 'Transferencia',
        tarjeta: 'Tarjeta'
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
}

/* Navegación */
.nav-quick {
  position: fixed;
  top: 10px;
  right: 10px;
  z-index: 1000;
  display: flex;
  gap: 5px;
}

.btn-nav {
  background: #dc3545;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 15px;
}

.header h2 {
  margin: 0;
  color: #333;
}

.stats {
  display: flex;
  gap: 15px;
  align-items: center;
  flex-wrap: wrap;
}

.stat {
  background: white;
  padding: 15px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  min-width: 140px;
}

.stat i {
  font-size: 20px;
  color: #007bff;
}

.stat span {
  font-size: 16px;
  font-weight: 700;
  color: #333;
}

.stat small {
  font-size: 10px;
  color: #666;
  text-transform: uppercase;
}

.btn-export {
  background: #28a745;
  color: white;
  border: none;
  padding: 12px 16px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-export:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Filtros */
.filtros {
  background: white;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filtro-row {
  display: flex;
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
  min-width: 150px;
}

.campo label {
  display: block;
  font-weight: 600;
  margin-bottom: 5px;
  color: #555;
  font-size: 14px;
}

.campo input,
.campo select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
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
  width: 40px;
  height: 38px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 18px;
}

.btn-buscar,
.btn-refresh {
  background: #007bff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.btn-refresh {
  background: #17a2b8;
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

/* Tabla */
.tabla-container {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  position: relative;
}

.loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255,255,255,0.9);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.loading i {
  font-size: 2rem;
  color: #007bff;
  margin-bottom: 10px;
}

.tabla {
  width: 100%;
  border-collapse: collapse;
}

.tabla th {
  background: #f8f9fa;
  padding: 12px 8px;
  text-align: left;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
  cursor: pointer;
}

.tabla th:hover {
  background: #e9ecef;
}

.tabla td {
  padding: 10px 8px;
  border-bottom: 1px solid #dee2e6;
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
}

.cliente small {
  color: #666;
  font-size: 11px;
}

.monto {
  font-weight: 700;
  color: #28a745;
  text-align: right;
}

.badge-pago,
.badge-estado {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
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

.badge-pago.transferencia {
  background: #17a2b8;
  color: white;
}

.badge-pago.tarjeta {
  background: #ffc107;
  color: #212529;
}

.badge-estado.activo {
  background: #d4edda;
  color: #155724;
}

.badge-estado.completado {
  background: #cce7ff;
  color: #004085;
}

.badge-estado.anulado {
  background: #f8d7da;
  color: #721c24;
}

.badge-estado.pendiente {
  background: #fff3cd;
  color: #856404;
}

.acciones {
  display: flex;
  gap: 5px;
  justify-content: center;
}

.btn-accion {
  width: 30px;
  height: 30px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-accion.info {
  background: #6f42c1;
  color: white;
}

.btn-accion.print {
  background: #28a745;
  color: white;
}

.btn-accion:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.sin-datos {
  text-align: center;
  padding: 40px;
  color: #6c757d;
}

.sin-datos i {
  font-size: 3rem;
  margin-bottom: 15px;
  color: #dee2e6;
}

/* Paginación */
.paginacion {
  padding: 15px;
  background: #f8f9fa;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
}

.paginacion button {
  background: #007bff;
  color: white;
  border: none;
  width: 35px;
  height: 35px;
  border-radius: 4px;
  cursor: pointer;
}

.paginacion button:disabled {
  background: #6c757d;
  cursor: not-allowed;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  max-width: 600px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #dee2e6;
}

.modal-header h3 {
  margin: 0;
}

.modal-header button {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #6c757d;
}

.modal-body {
  padding: 20px;
}

.detalle-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.detalle-grid .total {
  font-weight: 700;
  color: #28a745;
  font-size: 16px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 20px;
  border-top: 1px solid #dee2e6;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-primary {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

/* Notificación */
.notif {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 20px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  z-index: 1001;
  min-width: 250px;
}

.notif.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.notif.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.notif.info {
  background: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.notif button {
  background: none;
  border: none;
  color: currentColor;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
  .historial-container {
    padding: 15px;
  }

  .header {
    flex-direction: column;
    align-items: flex-start;
  }

  .stats {
    width: 100%;
    justify-content: space-between;
  }

  .stat {
    flex: 1;
    min-width: 100px;
  }

  .filtro-row {
    flex-direction: column;
  }

  .campo {
    min-width: auto;
  }

  .tabla-container {
    overflow-x: auto;
  }

  .tabla {
    min-width: 600px;
  }

  .detalle-grid {
    grid-template-columns: 1fr;
  }

  .modal-content {
    width: 95%;
    margin: 10px;
  }

  .modal-footer {
    flex-direction: column;
  }

  .notif {
    right: 10px;
    left: 10px;
    min-width: auto;
  }
}

@media (max-width: 480px) {
  .nav-quick {
    position: relative;
    top: auto;
    right: auto;
    margin-bottom: 15px;
  }

  .header h2 {
    font-size: 18px;
  }

  .stats {
    flex-direction: column;
  }

  .stat {
    width: 100%;
  }

  .tabla th,
  .tabla td {
    padding: 6px 4px;
    font-size: 12px;
  }

  .acciones {
    flex-direction: column;
    gap: 2px;
  }

  .btn-accion {
    width: 25px;
    height: 25px;
    font-size: 10px;
  }
}
</style>