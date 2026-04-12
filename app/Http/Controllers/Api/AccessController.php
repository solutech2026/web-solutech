<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcCard;
use App\Models\Person;
use App\Models\AccessLog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AccessController extends Controller
{
    /**
     * Validar acceso con tarjeta NFC (lector en tiempo real)
     * POST /api/access/validate
     */
    public function validateAccess(Request $request)
    {
        $request->validate([
            'card_code' => 'required|string',
            'gate' => 'nullable|string',
            'access_type' => 'nullable|in:entry,exit',
            'reader_name' => 'nullable|string',
            'reader_ip' => 'nullable|ip'
        ]);

        $cardCode = strtoupper($request->card_code);
        $gate = $request->gate ?? 'Puerta Principal';
        $accessType = $request->access_type ?? 'entry';
        $readerName = $request->reader_name ?? 'Lector NFC';
        $readerIp = $request->reader_ip ?? $request->ip();

        // Buscar tarjeta por código o UID
        $card = NfcCard::where('card_code', $cardCode)
            ->orWhere('card_uid', $cardCode)
            ->where('status', 'active')
            ->first();

        // Tarjeta no válida
        if (!$card) {
            return $this->logAndRespond(
                null, null, 'denied', 
                'Tarjeta no válida o inactiva', 
                $gate, $accessType, $readerIp, $readerName, $cardCode
            );
        }

        // Verificar persona asociada
        if (!$card->assigned_to) {
            return $this->logAndRespond(
                $card, null, 'denied', 
                'Tarjeta no asociada a ninguna persona', 
                $gate, $accessType, $readerIp, $readerName, $cardCode
            );
        }

        $person = Person::find($card->assigned_to);

        // Verificar si la persona existe
        if (!$person) {
            return $this->logAndRespond(
                $card, null, 'denied', 
                'Persona no encontrada', 
                $gate, $accessType, $readerIp, $readerName, $cardCode
            );
        }

        // Verificar si la persona está activa
        if (!$person->is_active) {
            return $this->logAndRespond(
                $card, $person, 'denied', 
                'Persona inactiva en el sistema', 
                $gate, $accessType, $readerIp, $readerName, $cardCode
            );
        }

        // Verificar horario de acceso según categoría
        $scheduleCheck = $this->checkScheduleAccess($person);
        if (!$scheduleCheck['allowed']) {
            return $this->logAndRespond(
                $card, $person, 'denied', 
                $scheduleCheck['reason'], 
                $gate, $accessType, $readerIp, $readerName, $cardCode
            );
        }

        // Verificar intentos fallidos recientes
        $recentDenied = AccessLog::where('nfc_card_id', $card->id)
            ->where('status', 'denied')
            ->where('access_time', '>=', now()->subMinutes(5))
            ->count();

        if ($recentDenied >= 5) {
            return response()->json([
                'success' => false,
                'status' => 'blocked',
                'message' => 'Demasiados intentos fallidos. Espere 5 minutos.',
                'code' => 'TOO_MANY_ATTEMPTS'
            ], 429);
        }

        // Verificar entrada duplicada en corto tiempo
        $lastAccess = AccessLog::where('person_id', $person->id)
            ->where('access_type', $accessType)
            ->where('access_time', '>=', now()->subMinutes(1))
            ->latest('access_time')
            ->first();

        if ($lastAccess && $lastAccess->status === 'granted') {
            return $this->logAndRespond(
                $card, $person, 'denied', 
                'Acceso duplicado en menos de 1 minuto', 
                $gate, $accessType, $readerIp, $readerName, $cardCode
            );
        }

        // ACCESO PERMITIDO
        $access = $this->logAndRespond(
            $card, $person, 'granted', 
            'Acceso permitido', 
            $gate, $accessType, $readerIp, $readerName, $cardCode
        );

        // Actualizar último acceso de la tarjeta
        $card->last_used_at = now();
        $card->save();

        // Actualizar último acceso de la persona
        $person->last_access_at = now();
        $person->save();

        // Preparar respuesta
        return response()->json([
            'success' => true,
            'status' => 'granted',
            'message' => 'Acceso permitido',
            'access_id' => $access->id,
            'access_time' => now()->toDateTimeString(),
            'person' => [
                'id' => $person->id,
                'name' => $person->name,
                'lastname' => $person->lastname,
                'full_name' => $person->full_name,
                'category' => $person->category,
                'subcategory' => $person->subcategory,
                'document_id' => $person->document_id,
                'email' => $person->email,
                'phone' => $person->phone,
                'company' => $person->company ? $person->company->name : null,
                'bio_url' => $person->bio_url ? url($person->bio_url) : null,
            ],
            'card' => [
                'id' => $card->id,
                'code' => $card->card_code,
                'uid' => $card->card_uid,
                'notes' => $card->notes
            ],
            'gate' => $gate,
            'reader' => [
                'name' => $readerName,
                'ip' => $readerIp
            ]
        ]);
    }

    /**
     * Verificar horario de acceso según categoría de la persona
     */
    private function checkScheduleAccess($person)
    {
        $now = now();
        $hour = (int)$now->format('H');
        $minute = (int)$now->format('i');
        $hourMinute = $hour + ($minute / 60);
        $isWeekend = $now->isWeekend();
        $dayOfWeek = strtolower($now->format('l'));

        // Empleados: horario laboral L-V 8am - 6pm
        if ($person->category === 'employee') {
            if ($isWeekend) {
                return ['allowed' => false, 'reason' => 'No hay acceso los fines de semana'];
            }
            if ($hourMinute < 8 || $hourMinute > 18) {
                return ['allowed' => false, 'reason' => 'Fuera de horario laboral (8:00 - 18:00)'];
            }
            return ['allowed' => true, 'reason' => ''];
        }

        // Estudiantes: horario escolar L-V 7:30am - 3:30pm
        if ($person->subcategory === 'student') {
            if ($isWeekend) {
                return ['allowed' => false, 'reason' => 'No hay clases los fines de semana'];
            }
            if ($hourMinute < 7.5 || $hourMinute > 15.5) {
                return ['allowed' => false, 'reason' => 'Fuera de horario escolar (7:30 - 15:30)'];
            }
            return ['allowed' => true, 'reason' => ''];
        }

        // Docentes: horario extendido L-V 7am - 6pm
        if ($person->subcategory === 'teacher') {
            if ($isWeekend) {
                return ['allowed' => false, 'reason' => 'No hay actividades los fines de semana'];
            }
            if ($hourMinute < 7 || $hourMinute > 18) {
                return ['allowed' => false, 'reason' => 'Fuera de horario docente (7:00 - 18:00)'];
            }
            return ['allowed' => true, 'reason' => ''];
        }

        // Administrativo: horario administrativo L-V 8am - 5pm
        if ($person->subcategory === 'administrative') {
            if ($isWeekend) {
                return ['allowed' => false, 'reason' => 'Oficina cerrada los fines de semana'];
            }
            if ($hourMinute < 8 || $hourMinute > 17) {
                return ['allowed' => false, 'reason' => 'Fuera de horario administrativo (8:00 - 17:00)'];
            }
            return ['allowed' => true, 'reason' => ''];
        }

        // Por defecto, acceso permitido
        return ['allowed' => true, 'reason' => ''];
    }

    /**
     * Registrar acceso y retornar respuesta
     */
    private function logAndRespond($card, $person, $status, $reason, $gate, $accessType, $readerIp, $readerName, $cardCode)
    {
        $accessLog = AccessLog::create([
            'company_id' => $person?->company_id ?? $card?->assignedPerson?->company_id,
            'person_id' => $person?->id ?? $card?->assigned_to,
            'nfc_card_id' => $card?->id,
            'access_type' => $accessType,
            'verification_method' => 'nfc',
            'access_time' => now(),
            'status' => $status,
            'gate' => $gate,
            'ip_address' => $readerIp,
            'reason' => $reason,
            'metadata' => json_encode([
                'user_agent' => request()->userAgent(),
                'card_code' => $cardCode,
                'reader_name' => $readerName,
                'version' => '1.0'
            ])
        ]);

        if ($status === 'granted') {
            return $accessLog;
        }

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $reason,
            'code' => $this->getErrorCode($reason),
            'access_time' => now()->toDateTimeString()
        ], 403);
    }

    /**
     * Obtener código de error según el motivo
     */
    private function getErrorCode($reason)
    {
        $codes = [
            'Tarjeta no válida o inactiva' => 'INVALID_CARD',
            'Tarjeta no asociada a ninguna persona' => 'UNASSIGNED_CARD',
            'Persona inactiva en el sistema' => 'INACTIVE_PERSON',
            'Fuera de horario laboral' => 'OUT_OF_SCHEDULE',
            'Fuera de horario escolar' => 'OUT_OF_SCHEDULE',
            'Fuera de horario docente' => 'OUT_OF_SCHEDULE',
            'Fuera de horario administrativo' => 'OUT_OF_SCHEDULE',
            'No hay acceso los fines de semana' => 'WEEKEND_ACCESS',
            'Acceso duplicado en menos de 1 minuto' => 'DUPLICATE_ACCESS'
        ];
        
        return $codes[$reason] ?? 'ACCESS_DENIED';
    }

    /**
     * Obtener logs de acceso (para API)
     * GET /api/access/logs
     */
    public function getLogs(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:500',
            'company_id' => 'nullable|exists:companies,id',
            'status' => 'nullable|in:granted,denied',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'person_id' => 'nullable|exists:persons,id',
            'card_id' => 'nullable|exists:nfc_cards,id'
        ]);

        $limit = $request->get('limit', 50);
        $companyId = $request->get('company_id');
        $status = $request->get('status');
        $from = $request->get('from');
        $to = $request->get('to');
        $personId = $request->get('person_id');
        $cardId = $request->get('card_id');

        $query = AccessLog::with(['person', 'nfcCard', 'company'])
            ->orderBy('access_time', 'desc');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($from) {
            $query->whereDate('access_time', '>=', $from);
        }

        if ($to) {
            $query->whereDate('access_time', '<=', $to);
        }

        if ($personId) {
            $query->where('person_id', $personId);
        }

        if ($cardId) {
            $query->where('nfc_card_id', $cardId);
        }

        $logs = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'total' => $logs->count(),
            'limit' => $limit,
            'logs' => $logs->map(function($log) {
                return [
                    'id' => $log->id,
                    'access_time' => $log->access_time->format('d/m/Y H:i:s'),
                    'access_time_raw' => $log->access_time,
                    'person' => $log->person ? [
                        'id' => $log->person->id,
                        'name' => $log->person->name,
                        'lastname' => $log->person->lastname,
                        'full_name' => $log->person->full_name,
                        'document_id' => $log->person->document_id,
                    ] : null,
                    'card' => $log->nfcCard ? [
                        'id' => $log->nfcCard->id,
                        'code' => $log->nfcCard->card_code,
                    ] : null,
                    'company' => $log->company ? $log->company->name : null,
                    'gate' => $log->gate,
                    'status' => $log->status,
                    'reason' => $log->reason,
                    'access_type' => $log->access_type,
                    'verification_method' => $log->verification_method,
                    'ip_address' => $log->ip_address,
                ];
            })
        ]);
    }

    /**
     * Obtener estadísticas de acceso
     * GET /api/access/stats
     */
    public function getStats(Request $request)
    {
        $today = now()->toDateString();
        $weekAgo = now()->subDays(7)->toDateString();
        $monthAgo = now()->subDays(30)->toDateString();

        // Estadísticas básicas
        $stats = [
            'today' => [
                'total' => AccessLog::whereDate('access_time', $today)->count(),
                'granted' => AccessLog::whereDate('access_time', $today)->where('status', 'granted')->count(),
                'denied' => AccessLog::whereDate('access_time', $today)->where('status', 'denied')->count(),
            ],
            'week' => [
                'total' => AccessLog::whereDate('access_time', '>=', $weekAgo)->count(),
                'granted' => AccessLog::whereDate('access_time', '>=', $weekAgo)->where('status', 'granted')->count(),
                'denied' => AccessLog::whereDate('access_time', '>=', $weekAgo)->where('status', 'denied')->count(),
            ],
            'month' => [
                'total' => AccessLog::whereDate('access_time', '>=', $monthAgo)->count(),
                'granted' => AccessLog::whereDate('access_time', '>=', $monthAgo)->where('status', 'granted')->count(),
                'denied' => AccessLog::whereDate('access_time', '>=', $monthAgo)->where('status', 'denied')->count(),
            ],
            'cards' => [
                'total' => NfcCard::count(),
                'active' => NfcCard::where('status', 'active')->count(),
                'assigned' => NfcCard::whereNotNull('assigned_to')->count(),
                'available' => NfcCard::whereNull('assigned_to')->where('status', 'active')->count(),
            ],
            'persons' => [
                'total' => Person::count(),
                'active' => Person::where('is_active', true)->count(),
                'employees' => Person::where('category', 'employee')->count(),
                'students' => Person::where('subcategory', 'student')->count(),
                'teachers' => Person::where('subcategory', 'teacher')->count(),
            ]
        ];

        // Accesos por hora (últimas 24h)
        $hourlyAccess = [];
        for ($i = 0; $i < 24; $i++) {
            $startHour = sprintf('%02d:00:00', $i);
            $endHour = sprintf('%02d:00:00', $i + 1);
            $hourlyAccess[$i] = AccessLog::whereTime('access_time', '>=', $startHour)
                ->whereTime('access_time', '<', $endHour)
                ->whereDate('access_time', $today)
                ->count();
        }
        $stats['hourly_access'] = $hourlyAccess;

        // Accesos por día de la semana (última semana)
        $dailyAccess = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $dailyAccess[$day] = AccessLog::whereRaw('LOWER(DAYNAME(access_time)) = ?', [$day])
                ->whereDate('access_time', '>=', $weekAgo)
                ->count();
        }
        $stats['daily_access'] = $dailyAccess;

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'generated_at' => now()->toDateTimeString()
        ]);
    }

    /**
     * Leer tarjeta desde lector USB (para pruebas)
     * POST /api/access/reader/read
     */
    public function readFromReader(Request $request)
    {
        $request->validate([
            'com_port' => 'nullable|string',
            'gate' => 'nullable|string',
            'access_type' => 'nullable|in:entry,exit'
        ]);

        $comPort = $request->get('com_port', 'COM3');
        $gate = $request->get('gate', 'Puerta Principal');
        $accessType = $request->get('access_type', 'entry');

        // Simular lectura de tarjeta (en producción, leer del puerto serie real)
        // Aquí iría la implementación real con php_serial o similar
        $cardCode = $this->simulateCardRead();

        if ($cardCode) {
            // Crear un nuevo request para validar el acceso
            $validateRequest = new Request([
                'card_code' => $cardCode,
                'gate' => $gate,
                'access_type' => $accessType,
                'reader_name' => 'USB Reader',
                'reader_ip' => $request->ip()
            ]);

            return $this->validateAccess($validateRequest);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo leer la tarjeta. Asegúrese de que el lector está conectado.'
        ], 400);
    }

    /**
     * Simular lectura de tarjeta (para pruebas)
     */
    private function simulateCardRead()
    {
        // En producción, leer del puerto serie real
        // Por ahora, simulamos una tarjeta de prueba
        return 'NFC-TEST-001';
    }

    /**
     * Obtener último acceso de una persona
     * GET /api/access/person/{id}/last
     */
    public function getLastAccess($personId)
    {
        $person = Person::findOrFail($personId);
        
        $lastAccess = AccessLog::where('person_id', $personId)
            ->orderBy('access_time', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'person' => [
                'id' => $person->id,
                'name' => $person->name,
                'lastname' => $person->lastname,
                'full_name' => $person->full_name,
            ],
            'last_access' => $lastAccess ? [
                'access_time' => $lastAccess->access_time->format('d/m/Y H:i:s'),
                'gate' => $lastAccess->gate,
                'status' => $lastAccess->status,
                'access_type' => $lastAccess->access_type,
            ] : null
        ]);
    }
}