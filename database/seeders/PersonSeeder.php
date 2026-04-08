<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonSeeder extends Seeder
{
    public function run()
    {
        DB::table('persons')->insert([
            [
                'company_id' => 1,
                'type' => 'employee',
                'name' => 'Ana García',
                'document_id' => 'V-12345678',
                'email' => 'ana@solutech.com',
                'phone' => '+58 412 1234567',
                'position' => 'CEO',
                'department' => 'Administración',
                'bio' => 'Fundadora de SoluTech con más de 15 años de experiencia en sistemas de seguridad.',
                'bio_url' => 'bio/' . Str::random(12),
                'companions' => 0,
                'visit_reason' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'type' => 'employee',
                'name' => 'Carlos Rodríguez',
                'document_id' => 'V-87654321',
                'email' => 'carlos@solutech.com',
                'phone' => '+58 412 2345678',
                'position' => 'CTO',
                'department' => 'Tecnología',
                'bio' => 'Ingeniero en sistemas especializado en IoT y control de acceso.',
                'bio_url' => 'bio/' . Str::random(12),
                'companions' => 0,
                'visit_reason' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2,
                'type' => 'visitor',
                'name' => 'Visitante Ejemplo',
                'document_id' => 'V-99988877',
                'email' => null,
                'phone' => '+58 412 3456789',
                'position' => null,
                'department' => null,
                'bio' => null,
                'bio_url' => 'bio/' . Str::random(12),
                'companions' => 2,
                'visit_reason' => 'recreación',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}