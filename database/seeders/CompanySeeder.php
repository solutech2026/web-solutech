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
            // ============ EMPRESAS (2) ============
            [
                'name' => 'SoluTech C.A.',
                'slug' => 'solutech',
                'type' => 'company',
                'address' => 'Av. Francisco de Miranda, Centro Empresarial Torre Europa, Piso 8, Caracas, Venezuela',
                'phone' => '+58 212 555 0101',
                'email' => 'info@solutech.com',
                'website' => 'https://solutech.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TecnoIndustrial del Este',
                'slug' => 'tecnoindustrial-este',
                'type' => 'company',
                'address' => 'Zona Industrial de Guarenas, Calle 5, Parcela 12, Guarenas, Miranda',
                'phone' => '+58 234 555 0202',
                'email' => 'contacto@tecnoindustrial.com',
                'website' => 'https://tecnoindustrial.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============ COLEGIOS (4) ============
            [
                'name' => 'Colegio San Ignacio de Loyola',
                'slug' => 'colegio-san-ignacio',
                'type' => 'school',
                'address' => 'Av. Los Chaguaramos, Urbanización Los Chaguaramos, Caracas, Venezuela',
                'phone' => '+58 212 555 0303',
                'email' => 'info@sanignacio.edu.ve',
                'website' => 'https://sanignacio.edu.ve',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unidad Educativa Santa María',
                'slug' => 'ue-santa-maria',
                'type' => 'school',
                'address' => 'Av. Principal de Santa María, Edif. Santa María, Caracas, Venezuela',
                'phone' => '+58 212 555 0404',
                'email' => 'secretaria@santamaria.edu.ve',
                'website' => 'https://santamaria.edu.ve',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Colegio Los Ángeles de Chacao',
                'slug' => 'colegio-angeles-chacao',
                'type' => 'school',
                'address' => 'Av. Principal de Chacao, Cruce con Calle Mohedano, Chacao, Caracas',
                'phone' => '+58 212 555 0505',
                'email' => 'direccion@angeleschacao.edu.ve',
                'website' => 'https://angeleschacao.edu.ve',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Liceo Bolivariano Simón Rodríguez',
                'slug' => 'liceo-simon-rodriguez',
                'type' => 'school',
                'address' => 'Av. Principal de El Valle, Parroquia El Valle, Caracas, Venezuela',
                'phone' => '+58 212 555 0606',
                'email' => 'liceosimonrodriguez@gmail.com',
                'website' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}