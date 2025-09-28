<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition()
    {
        return [
            'numero_documento' => $this->faker->unique()->numerify('########'),
            'nombre' => strtoupper($this->faker->firstName . ' ' . $this->faker->lastName . ' ' . $this->faker->lastName),
            'contacto' => $this->faker->numerify('9########'),
            'nacionalidad' => $this->faker->randomElement(['PERUANA', 'BRASILEÑA', 'COLOMBIANA']),
            'activo' => true
        ];
    }
}