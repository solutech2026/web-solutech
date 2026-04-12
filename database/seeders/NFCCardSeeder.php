<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NFCCardSeeder extends Seeder
{
    public function run()
    {
        // Obtener IDs de personas existentes
        $persons = DB::table('persons')->pluck('id')->toArray();
        
        $cards = [
            [
                'card_code' => 'NFC-ABC123',
                'card_uid' => '04:7E:3B:2A:1F:8C:3D',
                'assigned_to' => $persons[0] ?? null,  // ← Cambiado: person_id → assigned_to
                'assigned_at' => Carbon::now(),
                'status' => 'active',
                'notes' => 'Tarjeta CEO - Acceso a todas las áreas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-DEF456',
                'card_uid' => '12:34:56:78:90:AB:CD',
                'assigned_to' => null,  // ← Cambiado: person_id → assigned_to
                'assigned_at' => null,
                'status' => 'active',
                'notes' => 'Tarjeta de respaldo - Sin asignar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-GHI789',
                'card_uid' => 'AA:BB:CC:DD:EE:FF:11',
                'assigned_to' => $persons[1] ?? null,  // ← Cambiado: person_id → assigned_to
                'assigned_at' => Carbon::now(),
                'status' => 'active',
                'notes' => 'Acceso laboratorio y servidores',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-JKL012',
                'card_uid' => '22:33:44:55:66:77:88',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'active',
                'notes' => 'Tarjeta disponible para nuevo personal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-MNO345',
                'card_uid' => '99:88:77:66:55:44:33',
                'assigned_to' => $persons[2] ?? null,
                'assigned_at' => Carbon::now()->subDays(10),
                'status' => 'active',
                'notes' => 'Tarjeta de gerente de ventas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-PQR678',
                'card_uid' => '1A:2B:3C:4D:5E:6F:7G',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'lost',
                'notes' => 'Tarjeta reportada como perdida',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        foreach ($cards as $card) {
            DB::table('nfc_cards')->insert($card);
        }
        
        $this->command->info('✅ Tarjetas NFC creadas: ' . count($cards));
    }
}