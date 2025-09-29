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
            line-height: 1.2;
            color: #000;
            background: #f5f5f5;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .ticket-container {
            background: white;
            width: 80mm;
            max-width: 80mm;
            padding: 5mm;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .action-buttons {
            text-align: center;
            margin-bottom: 12px;
        }

        .btn {
            padding: 6px 12px;
            margin: 0 3px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
        }

        .btn-save {
            background: #007bff;
            color: white;
        }

        .btn-save:hover {
            background: #0056b3;
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

        .ticket-header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
        }

        .logo {
            width: 35px;
            height: 35px;
            margin-right: 8px;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #333;
            flex-shrink: 0;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .empresa-info {
            text-align: center;
        }

        .empresa-nombre {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .empresa-subtitulo {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            color: #2c5aa0;
        }

        .ticket-info {
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }

        .cliente-row {
            margin-bottom: 2px;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            line-height: 1.3;
        }

        .cliente-row strong {
            min-width: 80px;
            font-weight: bold;
        }

        .cliente-row span {
            text-align: right;
            flex: 1;
        }

        .productos-section {
            margin-bottom: 8px;
        }

        .productos-header {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 2px;
            font-weight: bold;
            font-size: 9px;
            display: grid;
            grid-template-columns: 22px 1fr 40px 40px;
            gap: 2px;
            text-align: center;
        }

        .producto-row {
            padding: 3px 2px;
            font-size: 9px;
            display: grid;
            grid-template-columns: 22px 1fr 40px 40px;
            gap: 2px;
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

        .total-section {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            margin-bottom: 8px;
            background: #f9f9f9;
        }

        .total-row {
            text-align: right;
            font-weight: bold;
            font-size: 13px;
            padding-right: 8px;
        }

        .viaje-info {
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }

        .viaje-row {
            margin-bottom: 2px;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            line-height: 1.3;
        }

        .viaje-row strong {
            min-width: 85px;
            font-weight: bold;
        }

        .viaje-row span {
            text-align: right;
            flex: 1;
        }

        .nota-section {
            margin-top: 4px;
            font-size: 8px;
            font-style: italic;
            background: #f0f0f0;
            padding: 3px;
            border-radius: 2px;
        }

        .ticket-footer {
            text-align: center;
            font-size: 8px;
            line-height: 1.2;
        }

        .politicas {
            margin-bottom: 6px;
            text-align: justify;
            padding: 4px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 2px;
        }

        .equipaje-info {
            text-align: center;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 9px;
            background: #d1ecf1;
            padding: 3px;
            border-radius: 2px;
        }

        .separador {
            text-align: center;
            margin: 6px 0;
            font-size: 9px;
            font-weight: bold;
        }

        .cambio-boleta {
            font-weight: bold;
            margin-top: 6px;
            font-size: 8px;
            background: #f8d7da;
            padding: 4px;
            border-radius: 2px;
            border: 1px solid #f5c6cb;
            text-align: center;
            line-height: 1.3;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .ticket-container {
                width: 80mm;
                max-width: 80mm;
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

        @media (max-width: 80mm) {
            .ticket-container {
                width: 58mm;
                font-size: 8px;
            }
            
            .empresa-nombre {
                font-size: 14px;
            }
            
            .logo {
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-print">Imprimir</button>
            <button onclick="guardarTicket()" class="btn btn-save">Guardar</button>
            <button onclick="window.close()" class="btn btn-close">Cerrar</button>
        </div>

        <div class="ticket-header">
            <div class="logo-section">
                <div class="logo">
                    <img src="{{ asset('images/rocio.jpg') }}" alt="Logo ROCÍO TRAVEL">
                </div>
                <div class="empresa-info">
                    <h1 class="empresa-nombre">ROCÍO TRAVEL</h1>
                    <h2 class="empresa-subtitulo">VENTA DE PASAJES FLUVIALES</h2>
                </div>
            </div>
        </div>

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
                <span>{{ $fecha_emision }} {{ date('h:i A', strtotime($hora_emision)) }}</span>
            </div>
            <div class="cliente-row">
                <strong>MEDIO PAGO:</strong>
                <span>{{ strtoupper($medio_pago) }}</span>
            </div>
        </div>

        <div class="productos-section">
            <div class="productos-header">
                <div>CANT</div>
                <div>DESCRIPCION</div>
                <div>P.UNIT</div>
                <div>SUB TOTAL</div>
            </div>
            
            <div class="producto-row">
                <div class="text-center">{{ $cantidad }}</div>
                <div class="text-left">{{ strtoupper($descripcion) }}</div>
                <div class="text-right">{{ number_format($precio_unitario, 2) }}</div>
                <div class="text-right">{{ number_format($subtotal, 2) }}</div>
            </div>
        </div>

        <div class="total-section">
            <div class="total-row">
                Total: S/ {{ number_format($total, 2) }}
            </div>
        </div>

        <div class="viaje-info">
            <div class="viaje-row">
                <strong>F. VIAJE:</strong>
                <span>{{ $fecha_viaje }}</span>
            </div>
            <div class="viaje-row">
                <strong>EMBARCACION:</strong>
                <span>{{ strtoupper($embarcacion) }}</span>
            </div>
            <div class="viaje-row">
                <strong>PUERTO EMBAR:</strong>
                <span>{{ strtoupper($puerto_embarque) }}</span>
            </div>
            <div class="viaje-row">
                <strong>H. EMBARQUE:</strong>
                <span>{{ date('h:i A', strtotime($hora_embarque)) }}</span>
            </div>
            <div class="viaje-row">
                <strong>H. SALIDA:</strong>
                <span>{{ date('h:i A', strtotime($hora_salida)) }}</span>
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
                ESTE TICKET PUEDE SER CAMBIADO POR<br>
                BOLETA DE VENTA O FACTURA
            </div>
        </div>
    </div>

    <script>
        function guardarTicket() {
            const ticketData = {
                numero_ticket: '{{ $numero_ticket ?? "" }}',
                cliente: {
                    nombre: '{{ $cliente["nombre"] ?? "" }}',
                    documento: '{{ $cliente["documento"] ?? "" }}',
                    contacto: '{{ $cliente["contacto"] ?? "" }}',
                    nacionalidad: '{{ $cliente["nacionalidad"] ?? "" }}'
                },
                fecha_emision: '{{ $fecha_emision ?? "" }}',
                hora_emision: '{{ $hora_emision ?? "" }}',
                medio_pago: '{{ $medio_pago ?? "" }}',
                cantidad: '{{ $cantidad ?? "" }}',
                descripcion: '{{ $descripcion ?? "" }}',
                precio_unitario: '{{ $precio_unitario ?? "" }}',
                subtotal: '{{ $subtotal ?? "" }}',
                total: '{{ $total ?? "" }}',
                fecha_viaje: '{{ $fecha_viaje ?? "" }}',
                embarcacion: '{{ $embarcacion ?? "" }}',
                puerto_embarque: '{{ $puerto_embarque ?? "" }}',
                hora_embarque: '{{ $hora_embarque ?? "" }}',
                hora_salida: '{{ $hora_salida ?? "" }}',
                nota: '{{ $nota ?? "" }}'
            };

            fetch('/api/guardar-ticket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(ticketData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ticket guardado exitosamente en el historial');
                    
                    if (window.opener && window.opener.postMessage) {
                        window.opener.postMessage({
                            type: 'TICKET_GUARDADO',
                            data: ticketData
                        }, '*');
                    }
                    
                    setTimeout(() => {
                        window.close();
                    }, 1000);
                } else {
                    alert('Error al guardar el ticket: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
        }

        function cerrarVentana() {
            window.close();
        }
        
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.close();
            }
        });

        window.addEventListener('message', function(event) {
            if (event.data.type === 'ACTUALIZAR_HISTORIAL') {
                console.log('Historial actualizado');
            }
        });
    </script>
</body>
</html>