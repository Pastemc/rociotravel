<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>ROCÍO TRAVEL - PASAJE FLUVIAL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Arial', sans-serif; 
            background: #f5f5f5; 
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }
        
        .page {
            width: 210mm;
            margin: 20px auto;
            background: white;
        }
        
        /* BOTÓN DE DESCARGA */
        .download-section {
            background: linear-gradient(135deg, #0891b2 0%, #0369a1 100%);
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .btn-download {
            background: #fff;
            color: #0369a1;
            border: none;
            padding: 8px 20px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 6px;
        }
        
        .btn-download:hover {
            background: #f0f9ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(3, 105, 161, 0.3);
        }
        
        .btn-download:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .download-info {
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* TICKET PRINCIPAL */
        .ticket {
            border: 3px solid #0369a1;
            background: white;
            position: relative;
        }
        
        /* HEADER CON TÍTULO CENTRADO Y LOGO */
        .header-main {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-bottom: 2px solid #0891b2;
            background: linear-gradient(135deg, #0891b2 0%, #0369a1 100%);
            gap: 20px;
        }
        
        .logo-container {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border: 2px solid #fff;
            border-radius: 8px;
            padding: 5px;
            background: white;
        }
        
        .header-center {
            text-align: center;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 5px;
            color: #fff;
        }
        
        .company-subtitle {
            font-size: 11px;
            font-weight: normal;
            letter-spacing: 1px;
            color: #f0f9ff;
            text-transform: uppercase;
        }
        
        /* INFORMACIÓN DE LA EMPRESA */
        .company-info {
            padding: 10px 20px;
            border-bottom: 1px solid #0891b2;
            background: #f0f9ff;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .info-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            color: #0369a1;
        }
        
        /* DATOS DEL CLIENTE */
        .client-section {
            padding: 12px 20px;
            border-bottom: 1px solid #0891b2;
        }
        
        .section-row {
            display: flex;
            gap: 20px;
            margin-bottom: 8px;
        }
        
        .section-row:last-child {
            margin-bottom: 0;
        }
        
        .field-group {
            flex: 1;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        
        .field-label {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            min-width: fit-content;
            color: #0369a1;
        }
        
        .field-value {
            flex: 1;
            border-bottom: 1px solid #0891b2;
            padding-bottom: 2px;
            font-size: 10px;
            min-height: 16px;
        }
        
        /* TABLA DE SERVICIOS */
        .services-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .services-table thead {
            background: linear-gradient(135deg, #0891b2 0%, #0369a1 100%);
            color: #fff;
        }
        
        .services-table th {
            padding: 8px 15px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid #0369a1;
        }
        
        .services-table th:last-child {
            border-right: none;
        }
        
        .services-table th.desc-header {
            text-align: left;
        }
        
        .services-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #e0f2fe;
            border-right: 1px solid #e0f2fe;
            font-size: 10px;
            text-align: center;
        }
        
        .services-table tbody td:last-child {
            border-right: none;
        }
        
        .service-description {
            font-weight: bold;
            margin-bottom: 3px;
            text-align: center;
            color: #0369a1;
        }
        
        .service-detail {
            font-size: 9px;
            color: #64748b;
            text-align: center;
        }
        
        /* SECCIÓN DE TOTAL */
        .total-section {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            border-top: 2px solid #0891b2;
        }
        
        .payment-details {
            flex: 1;
            padding: 15px 20px;
            background: #f0f9ff;
        }
        
        .payment-title {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            color: #0369a1;
        }
        
        .payment-info {
            font-size: 9px;
            line-height: 1.6;
            color: #334155;
        }
        
        .total-box {
            background: linear-gradient(135deg, #0891b2 0%, #0369a1 100%);
            color: #fff;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            min-width: 200px;
        }
        
        .total-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .total-amount {
            font-size: 20px;
            font-weight: bold;
        }
        
        /* DETALLES DEL VIAJE */
        .travel-details {
            padding: 12px 20px;
            background: #f0f9ff;
            border-top: 1px solid #0891b2;
            display: flex;
            justify-content: space-around;
        }
        
        .travel-item {
            text-align: center;
        }
        
        .travel-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }
        
        .travel-value {
            font-size: 11px;
            font-weight: bold;
            color: #0369a1;
        }
        
        /* TÉRMINOS Y CONDICIONES */
        .terms-section {
            padding: 12px 20px;
            background: #fff;
            border-top: 1px solid #0891b2;
        }
        
        .terms-title {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            color: #0369a1;
        }
        
        .terms-text {
            font-size: 8px;
            line-height: 1.5;
            color: #334155;
            text-align: justify;
        }
        
        /* FOOTER NEGRO */
        .footer-black {
            background: linear-gradient(135deg, #0891b2 0%, #0369a1 100%);
            color: #fff;
            padding: 10px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* FOOTER INFERIOR */
        .footer-bottom {
            background: #0369a1;
            color: #fff;
            padding: 8px;
            text-align: center;
            font-size: 8px;
            letter-spacing: 0.5px;
        }
        
        @media print {
            body { 
                margin: 0; 
                background: white;
            }
            .page { 
                margin: 0; 
                width: 100%;
            }
            .download-section { 
                display: none; 
            }
            .ticket { 
                page-break-inside: avoid; 
            }
        }
        
        @media screen and (max-width: 768px) {
            .page {
                width: 100%;
                margin: 0;
            }
            
            .download-section {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-download {
                width: 100%;
                justify-content: center;
            }
            
            .header-center {
                padding: 0 20px;
            }
            
            .logo-left {
                position: static;
                margin-bottom: 10px;
            }
            
            .header-main {
                flex-direction: column;
            }
            
            .total-section {
                flex-direction: column;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="page">
        <!-- BOTÓN DE DESCARGA -->
        <div class="download-section">
            <button class="btn-download" onclick="descargarImagenPNG()">
                <i class="fas fa-download"></i>
                Descargar PNG
            </button>
            <div class="download-info">
                <i class="fas fa-info-circle"></i> Descarga tu pasaje en alta calidad
            </div>
        </div>
        
        <div class="ticket">
            <!-- HEADER PRINCIPAL CON LOGO Y TÍTULO CENTRADO -->
            <div class="header-main">
                <div class="logo-container">
                    <img src="{{ asset('images/rocio.jpg') }}" alt="Logo Rocío Travel" class="logo-img">
                </div>
                <div class="header-center">
                    <div class="company-name">ROCÍO TRAVEL</div>
                    <div class="company-subtitle">Venta de Pasajes Fluviales</div>
                </div>
            </div>
            
            <!-- INFORMACIÓN DE LA EMPRESA -->
            <div class="company-info">
                <div class="info-item">
                    <span class="info-label">Dirección:</span>
                    <span>Calle Pevas N° 366, Iquitos</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contacto:</span>
                    <span>+51 901 978 379</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span>travelrocio19@gmail.com</span>
                </div>
            </div>
            
            <!-- DATOS DEL CLIENTE -->
            <div class="client-section">
                <div class="section-row">
                    <div class="field-group" style="flex: 2;">
                        <span class="field-label">Cliente:</span>
                        <span class="field-value">{{ strtoupper($cliente['nombre'] ?? 'SIN ESPECIFICAR') }}</span>
                    </div>
                    <div class="field-group">
                        <span class="field-label">DNI/CE:</span>
                        <span class="field-value">{{ $cliente['documento'] ?? '---' }}</span>
                    </div>
                </div>
                <div class="section-row">
                    <div class="field-group">
                        <span class="field-label">Fecha emisión:</span>
                        <span class="field-value">{{ $fecha_emision ?? date('d/m/Y') }}</span>
                    </div>
                    <div class="field-group">
                        <span class="field-label">Medio de pago:</span>
                        <span class="field-value">{{ strtoupper($medio_pago ?? 'EFECTIVO') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- TABLA DE SERVICIOS CON HEADERS Y CONTENIDO CENTRADO -->
            <table class="services-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">CANT</th>
                        <th style="width: 60%;">DESCRIPCIÓN</th>
                        <th style="width: 15%;">IMPORTE</th>
                        <th style="width: 15%;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <div class="service-description">{{ strtoupper($descripcion ?? 'PASAJE FLUVIAL') }}</div>
                            @if(!empty($embarcacion))
                            <div class="service-detail">Embarcación: {{ strtoupper($embarcacion) }}</div>
                            @endif
                            @if(!empty($puerto_embarque))
                            <div class="service-detail">Puerto de embarque: {{ strtoupper($puerto_embarque) }}</div>
                            @endif
                            @if(!empty($destino))
                            <div class="service-detail">Destino: {{ strtoupper($destino) }}</div>
                            @endif
                        </td>
                        <td>S/ {{ number_format($total ?? 0, 2) }}</td>
                        <td>S/ {{ number_format($total ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- SECCIÓN DE TOTAL Y DETALLES DE PAGO -->
            <div class="total-section">
                <div class="payment-details">
                    <div class="payment-title">Observaciones:</div>
                    <div class="payment-info">
                        {{ $detalles_pago ?? 'Pasaje válido únicamente para la fecha y hora indicada. No incluye alimentación.' }}
                    </div>
                </div>
                <div class="total-box">
                    <span class="total-label">Total S/</span>
                    <span class="total-amount">{{ number_format($total ?? 0, 2) }}</span>
                </div>
            </div>
            
            <!-- DETALLES DEL VIAJE -->
            <div class="travel-details">
                <div class="travel-item">
                    <div class="travel-label">Fecha de viaje</div>
                    <div class="travel-value">{{ $fecha_viaje ?? '---' }}</div>
                </div>
                <div class="travel-item">
                    <div class="travel-label">Hora de embarque</div>
                    <div class="travel-value">{{ $hora_embarque ?? '---' }}</div>
                </div>
                <div class="travel-item">
                    <div class="travel-label">Hora de salida</div>
                    <div class="travel-value">{{ $hora_salida ?? '---' }}</div>
                </div>
                <div class="travel-item">
                    <div class="travel-label">Equipaje permitido</div>
                    <div class="travel-value">15 KG</div>
                </div>
            </div>
            
            <!-- TÉRMINOS Y CONDICIONES -->
            <div class="terms-section">
                <div class="terms-title">Términos y Condiciones:</div>
                <div class="terms-text">
                    La empresa no aceptará devoluciones una vez realizada la venta y separado el cupo. En caso que la embarcación haya partido y Ud. no aborde, perderá su derecho a viajar y el valor de su pasaje. El pasajero embarca y desembarca su propio equipaje. La empresa se responsabiliza únicamente de los pasajeros portadores de este pasaje. Este ticket puede ser cambiado por boleta de venta o factura según requerimiento.
                </div>
            </div>
            
            <!-- FOOTER NEGRO -->
            <div class="footer-black">
                CONSERVE ESTE PASAJE DURANTE TODO EL VIAJE
            </div>
            
            <!-- FOOTER INFERIOR -->
            <div class="footer-bottom">
                IQUITOS • YURIMAGUAS • PUCALLPA • SANTA ROSA • Y TODOS LOS PUERTOS INTERMEDIOS
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        let descargando = false;
        
        function descargarImagenPNG() {
            if (descargando) {
                return;
            }
            
            descargando = true;
            const boton = document.querySelector('.btn-download');
            const textoOriginal = boton.innerHTML;
            
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
            boton.disabled = true;
            
            const ticket = document.querySelector('.ticket');
            
            const opciones = {
                scale: 3,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false,
                width: ticket.scrollWidth,
                height: ticket.scrollHeight,
                scrollX: 0,
                scrollY: 0,
                windowWidth: ticket.scrollWidth,
                windowHeight: ticket.scrollHeight,
                onclone: function(clonedDoc) {
                    const clonedTicket = clonedDoc.querySelector('.ticket');
                    if (clonedTicket) {
                        clonedTicket.style.transform = 'scale(1)';
                        clonedTicket.style.width = '100%';
                    }
                }
            };
            
            html2canvas(ticket, opciones).then(function(canvas) {
                try {
                    canvas.toBlob(function(blob) {
                        const link = document.createElement('a');
                        const url = URL.createObjectURL(blob);
                        
                        const fecha = new Date();
                        const fechaStr = `${fecha.getFullYear()}${String(fecha.getMonth()+1).padStart(2, '0')}${String(fecha.getDate()).padStart(2, '0')}`;
                        const horaStr = `${String(fecha.getHours()).padStart(2, '0')}${String(fecha.getMinutes()).padStart(2, '0')}${String(fecha.getSeconds()).padStart(2, '0')}`;
                        
                        const nombreClienteElement = document.querySelector('.field-value');
                        let nombreCliente = 'Cliente';
                        if (nombreClienteElement) {
                            nombreCliente = nombreClienteElement.textContent.trim().replace(/[^a-zA-Z0-9]/g, '_').substring(0, 20);
                        }
                        
                        const nombreArchivo = `Pasaje_Rocio_Travel_${nombreCliente}_${fechaStr}_${horaStr}.png`;
                        
                        link.download = nombreArchivo;
                        link.href = url;
                        link.style.display = 'none';
                        
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        setTimeout(() => {
                            URL.revokeObjectURL(url);
                            boton.innerHTML = textoOriginal;
                            boton.disabled = false;
                            descargando = false;
                        }, 500);
                        
                    }, 'image/png', 1.0);
                    
                } catch (error) {
                    console.error('Error al descargar:', error);
                    boton.innerHTML = textoOriginal;
                    boton.disabled = false;
                    descargando = false;
                }
            }).catch(function(error) {
                console.error('Error en html2canvas:', error);
                boton.innerHTML = textoOriginal;
                boton.disabled = false;
                descargando = false;
            });
        }
    </script>
</body>
</html>