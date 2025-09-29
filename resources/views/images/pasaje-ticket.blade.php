<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $tipo_documento }} - {{ $numero_boleta }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: white; 
            font-size: 14px;
        }
        .boleta { 
            max-width: 800px; 
            margin: 0 auto; 
            border: 1px solid #ccc; 
            padding: 20px; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        h1 { 
            color: #2c3e50; 
            margin: 0; 
            font-size: 22px;
        }
        .datos { 
            margin: 10px 0; 
        }
        .total { 
            font-size: 18px; 
            font-weight: bold; 
            text-align: right; 
            margin-top: 20px; 
        }
    </style>
</head>
<body>
    <div class="boleta">
        <div class="header">
            <h1>{{ $empresa }}</h1>
            <p><strong>{{ $tipo_documento }}</strong></p>
            <p>N° {{ $numero_boleta }}</p>
            <p>{{ $fecha_emision }} - {{ $hora_emision }}</p>
        </div>
        
        <div class="datos">
            <strong>CLIENTE:</strong><br>
            {{ $cliente['nombre'] ?? '---' }}<br>
            Doc: {{ $cliente['documento'] ?? '---' }}
        </div>
        
        <div class="datos">
            <strong>VIAJE:</strong><br>
            {{ $descripcion ?? '---' }}<br>
            Embarcación: {{ $embarcacion ?? '---' }}<br>
            Puerto: {{ $puerto_embarque ?? '---' }}
        </div>
        
        <div class="datos">
            <strong>PAGO:</strong>
            {{ $medio_pago ?? '---' }}
            @if(!empty($detalles_pago))
                <br>{{ $detalles_pago }}
            @endif
        </div>
        
        <div class="total">
            TOTAL: S/ {{ number_format($total ?? 0, 2) }}
        </div>
    </div>
</body>
</html>
