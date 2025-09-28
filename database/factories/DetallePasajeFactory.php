<?php

namespace Database\Factories;

use App\Models\DetallePasaje;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetallePasajeFactory extends Factory
{
    protected $model = DetallePasaje::class;

    public function definition()
    {
        $destinos = ['PUCALLPA', 'YURIMAGUAS', 'FRONTERA', 'OTROS'];
        $destino = $this->faker->randomElement($destinos);
        
        $rutasPorDestino = [
            'PUCALLPA' => ['NAUTA - PUCALLPA', 'IQUITOS - PUCALLPA'],
            'YURIMAGUAS' => ['NAUTA - ALFONSO UGARTE', 'IQUITOS - YURIMAGUAS'],
            'FRONTERA' => ['NAUTA - FRONTERA', 'IQUITOS - FRONTERA'],
            'OTROS' => [
                'NAUTA - REQUENA', 'NAUTA - SAN LORENZO', 'NAUTA - SANTA ROSA',
                'IQUITOS - REQUENA', 'IQUITOS - TROMPETEROS', 'IQUITOS - INTUTO'
            ]
        ];

        $ruta = $this->faker->randomElement($rutasPorDestino[$destino]);
        $precioUnitario = $this->faker->randomFloat(2, 80, 300);

        return [
            'destino' => $destino,
            'ruta' => $ruta,
            'cantidad' => 1, // Siempre 1
            'precio_unitario' => $precioUnitario,
            'activo' => true
        ];
    }
}