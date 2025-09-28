<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Pasaje - {{ $numero_ticket }}</title>
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
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            border: 3px solid #000;
        }

        /* Header */
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .logo {
            width: 120px;
            height: 120px;
            flex-shrink: 0;
            margin-right: 20px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .empresa-info {
            flex: 1;
            text-align: left;
        }

        .empresa-info h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .empresa-info h2 {
            font-size: 14px;
            color: #2c5aa0;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .direcciones {
            font-size: 10px;
            line-height: 1.3;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: 500;
        }

        .contacto-info {
            font-size: 10px;
            line-height: 1.3;
        }

        .contacto-info div {
            margin-bottom: 2px;
        }

        .ubicacion {
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
            text-transform: uppercase;
        }

        .emitido {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        /* Separador de línea horizontal después del header */
        .separador {
            border-bottom: 1px solid #000;
            margin: 20px 0;
        }

        /* Información del ticket */
        .ticket-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .ticket-left {
            flex: 1;
        }

        .ticket-right {
            text-align: right;
            min-width: 200px;
        }

        .ticket-info div {
            margin-bottom: 4px;
            font-size: 11px;
        }

        /* Información del cliente */
        .cliente-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .cliente-field {
            display: flex;
            align-items: baseline;
            margin-bottom: 8px;
        }

        .cliente-field strong {
            min-width: 110px;
            display: inline-block;
            font-size: 11px;
        }

        .cliente-field span {
            font-size: 11px;
        }

        /* Tabla de detalles */
        .tabla-container {
            margin-bottom: 20px;
        }

        .tabla-detalles {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }

        .tabla-detalles th,
        .tabla-detalles td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        .tabla-detalles th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 12px;
        }

        .descripcion {
            text-align: left !important;
        }

        .precio {
            text-align: right !important;
            font-weight: bold;
        }

        /* Total */
        .total-section {
            text-align: right;
            margin-bottom: 30px;
            padding-top: 10px;
        }

        .total-row {
            font-size: 16px;
            font-weight: bold;
        }

        /* Información del viaje */
        .viaje-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            border-top: 1px solid #000;
            padding-top: 15px;
        }

        .viaje-field {
            display: flex;
            align-items: baseline;
            margin-bottom: 8px;
        }

        .viaje-field strong {
            min-width: 130px;
            display: inline-block;
            font-size: 11px;
        }

        .viaje-field span {
            font-size: 11px;
            font-weight: normal;
        }

        /* Footer */
        .footer {
            border-top: 2px solid #000;
            padding-top: 15px;
            text-align: center;
        }

        .politicas {
            font-size: 9px;
            line-height: 1.3;
            margin-bottom: 15px;
            text-align: center;
            color: #d32f2f;
            font-weight: 500;
        }

        .info-adicional {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .equipaje {
            background-color: #d32f2f;
            color: white;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .cancelado {
            background-color: #d32f2f;
            color: white;
            padding: 5px 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .cambio-boleta {
            font-size: 11px;
            font-weight: bold;
            color: #d32f2f;
            text-align: center;
            margin-top: 10px;
            text-transform: uppercase;
        }

        /* Posición relativa para el contenedor principal */
        .header-container {
            position: relative;
        }

        /* Utilidades */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Responsive para impresión */
        @media print {
            body { margin: 0; }
            .container { border: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header con posicionamiento relativo para el "Emitido" -->
        <div class="header-container">
            <div class="emitido">Emitido: {{ $numero_ticket }}</div>
            
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('images/rocio.jpg') }}" alt="Logo ROCÍO TRAVEL">
                </div>
                <div class="empresa-info">
                    <h1>ROCÍO TRAVEL</h1>
                    <h2>VENTA DE PASAJES FLUVIALES</h2>
                    <div class="direcciones">
                        IQUITOS - YURIMAGUAS - PUCALLPA - SANTA ROSA - INTUTO - SAN LORENZO,<br>
                        TROMPETEROS - PANTOJA - REQUENA - Y PUERTOS INTERMEDIOS
                    </div>
                    <div class="contacto-info">
                        <div>Dirección: Calle. Pevas N° 366</div>
                        <div>Correo: travelrocio19@gmail.com</div>
                        <div>Contacto: +51901978379</div>
                        <div>Yape: 989667653</div>
                        <div class="ubicacion">IQUITOS - MAYNAS - LORETO</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="separador"></div>

        <!-- Información del ticket -->
        <div class="ticket-info">
            <div class="ticket-left">
                <div><strong>Fecha de emisión:</strong> {{ $fecha_emision }} {{ $hora_emision }}</div>
                <div><strong>Medio de pago:</strong> {{ ucfirst($medio_pago) }}</div>
            </div>
            <div class="ticket-right">
                <div><strong>{{ $fecha_emision }}</strong></div>
                <div><strong>{{ $hora_emision }}</strong></div>
                <div><strong>Por operador: {{ $operador }}</strong></div>
                <div><strong>Fecha de emisión:</strong> {{ $fecha_emision }}</div>
                <div><strong>Hora de emisión:</strong> {{ $hora_emision }}</div>
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="cliente-info">
            <div class="cliente-field">
                <strong>Señor(a):</strong>
                <span>{{ $cliente['nombre'] ?? 'CLIENTE' }}</span>
            </div>
            <div></div>
            <div class="cliente-field">
                <strong>Doc. Identidad:</strong>
                <span>{{ $cliente['documento'] ?? '' }}</span>
            </div>
            <div></div>
            <div class="cliente-field">
                <strong>Contacto:</strong>
                <span>{{ $cliente['contacto'] ?? '' }}</span>
            </div>
            <div></div>
            <div class="cliente-field">
                <strong>Nacionalidad:</strong>
                <span>{{ $cliente['nacionalidad'] ?? '' }}</span>
            </div>
            <div></div>
        </div>

        <!-- Tabla de detalles -->
        <div class="tabla-container">
            <table class="tabla-detalles">
                <thead>
                    <tr>
                        <th style="width: 10%">Cantidad</th>
                        <th style="width: 50%">Descripción</th>
                        <th style="width: 20%">Precio Unitario</th>
                        <th style="width: 20%">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pasaje['cantidad'] }}</td>
                        <td class="descripcion">{{ $pasaje['descripcion'] }}</td>
                        <td class="precio">{{ $pasaje['precio_unitario'] }}</td>
                        <td class="precio">{{ $pasaje['subtotal'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="total-section">
            <div class="total-row">Total: S/. {{ $total }}</div>
        </div>

        <!-- Información del viaje -->
        <div class="viaje-info">
            <div class="viaje-field">
                <strong>Fecha de viaje:</strong>
                <span>{{ $viaje['fecha_viaje'] }}</span>
            </div>
            <div class="viaje-field">
                <strong>Nota:</strong>
                <span>{{ $nota ?? '' }}</span>
            </div>
            <div class="viaje-field">
                <strong>Embarcación:</strong>
                <span>{{ $viaje['embarcacion'] }}</span>
            </div>
            <div></div>
            <div class="viaje-field">
                <strong>Puerto de embarque:</strong>
                <span>{{ $viaje['puerto_embarque'] }}</span>
            </div>
            <div></div>
            <div class="viaje-field">
                <strong>Hora de embarque:</strong>
                <span>{{ $viaje['hora_embarque'] }}</span>
            </div>
            <div></div>
            <div class="viaje-field">
                <strong>Hora de salida:</strong>
                <span>{{ $viaje['hora_salida'] }}</span>
            </div>
            <div></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="politicas">
                La empresa no aceptará devoluciones una vez realizada la venta y separado el cupo;<br>
                en caso que la embarcación haya partido y Ud. no abordó,<br>
                perderá su derecho a viajar y el valor de su pasaje.
            </div>
            
            <div class="info-adicional">
                <div class="equipaje">Equipaje: {{ $equipaje ?? '15Kg por pasajero' }}</div>
                <div class="cancelado">Cancelado</div>
            </div>
            
            <div class="cambio-boleta">{{ $cambio_boleta ?? 'ESTE TICKET PUEDE SER CAMBIADO POR BOLETA DE VENTA O FACTURA' }}</div>
        </div>
    </div>
</body>
</html>