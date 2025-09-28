<template>
  <div class="reporte-ventas-container">
    <!-- Header del reporte -->
    <div class="header-reporte">
      <div class="logo-section">
        <div class="logo-placeholder">
          <i class="fas fa-ship"></i>
        </div>
        <div class="company-info">
          <h2>ROCÍO TRAVEL</h2>
          <h4>REPORTE DE VENTAS</h4>
        </div>
      </div>
      
      <div class="date-filters">
        <div class="filter-group">
          <label>Fecha de inicio:</label>
          <input 
            type="date" 
            v-model="filtros.fechaInicio" 
            @change="cargarDatos"
            class="form-control"
          >
        </div>
        <div class="filter-group">
          <label>Fecha de fin:</label>
          <input 
            type="date" 
            v-model="filtros.fechaFin" 
            @change="cargarDatos"
            class="form-control"
          >
        </div>
        <div class="filter-actions">
          <button @click="buscarDatos" class="btn-buscar">
            <i class="fas fa-search"></i> Buscar
          </button>
          <button @click="exportarExcel" class="btn-excel">
            <i class="fas fa-file-excel"></i> Excel
          </button>
        </div>
      </div>
    </div>

    <!-- Tabla principal de ventas -->
    <div class="tabla-ventas">
      <table class="table-reporte">
        <thead>
          <tr>
            <th>N°</th>
            <th>Fecha de emision</th>
            <th>Fecha y hora de viaje</th>
            <th>N° de asientos</th>
            <th>Nombres</th>
            <th>DNI</th>
            <th>Telefono</th>
            <th>Embarcacion</th>
            <th>Ruta</th>
            <th>Tipo de Servicio</th>
            <th>Medio de Pago</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(venta, index) in ventas" :key="venta.id" :class="{'row-even': index % 2 === 0}">
            <td>{{ index + 1 }}</td>
            <td>{{ formatearFecha(venta.fecha_emision) }}</td>
            <td>{{ formatearFechaHora(venta.fecha_viaje) }}</td>
            <td>{{ venta.asientos }}</td>
            <td>{{ venta.nombres }}</td>
            <td>{{ venta.dni }}</td>
            <td>{{ venta.telefono }}</td>
            <td>{{ venta.embarcacion }}</td>
            <td>{{ venta.ruta }}</td>
            <td>{{ venta.tipo_servicio }}</td>
            <td>{{ venta.medio_pago }}</td>
          </tr>
          <tr v-if="ventas.length === 0">
            <td colspan="11" class="no-data">No hay datos para mostrar</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Sección de totales -->
    <div class="totales-section">
      <div class="row">
        <div class="col-md-6">
          <div class="reporte-card">
            <h5>REPORTE POR EMBARCACION</h5>
            <table class="table-totales">
              <thead>
                <tr>
                  <th>Embarcacion</th>
                  <th>Numero de Ventas</th>
                  <th>Total Medio de pago</th>
                  <th>Medio de Pago Generado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="embarcacion in reporteEmbarcaciones" :key="embarcacion.nombre">
                  <td>{{ embarcacion.nombre }}</td>
                  <td>{{ embarcacion.ventas }}</td>
                  <td>{{ embarcacion.total_medio }}</td>
                  <td>{{ embarcacion.medio_generado }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="col-md-6">
          <div class="reporte-card">
            <h5>REPORTE POR TIPO DE PAGO</h5>
            <table class="table-totales">
              <thead>
                <tr>
                  <th>Medio de Transacciones</th>
                  <th>Medio Generado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="pago in reportePagos" :key="pago.tipo">
                  <td>{{ pago.tipo }}</td>
                  <td>S/ {{ pago.monto.toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Totales generales -->
      <div class="totales-generales">
        <div class="row">
          <div class="col-md-6">
            <div class="total-box">
              <h6>Total de pasajes vendidos:</h6>
              <div class="total-number">{{ totales.pasajes_vendidos }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="total-box">
              <h6>Total de pasajes vendidos:</h6>
              <div class="total-number total-final">{{ totales.pasajes_vendidos_final }}</div>
            </div>
          </div>
        </div>
        
        <div class="row mt-3">
          <div class="col-md-6">
            <div class="total-box">
              <h6>Total de Ventas realizadas:</h6>
              <div class="total-money">S/ {{ totales.ventas_realizadas.toFixed(2) }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="total-box">
              <h6>Total de Ventas:</h6>
              <div class="total-money total-final">S/ {{ totales.ventas_total.toFixed(2) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading overlay -->
    <div v-if="cargando" class="loading-overlay">
      <div class="spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Cargando datos...</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "HomeView",
  data() {
    return {
      cargando: false,
      filtros: {
        fechaInicio: this.obtenerFechaInicio(),
        fechaFin: this.obtenerFechaFin()
      },
      ventas: [],
      reporteEmbarcaciones: [],
      reportePagos: [],
      totales: {
        pasajes_vendidos: 0,
        pasajes_vendidos_final: 0,
        ventas_realizadas: 0.00,
        ventas_total: 0.00
      }
    }
  },

  methods: {
    obtenerFechaInicio() {
      const fecha = new Date()
      fecha.setDate(1) // Primer día del mes
      return fecha.toISOString().split('T')[0]
    },

    obtenerFechaFin() {
      const fecha = new Date()
      return fecha.toISOString().split('T')[0]
    },

    async cargarDatos() {
      this.cargando = true
      try {
        // Datos de ejemplo - reemplazar con llamadas reales a la API
        await this.simularCargaDatos()
        
        // En producción, usar estas llamadas:
        // const response = await fetch(`/api/detalle-pasajes?fecha_inicio=${this.filtros.fechaInicio}&fecha_fin=${this.filtros.fechaFin}`)
        // const data = await response.json()
        // this.procesarDatos(data)
        
      } catch (error) {
        console.error('Error al cargar datos:', error)
      } finally {
        this.cargando = false
      }
    },

    async simularCargaDatos() {
      // Simular delay de carga
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Datos de ejemplo
      this.ventas = [
        {
          id: 1,
          fecha_emision: '2024-12-24',
          fecha_viaje: '2024-12-25 08:00:00',
          asientos: 1,
          nombres: 'JUAN CARLOS PEREZ RODRIGUEZ',
          dni: '12345678',
          telefono: '987654321',
          embarcacion: 'RIO AMAZONAS I',
          ruta: 'IQUITOS - YURIMAGUAS',
          tipo_servicio: 'ECONÓMICO',
          medio_pago: 'EFECTIVO'
        },
        {
          id: 2,
          fecha_emision: '2024-12-24',
          fecha_viaje: '2024-12-25 14:30:00',
          asientos: 2,
          nombres: 'MARIA ELENA GARCIA LOPEZ',
          dni: '87654321',
          telefono: '123456789',
          embarcacion: 'ESTRELLA FLUVIAL',
          ruta: 'NAUTA - REQUENA',
          tipo_servicio: 'TURÍSTICO',
          medio_pago: 'TARJETA'
        },
        {
          id: 3,
          fecha_emision: '2024-12-24',
          fecha_viaje: '2024-12-26 06:00:00',
          asientos: 1,
          nombres: 'CARLOS ANTONIO SILVA MENDOZA',
          dni: '11223344',
          telefono: '555666777',
          embarcacion: 'RIO AMAZONAS I',
          ruta: 'IQUITOS - PUCALLPA',
          tipo_servicio: 'ECONÓMICO',
          medio_pago: 'EFECTIVO'
        }
      ]

      this.reporteEmbarcaciones = [
        {
          nombre: 'RIO AMAZONAS I',
          ventas: 15,
          total_medio: 'EFECTIVO/TARJETA',
          medio_generado: 'S/ 1,250.00'
        },
        {
          nombre: 'ESTRELLA FLUVIAL',
          ventas: 8,
          total_medio: 'EFECTIVO',
          medio_generado: 'S/ 960.00'
        }
      ]

      this.reportePagos = [
        { tipo: 'EFECTIVO', monto: 1580.00 },
        { tipo: 'TARJETA', monto: 630.00 },
        { tipo: 'TRANSFERENCIA', monto: 450.00 }
      ]

      this.totales = {
        pasajes_vendidos: this.ventas.length,
        pasajes_vendidos_final: this.ventas.length,
        ventas_realizadas: this.reportePagos.reduce((sum, pago) => sum + pago.monto, 0),
        ventas_total: this.reportePagos.reduce((sum, pago) => sum + pago.monto, 0)
      }
    },

    async buscarDatos() {
      await this.cargarDatos()
    },

    exportarExcel() {
      // Crear datos para Excel
      const datosExcel = []
      
      // Agregar encabezados
      datosExcel.push([
        'N°', 'Fecha de emisión', 'Fecha y hora de viaje', 'N° de asientos',
        'Nombres', 'DNI', 'Teléfono', 'Embarcación', 'Ruta', 'Tipo de Servicio', 'Medio de Pago'
      ])
      
      // Agregar datos de ventas
      this.ventas.forEach((venta, index) => {
        datosExcel.push([
          index + 1,
          this.formatearFecha(venta.fecha_emision),
          this.formatearFechaHora(venta.fecha_viaje),
          venta.asientos,
          venta.nombres,
          venta.dni,
          venta.telefono,
          venta.embarcacion,
          venta.ruta,
          venta.tipo_servicio,
          venta.medio_pago
        ])
      })

      // Crear y descargar archivo
      this.descargarCSV(datosExcel, `reporte_ventas_${this.filtros.fechaInicio}_${this.filtros.fechaFin}.csv`)
    },

    descargarCSV(datos, nombreArchivo) {
      const csvContent = datos.map(row => 
        row.map(cell => `"${cell}"`).join(',')
      ).join('\n')
      
      const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' })
      const link = document.createElement('a')
      
      if (link.download !== undefined) {
        const url = URL.createObjectURL(blob)
        link.setAttribute('href', url)
        link.setAttribute('download', nombreArchivo)
        link.style.visibility = 'hidden'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      }
    },

    formatearFecha(fecha) {
      if (!fecha) return ''
      const date = new Date(fecha)
      return date.toLocaleDateString('es-PE')
    },

    formatearFechaHora(fechaHora) {
      if (!fechaHora) return ''
      const date = new Date(fechaHora)
      return date.toLocaleString('es-PE')
    }
  },

  mounted() {
    this.cargarDatos()
  }
}
</script>

<style scoped>
.reporte-ventas-container {
  background: white;
  min-height: 100vh;
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Header del reporte */
.header-reporte {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #e0e0e0;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 15px;
}

.logo-placeholder {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #1e3c72, #2a5298);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
}

.company-info h2 {
  margin: 0;
  font-size: 24px;
  font-weight: bold;
  color: #1e3c72;
}

.company-info h4 {
  margin: 5px 0 0 0;
  font-size: 16px;
  color: #666;
  font-weight: 500;
}

.date-filters {
  display: flex;
  gap: 15px;
  align-items: flex-end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.filter-group label {
  font-size: 12px;
  color: #666;
  font-weight: 500;
}

.filter-group input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  width: 140px;
}

.filter-actions {
  display: flex;
  gap: 10px;
}

.btn-buscar, .btn-excel {
  padding: 8px 15px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: all 0.3s ease;
}

.btn-buscar {
  background: #007bff;
  color: white;
}

.btn-buscar:hover {
  background: #0056b3;
}

.btn-excel {
  background: #28a745;
  color: white;
}

.btn-excel:hover {
  background: #1e7e34;
}

/* Tabla principal */
.tabla-ventas {
  margin-bottom: 30px;
  overflow-x: auto;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
}

.table-reporte {
  width: 100%;
  border-collapse: collapse;
  background: white;
  font-size: 12px;
}

.table-reporte th {
  background: #f8f9fa;
  padding: 12px 8px;
  text-align: left;
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #dee2e6;
  white-space: nowrap;
}

.table-reporte td {
  padding: 10px 8px;
  border-bottom: 1px solid #dee2e6;
  vertical-align: middle;
}

.row-even {
  background: #f8f9fa;
}

.no-data {
  text-align: center;
  color: #666;
  font-style: italic;
  padding: 40px;
}

/* Sección de totales */
.totales-section {
  margin-top: 40px;
}

.reporte-card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.reporte-card h5 {
  margin: 0 0 15px 0;
  font-size: 14px;
  font-weight: bold;
  color: #495057;
  text-transform: uppercase;
  border-bottom: 2px solid #007bff;
  padding-bottom: 8px;
}

.table-totales {
  width: 100%;
  border-collapse: collapse;
  font-size: 11px;
}

.table-totales th {
  background: #f1f3f4;
  padding: 8px;
  text-align: left;
  font-weight: 600;
  border: 1px solid #ddd;
  font-size: 10px;
}

.table-totales td {
  padding: 8px;
  border: 1px solid #ddd;
}

/* Totales generales */
.totales-generales {
  margin-top: 30px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.total-box {
  background: white;
  padding: 15px;
  border-radius: 6px;
  text-align: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  margin-bottom: 15px;
}

.total-box h6 {
  margin: 0 0 10px 0;
  font-size: 12px;
  color: #666;
}

.total-number {
  font-size: 24px;
  font-weight: bold;
  color: #007bff;
}

.total-money {
  font-size: 24px;
  font-weight: bold;
  color: #28a745;
}

.total-final {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 8px;
  border-radius: 4px;
}

/* Loading */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.spinner {
  text-align: center;
  color: #007bff;
}

.spinner i {
  font-size: 48px;
  margin-bottom: 15px;
}

.spinner p {
  font-size: 16px;
  margin: 0;
}

/* Grid system básico */
.row {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.col-md-6 {
  flex: 1;
  min-width: 300px;
}

.mt-3 {
  margin-top: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .header-reporte {
    flex-direction: column;
    gap: 20px;
  }
  
  .date-filters {
    flex-direction: column;
    width: 100%;
  }
  
  .filter-group input {
    width: 100%;
  }
  
  .table-reporte {
    font-size: 10px;
  }
  
  .table-reporte th,
  .table-reporte td {
    padding: 6px 4px;
  }
}

@media (max-width: 480px) {
  .reporte-ventas-container {
    padding: 10px;
  }
  
  .company-info h2 {
    font-size: 18px;
  }
  
  .total-number,
  .total-money {
    font-size: 18px;
  }
}
</style>