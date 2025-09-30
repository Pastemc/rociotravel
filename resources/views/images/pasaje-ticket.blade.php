<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $tipo_documento }} - {{ $numero_boleta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: Arial, sans-serif; 
            background: white; 
            font-size: 11px;
            line-height: 1.4;
        }
        
        .page {
            width: 210mm;
            margin: 0 auto;
            padding: 10mm;
        }
        
        .ticket {
            border: 2px solid #000;
            padding: 10mm;
            background: white;
            position: relative;
        }
        
        /* ENCABEZADO */
        .header-section {
            position: relative;
            width: 100%;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 85px;
        }
        
        .logo-container {
            border: 2px solid #00a0e3;
            border-radius: 8px;
            padding: 6px;
            background: white;
            display: inline-block;
        }
        
        .logo-img {
            width: 70px;
            height: auto;
            display: block;
        }
        
        .header-center {
            text-align: center;
            max-width: 550px;
        }
        
        .logo-empresa {
            font-size: 20px;
            font-weight: bold;
            color: #c41e3a;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .subtitulo {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #000;
        }
        
        .info-empresa {
            font-size: 8px;
            line-height: 1.5;
            color: #333;
        }
        
        .info-empresa p {
            margin: 2px 0;
        }
        
        .info-empresa .destinos {
            font-weight: normal;
            color: #555;
        }
        
        .info-empresa .ubicacion {
            color: #00a0e3;
            font-weight: bold;
            font-size: 9px;
            margin-top: 6px;
        }
        
        /* FECHA DE EMISIÓN */
        .fecha-emision {
            text-align: center;
            padding: 8px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            margin: 10px 0;
            font-size: 10px;
        }
        
        .fecha-emision strong {
            color: #c41e3a;
        }
        
        /* DATOS DEL CLIENTE */
        .datos-pasaje {
            margin: 12px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        
        .fila-datos {
            display: table;
            width: 100%;
            margin: 5px 0;
        }
        
        .columna-dato {
            display: table-cell;
            padding: 3px 8px;
        }
        
        .columna-dato.col-25 { width: 25%; }
        .columna-dato.col-33 { width: 33.33%; }
        .columna-dato.col-50 { width: 50%; }
        .columna-dato.col-75 { width: 75%; }
        
        .label-dato {
            font-weight: bold;
            font-size: 9px;
            color: #555;
            display: block;
        }
        
        .valor-dato {
            font-size: 11px;
            color: #000;
            display: block;
            margin-top: 2px;
        }
        
        /* TABLA DE SERVICIOS */
        .tabla-servicios {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 10px;
        }
        
        .tabla-servicios thead {
            background: #2c3e50;
            color: white;
        }
        
        .tabla-servicios th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        .tabla-servicios td {
            padding: 10px 8px;
            border: 1px solid #ddd;
        }
        
        .tabla-servicios .col-cantidad { 
            width: 10%; 
            text-align: center; 
        }
        
        .tabla-servicios .col-descripcion { 
            width: 60%; 
        }
        
        .tabla-servicios .col-precio { 
            width: 15%; 
            text-align: right; 
        }
        
        .tabla-servicios .col-subtotal { 
            width: 15%; 
            text-align: right; 
        }
        
        /* TOTAL */
        .seccion-total {
            text-align: right;
            margin-top: 12px;
            padding: 12px;
            background: #f8f8f8;
            border: 2px solid #000;
        }
        
        .total-label {
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            min-width: 100px;
        }
        
        .total-monto {
            font-size: 20px;
            font-weight: bold;
            color: #c41e3a;
            display: inline-block;
        }
        
        /* DETALLES DE PAGO */
        .detalles-pago {
            margin-top: 12px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .detalles-pago .titulo-seccion {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 6px;
            color: #c41e3a;
        }
        
        /* FOOTER */
        .footer-ticket {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
            line-height: 1.6;
        }
        
        .footer-advertencia {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 8px;
            margin-top: 10px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            line-height: 1.5;
        }
        
        .footer-rojo {
            background: #c41e3a;
            color: white;
            padding: 8px;
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        @media print {
            body { margin: 0; }
            .page { margin: 0; padding: 5mm; }
            .ticket { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="ticket">
            <!-- ENCABEZADO -->
            <div class="header-section">
                <!-- Logo a la izquierda más pequeño -->
                <div class="header-logo">
                    <div class="logo-container">
                        <img src="{{ asset('images/rocio.jpg') }}" alt="Logo Rocío Travel" class="logo-img">
                    </div>
                </div>
                
                <!-- Información CENTRADA y más compacta -->
                <div class="header-center">
                    <div class="logo-empresa">ROCÍO TRAVEL</div>
                    <div class="subtitulo">VENTA DE PASAJES FLUVIALES</div>
                    <div class="info-empresa">
                        <p class="destinos"><strong>IQUITOS - YURIMAGUAS - PUCALLPA - SANTA ROSA - INTUTO - SAN LORENZO,</strong></p>
                        <p class="destinos"><strong>TROMPETEROS - PANTOJA - REQUENA - Y PUERTOS INTERMEDIOS</strong></p>
                        <p style="margin-top: 5px;"><strong>Dirección:</strong> Calle. Pevas N° 366</p>
                        <p><strong>Correo:</strong> travelrocio19@gmail.com</p>
                        <p><strong>Contacto:</strong> +51901978379</p>
                        <p><strong>Yape:</strong> 989667653</p>
                        <p class="ubicacion">IQUITOS - MAYNAS - LORETO</p>
                    </div>
                </div>
            </div>
            
            <!-- FECHA DE EMISIÓN Y MEDIO DE PAGO -->
            <div class="fecha-emision">
                <strong>Fecha de emisión:</strong> {{ $fecha_emision }} | 
                <strong>Medio de pago:</strong> {{ $medio_pago ?? 'EFECTIVO' }}
            </div>
            
            <!-- DATOS DEL CLIENTE -->
            <div class="datos-pasaje">
                <div class="fila-datos">
                    <div class="columna-dato col-75">
                        <span class="label-dato">CLIENTE:</span>
                        <span class="valor-dato">{{ strtoupper($cliente['nombre'] ?? 'SIN ESPECIFICAR') }}</span>
                    </div>
                    <div class="columna-dato col-25">
                        <span class="label-dato">Doc. Identidad:</span>
                        <span class="valor-dato">{{ $cliente['documento'] ?? '---' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- TABLA DE SERVICIOS -->
            <table class="tabla-servicios">
                <thead>
                    <tr>
                        <th class="col-cantidad">Cantidad</th>
                        <th class="col-descripcion">Descripción</th>
                        <th class="col-precio">Precio Unitario</th>
                        <th class="col-subtotal">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="col-cantidad">1</td>
                        <td class="col-descripcion">
                            <strong>{{ strtoupper($descripcion ?? 'PASAJE FLUVIAL') }}</strong>
                            @if(!empty($embarcacion))
                            <br>Embarcación: {{ strtoupper($embarcacion) }}
                            @endif
                            @if(!empty($puerto_embarque))
                            <br>Puerto: {{ strtoupper($puerto_embarque) }}
                            @endif
                        </td>
                        <td class="col-precio">S/ {{ number_format($total ?? 0, 2) }}</td>
                        <td class="col-subtotal">S/ {{ number_format($total ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- TOTAL -->
            <div class="seccion-total">
                <span class="total-label">Total: S/</span>
                <span class="total-monto">{{ number_format($total ?? 0, 2) }}</span>
            </div>
            
            <!-- DETALLES DE PAGO -->
            @if(!empty($detalles_pago))
            <div class="detalles-pago">
                <div class="titulo-seccion">Nota:</div>
                <div style="font-size: 9px;">{{ $detalles_pago }}</div>
            </div>
            @endif
            
            <!-- DETALLES DEL VIAJE -->
            <div class="detalles-pago">
                <div class="titulo-seccion">Detalles del viaje:</div>
                <div class="fila-datos">
                    <div class="columna-dato col-33">
                        <span class="label-dato">Fecha de viaje:</span>
                        <span class="valor-dato">{{ $fecha_viaje ?? '---' }}</span>
                    </div>
                    <div class="columna-dato col-33">
                        <span class="label-dato">Hora de embarque:</span>
                        <span class="valor-dato">{{ $hora_embarque ?? '---' }}</span>
                    </div>
                    <div class="columna-dato col-33">
                        <span class="label-dato">Hora de salida:</span>
                        <span class="valor-dato">{{ $hora_salida ?? '---' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- ADVERTENCIA -->
            <div class="footer-advertencia">
                La empresa no aceptará devoluciones una vez realizada la venta y separado el cupo<br>
                en caso que la embarcación haya partido y Ud. no aborde,<br>
                perderá su derecho a viajar y el valor de su pasaje.
            </div>
            
            <!-- EQUIPAJE -->
            <div class="footer-rojo">
                EQUIPAJE: 15KG POR PASAJERO
            </div>
            
            <!-- FOOTER -->
            <div class="footer-ticket">
                <p>La empresa se responsabiliza solamente de los pasajeros dueños de este pasaje.</p>
                <p>El pasajero de embarque y saca siempre su propio equipaje.</p>
            </div>
            
            <!-- CAMBIO DE TICKET -->
            <div class="footer-rojo">
                ESTE TICKET PUEDE SER CAMBIADO POR BOLETA DE VENTA O FACTURA
            </div>
        </div>
    </div>
</body>
</html>