<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        $clientes = [
            [
                'numero_documento' => '12345678',
                'nombre' => 'JUAN CARLOS MENDOZA RIVERA',
                'contacto' => '987654321',
                'nacionalidad' => 'PERUANA'
            ],
            [
                'numero_documento' => '87654321',
                'nombre' => 'MARIA ELENA RODRIGUEZ VASQUEZ',
                'contacto' => '912345678',
                'nacionalidad' => 'PERUANA'
            ],
            [
                'numero_documento' => '11223344',
                'nombre' => 'CARLOS ARMANDO MERMAO NAVARRO',
                'contacto' => '998877665',
                'nacionalidad' => 'PERUANA'
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}