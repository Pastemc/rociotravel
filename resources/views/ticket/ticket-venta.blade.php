<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta - {{ $numero_ticket }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .ticket-container {
            background: white;
            width: 80mm;
            padding: 8mm;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* Botones de acción */
        .action-buttons {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-print {
            background: #28a745;
            color: white;
        }

        .btn-print:hover {
            background: #218838;
        }

        .btn-close {
            background: #dc3545;
            color: white;
        }

        .btn-close:hover {
            background: #c82333;
        }

        /* Header del ticket con logo */
        .ticket-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 8px;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 12px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #333;
            flex-shrink: 0;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .empresa-info {
            flex: 1;
            text-align: center;
        }

        .empresa-nombre {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .empresa-subtitulo {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .empresa-destinos {
            font-size: 9px;
            line-height: 1.2;
            margin-bottom: 5px;
            text-align: center;
        }

        .empresa-contacto {
            font-size: 9px;
            line-height: 1.3;
            text-align: center;
        }

        .empresa-contacto div {
            margin-bottom: 1px;
        }

        .empresa-ubicacion {
            font-weight: bold;
            font-size: 10px;
            margin-top: 3px;
        }

        /* Información del ticket */
        .ticket-info {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }

        .cliente-row {
            margin-bottom: 2px;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
        }

        .cliente-row strong {
            min-width: 90px;
        }

        /* Tabla de productos */
        .productos-section {
            margin-bottom: 10px;
        }

        .productos-header {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 2px;
            font-weight: bold;
            font-size: 10px;
            display: grid;
            grid-template-columns: 25px 1fr 45px 45px;
            gap: 3px;
            text-align: center;
        }

        .producto-row {
            padding: 3px 2px;
            font-size: 10px;
            display: grid;
            grid-template-columns: 25px 1fr 45px 45px;
            gap: 3px;
            border-bottom: 1px solid #ccc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Total */
        .total-section {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 6px 0;
            margin-bottom: 12px;
            background: #f9f9f9;
        }

        .total-row {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            padding-right: 10px;
        }

        /* Información del viaje */
        .viaje-info {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }

        .viaje-row {
            margin-bottom: 3px;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
        }

        .viaje-row strong {
            min-width: 100px;
        }

        .nota-section {
            margin-top: 5px;
            font-size: 9px;
            font-style: italic;
            background: #f0f0f0;
            padding: 3px;
            border-radius: 3px;
        }

        /* Footer */
        .ticket-footer {
            text-align: center;
            font-size: 8px;
            line-height: 1.3;
        }

        .politicas {
            margin-bottom: 10px;
            text-align: justify;
            padding: 5px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 3px;
        }

        .equipaje-info {
            text-align: right;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 10px;
            background: #d1ecf1;
            padding: 3px;
            border-radius: 3px;
        }

        .separador {
            text-align: center;
            margin: 8px 0;
            font-size: 10px;
            font-weight: bold;
        }

        .cambio-boleta {
            font-weight: bold;
            margin-top: 8px;
            font-size: 9px;
            background: #f8d7da;
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #f5c6cb;
        }

        /* Estilos para impresión */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .ticket-container {
                width: 80mm;
                margin: 0;
                padding: 3mm;
                box-shadow: none;
                border: none;
                border-radius: 0;
            }

            .action-buttons {
                display: none;
            }

            .ticket-header,
            .ticket-info,
            .viaje-info {
                break-inside: avoid;
            }

            .politicas,
            .equipaje-info,
            .cambio-boleta {
                background: transparent !important;
                border: none !important;
            }
        }

        /* Ajustes para papel térmico pequeño */
        @media (max-width: 80mm) {
            .ticket-container {
                width: 58mm;
                font-size: 9px;
            }
            
            .empresa-nombre {
                font-size: 14px;
            }
            
            .logo {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <!-- Botones de acción -->
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-print">Imprimir</button>
            <button onclick="window.close()" class="btn btn-close">Cerrar</button>
        </div>

        <!-- Información del ticket -->
        <div class="ticket-info">
            <div class="cliente-row">
                <strong>CLIENTE:</strong> 
                <span>{{ strtoupper($cliente['nombre']) }}</span>
            </div>
            <div class="cliente-row">
                <strong>DOC. IDENT:</strong>
                <span>{{ $cliente['documento'] }}</span>
            </div>
            <div class="cliente-row">
                <strong>CONTACTO:</strong>
                <span>{{ $cliente['contacto'] }}</span>
            </div>
            <div class="cliente-row">
                <strong>NACIONALIDAD:</strong>
                <span>{{ strtoupper($cliente['nacionalidad']) }}</span>
            </div>
            <div class="cliente-row">
                <strong>F. EMISION:</strong>
                <span>{{ $fecha_emision }} {{ $hora_emision }}</span>
            </div>
            <div class="cliente-row">
                <strong>MEDIO PAGO:</strong>
                <span>{{ ucfirst($medio_pago) }}</span>
            </div>
        </div>

        <!-- Productos/Servicios -->
        <div class="productos-section">
            <div class="productos-header">
                <div>CANT.</div>
                <div>DESCRIPCION</div>
                <div>P. UNIT</div>
                <div>SUB TOTAL</div>
            </div>
            
            <div class="producto-row">
                <div class="text-center">{{ $cantidad }}</div>
                <div class="text-left">{{ strtoupper($descripcion) }}</div>
                <div class="text-right">{{ $precio_unitario }}</div>
                <div class="text-right">{{ $subtotal }}</div>
            </div>
        </div>

        <!-- Total -->
        <div class="total-section">
            <div class="total-row">
                Total: S/ {{ $total }}
            </div>
        </div>

        <!-- Información del viaje -->
        <div class="viaje-info">
            <div class="viaje-row">
                <strong>F. VIAJE:</strong>
                <span>{{ $fecha_viaje }}</span>
            </div>
            <div class="viaje-row">
                <strong>EMBARCACION:</strong>
                <span>{{ $embarcacion }}</span>
            </div>
            <div class="viaje-row">
                <strong>PUERTO EMBAR:</strong>
                <span>{{ $puerto_embarque }}</span>
            </div>
            <div class="viaje-row">
                <strong>H. EMBARQUE:</strong>
                <span>{{ $hora_embarque }}</span>
            </div>
            <div class="viaje-row">
                <strong>H. SALIDA:</strong>
                <span>{{ $hora_salida }}</span>
            </div>
            
            @if($nota)
                <div class="nota-section">
                    <strong>Nota:</strong> {{ $nota }}
                </div>
            @endif
        </div>

        <div class="separador">
            ================================
        </div>

        <!-- Footer -->
        <div class="ticket-footer">
            <div class="politicas">
                La empresa no aceptará devoluciones una vez realizada la venta y separado el cupo; en caso que la embarcación haya partido y Ud. no abordó, perderá su derecho a viajar y el valor de su pasaje.
            </div>
            
            <div class="equipaje-info">
                Equipaje: 15Kg por pasajero
            </div>
            
            <div class="separador">
            ================================
            </div>
            
            <div class="cambio-boleta">
                ESTE TICKET PUEDE SER CAMBIADO POR BOLETA DE<br>
                VENTA O FACTURA
            </div>
        </div>
    </div>

    <script>
        // Función para cerrar
        function cerrarVentana() {
            window.close();
        }
        
        // Cerrar con ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>