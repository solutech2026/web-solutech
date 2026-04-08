<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NFCCard;
use App\Models\AccessLog;
use App\Models\Person;
use Carbon\Carbon;

class AccessController extends Controller
{
    /**
     * Validar acceso con tarjeta NFC
     */
    public function validateAccess(Request $request)
    {
        $request->validate([
            'card_code' => 'required|string',
            'gate' => 'nullable|string',
            'access_type' => 'nullable|in:entry,exit'
        ]);

        $cardCode = strtoupper($request->card_code);
        $gate = $request->gate ?? 'Puerta Principal';
        $accessType = $request->access_type ?? 'entry';

        // Buscar tarjeta
        $card = NFCCard::where('card_code', $cardCode)
            ->where('status', 'active')
            ->first();

        // Tarjeta no válida
        if (!$card) {
            return $this->logAndRespond(null, 'denied', 'Tarjeta no válida o inactiva', $gate, $accessType, $request);
        }

        // Verificar persona asociada
        if (!$card->person) {
            return $this->logAndRespond($card, 'denied', 'Tarjeta no asociada a ninguna persona', $gate, $accessType, $request);
        }

        $person = $card->person;

        // Verificar si la persona está activa
        if (!$person->is_active) {
            return $this->logAndRespond($card, 'denied', 'Persona inactiva en el sistema', $gate, $accessType, $request);
        }

        // Verificar horario de acceso (opcional - para empleados)
        if ($person->type === 'employee') {
            $hour = now()->format('H:i');
            $isWeekend = now()->isWeekend();

            // Ejemplo: solo acceso en horario laboral (8am - 6pm)
            if ($hour < '08:00' || $hour > '18:00') {
                return $this->logAndRespond($card, 'denied', 'Fuera de horario laboral', $gate, $accessType, $request);
            }
        }

        // Verificar si ya está dentro (para control de entrada/salida)
        if ($accessType === 'entry') {
            $lastEntry = AccessLog::where('person_id', $person->id)
                ->where('access_type', 'entry')
                ->whereDate('access_time', now()->toDateString())
                ->latest('access_time')
                ->first();

            // Opcional: evitar entradas duplicadas en corto tiempo
            if ($lastEntry && $lastEntry->access_time->diffInMinutes(now()) < 1) {
                return $this->logAndRespond($card, 'denied', 'Intento de acceso muy frecuente', $gate, $accessType, $request);
            }
        }

        // ACCESO PERMITIDO
        $access = $this->logAndRespond($card, 'granted', 'Acceso permitido', $gate, $accessType, $request);

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
                'type' => $person->type,
                'document_id' => $person->document_id,
                'company' => $person->company ? $person->company->name : null,
                'bio_url' => $person->bio_url ? url($person->bio_url) : null,
            ],
            'card' => [
                'code' => $card->card_code,
                'notes' => $card->notes
            ]
        ]);
    }

    /**
     * Registrar acceso y retornar respuesta
     */
    private function logAndRespond($card, $status, $reason, $gate, $accessType, $request)
    {
        return AccessLog::create([
            'company_id' => $card?->person?->company_id, // Usando nullsafe operator de PHP 8+
            'person_id' => $card?->person?->id,
            'nfc_card_id' => $card?->id,
            'access_type' => $accessType,
            'verification_method' => 'nfc',
            'access_time' => now(),
            'status' => $status,
            'gate' => $gate,
            'ip_address' => $request->ip(),
            'reason' => $reason,
            'metadata' => [
                'user_agent' => $request->userAgent(),
                'card_code' => $request->card_code,
                'version' => '1.0' // Para rastrear qué versión de tu app NFC hizo la carga
            ]
        ]);
    }

    /**
     * Obtener logs de acceso (para API)
     */
    public function getLogs(Request $request)
    {
        $limit = $request->get('limit', 50);
        $companyId = $request->get('company_id');
        $status = $request->get('status');
        $from = $request->get('from');
        $to = $request->get('to');

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

        $logs = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'total' => $logs->count(),
            'logs' => $logs
        ]);
    }

    /**
     * Obtener estadísticas de acceso
     */
    public function getStats(Request $request)
    {
        $today = now()->toDateString();
        $weekAgo = now()->subDays(7)->toDateString();

        $stats = [
            'total_today' => AccessLog::whereDate('access_time', $today)->count(),
            'granted_today' => AccessLog::whereDate('access_time', $today)->where('status', 'granted')->count(),
            'denied_today' => AccessLog::whereDate('access_time', $today)->where('status', 'denied')->count(),
            'total_week' => AccessLog::whereDate('access_time', '>=', $weekAgo)->count(),
            'active_cards' => NFCCard::where('status', 'active')->count(),
            'active_persons' => Person::where('is_active', true)->count(),
        ];

        // Accesos por hora (últimas 24h)
        $hourlyAccess = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyAccess[$i] = AccessLog::whereTime('access_time', '>=', sprintf('%02d:00:00', $i))
                ->whereTime('access_time', '<', sprintf('%02d:00:00', $i + 1))
                ->whereDate('access_time', $today)
                ->count();
        }

        $stats['hourly_access'] = $hourlyAccess;

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
