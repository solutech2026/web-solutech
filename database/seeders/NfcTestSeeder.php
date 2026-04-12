<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class NfcTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ============================================
        // TARJETAS NFC
        // ============================================
        
        // Obtener IDs de personas existentes para asignar tarjetas
        $persons = DB::table('persons')->pluck('id')->toArray();
        
        $cards = [
            // Tarjetas asignadas
            [
                'card_code' => 'NFC-EMP-001',
                'card_uid' => '04:7E:3B:2A:1F:8C:3D',
                'assigned_to' => $persons[0] ?? null,
                'assigned_at' => Carbon::now()->subDays(30),
                'status' => 'active',
                'notes' => 'Tarjeta de empleado - Acceso a oficinas',
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-EMP-002',
                'card_uid' => '12:34:56:78:90:AB:CD',
                'assigned_to' => $persons[1] ?? null,
                'assigned_at' => Carbon::now()->subDays(25),
                'status' => 'active',
                'notes' => 'Gerente de Ventas - Acceso total',
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-EMP-003',
                'card_uid' => 'AA:BB:CC:DD:EE:FF:11',
                'assigned_to' => $persons[2] ?? null,
                'assigned_at' => Carbon::now()->subDays(20),
                'status' => 'active',
                'notes' => 'Desarrollador - Acceso a laboratorio',
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-STU-001',
                'card_uid' => '22:33:44:55:66:77:88',
                'assigned_to' => $persons[3] ?? null,
                'assigned_at' => Carbon::now()->subDays(15),
                'status' => 'active',
                'notes' => 'Estudiante - Acceso a biblioteca',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-STU-002',
                'card_uid' => '99:88:77:66:55:44:33',
                'assigned_to' => $persons[4] ?? null,
                'assigned_at' => Carbon::now()->subDays(10),
                'status' => 'active',
                'notes' => 'Estudiante destacado',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-TCH-001',
                'card_uid' => '1A:2B:3C:4D:5E:6F:7G',
                'assigned_to' => $persons[5] ?? null,
                'assigned_at' => Carbon::now()->subDays(5),
                'status' => 'active',
                'notes' => 'Docente - Acceso a salones',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now(),
            ],
            
            // Tarjetas sin asignar (disponibles)
            [
                'card_code' => 'NFC-AVAIL-001',
                'card_uid' => 'AB:CD:EF:12:34:56:78',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'active',
                'notes' => 'Tarjeta disponible para nuevo empleado',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-AVAIL-002',
                'card_uid' => 'FE:DC:BA:98:76:54:32',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'active',
                'notes' => 'Tarjeta de repuesto',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-AVAIL-003',
                'card_uid' => '11:22:33:44:55:66:77',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'active',
                'notes' => 'Tarjeta para nuevo estudiante',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-AVAIL-004',
                'card_uid' => '88:99:AA:BB:CC:DD:EE',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'active',
                'notes' => 'Tarjeta de reserva',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now(),
            ],
            
            // Tarjetas inactivas
            [
                'card_code' => 'NFC-LOST-001',
                'card_uid' => 'FF:EE:DD:CC:BB:AA:99',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'lost',
                'notes' => 'Tarjeta reportada como perdida',
                'created_at' => Carbon::now()->subDays(60),
                'updated_at' => Carbon::now(),
            ],
            [
                'card_code' => 'NFC-DAMAGED-001',
                'card_uid' => '00:11:22:33:44:55:66',
                'assigned_to' => null,
                'assigned_at' => null,
                'status' => 'damaged',
                'notes' => 'Tarjeta dañada - No funciona correctamente',
                'created_at' => Carbon::now()->subDays(50),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        // Insertar tarjetas y guardar los IDs
        $cardIds = [];
        foreach ($cards as $card) {
            $id = DB::table('nfc_cards')->insertGetId($card);
            $cardIds[] = $id;
        }
        
        // ============================================
        // REGISTROS DE ACCESO (para pruebas)
        // ============================================
        
        $accessLogs = [];
        $statuses = ['granted', 'denied'];
        $gates = ['Puerta Principal', 'Puerta Lateral', 'Oficina 101', 'Laboratorio', 'Biblioteca', 'Estacionamiento'];
        $methods = ['nfc', 'manual', 'qr'];
        
        for ($i = 0; $i < 100; $i++) {
            // Obtener un card_id válido de los insertados
            $cardId = $cardIds[array_rand($cardIds)];
            $card = DB::table('nfc_cards')->find($cardId);
            
            $accessLogs[] = [
                'company_id' => null,
                'person_id' => $card && $card->assigned_to ? $card->assigned_to : null,
                'nfc_card_id' => $cardId,
                'card_id' => $cardId,
                'access_type' => 'entry',
                'access_time' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                'gate' => $gates[array_rand($gates)],
                'verification_method' => $methods[array_rand($methods)],
                'status' => $statuses[array_rand($statuses)],
                'notes' => rand(1, 10) > 8 ? 'Acceso registrado correctamente' : null,
                'ip_address' => '192.168.1.' . rand(1, 255),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Ordenar por fecha
        usort($accessLogs, function($a, $b) {
            return $a['access_time'] <=> $b['access_time'];
        });
        
        foreach ($accessLogs as $log) {
            DB::table('access_logs')->insert($log);
        }
        
        // ============================================
        // ACTUALIZAR ÚLTIMO ACCESO DE PERSONAS
        // ============================================
        
        foreach ($persons as $personId) {
            $lastAccess = DB::table('access_logs')
                ->where('person_id', $personId)
                ->where('status', 'granted')
                ->orderBy('access_time', 'desc')
                ->first();
            
            if ($lastAccess) {
                DB::table('persons')
                    ->where('id', $personId)
                    ->update(['last_access_at' => $lastAccess->access_time]);
            }
        }
        
        $this->command->info('✅ Datos de prueba creados:');
        $this->command->info("   - " . count($cards) . " tarjetas NFC");
        $this->command->info("   - " . count($accessLogs) . " registros de acceso");
    }
}