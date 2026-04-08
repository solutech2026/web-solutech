<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NFCCardSeeder extends Seeder
{
    public function run()
    {
        DB::table('nfc_cards')->insert([
            [
                'card_code' => 'NFC-ABC123',
                'person_id' => 1,
                'status' => 'active',
                'notes' => 'Tarjeta CEO - Acceso a todas las áreas',
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'card_code' => 'NFC-DEF456',
                'person_id' => null,
                'status' => 'active',
                'notes' => 'Tarjeta de respaldo - Sin asignar',
                'assigned_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'card_code' => 'NFC-GHI789',
                'person_id' => 2,
                'status' => 'active',
                'notes' => 'Acceso laboratorio y servidores',
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}