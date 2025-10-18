<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evento;

class EventoSeeder extends Seeder
{
    public function run()
    {
        $eventos = ['Boda', 'Graduación', 'Fiesta', 'Exposicion Formal'];
        foreach ($eventos as $nombre) {
            Evento::create(['nombre' => $nombre]);
        }
    }
}
