<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetallePasaje;

class DetallePasajeSeeder extends Seeder
{
    public function run()
    {
        $detalles = [
            [
                'destino' => 'YURIMAGUAS',
                'ruta' => 'NAUTA - ALFONSO UGARTE',
                'cantidad' => 2,
                'precio_unitario' => 150.00
            ],
            [
                'destino' => 'PUCALLPA',
                'ruta' => 'IQUITOS - PUCALLPA',
                'cantidad' => 1,
                'precio_unitario' => 200.00
            ],
            [
                'destino' => 'OTROS',
                'ruta' => 'NAUTA - REQUENA',
                'cantidad' => 3,
                'precio_unitario' => 120.00
            ]
        ];

        foreach ($detalles as $detalle) {
            DetallePasaje::create($detalle);
        }
    }
}