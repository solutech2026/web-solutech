<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run()
    {
        DB::table('companies')->insert([
            [
                'name' => 'SoluTech',
                'slug' => 'solutech',
                'type' => 'office',
                'address' => 'Caracas, Venezuela',
                'phone' => '+58 412 471 4588',
                'email' => 'info@solutech.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Parque Ávila',
                'slug' => 'parque-avila',
                'type' => 'park',
                'address' => 'Cerro El Ávila, Caracas',
                'phone' => '+58 212 555 1234',
                'email' => 'avila@parques.gob.ve',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Parque Warairarepano',
                'slug' => 'warairarepano',
                'type' => 'park',
                'address' => 'Caracas, Venezuela',
                'phone' => '+58 212 555 5678',
                'email' => 'waraira@parques.gob.ve',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}