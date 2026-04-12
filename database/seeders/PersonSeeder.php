<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PersonSeeder extends Seeder
{
    public function run()
    {
        // Obtener IDs de empresas y colegios
        $solutechId = DB::table('companies')->where('slug', 'solutech')->value('id');
        $tecnoindustrialId = DB::table('companies')->where('slug', 'tecnoindustrial-este')->value('id');
        $sanIgnacioId = DB::table('companies')->where('slug', 'colegio-san-ignacio')->value('id');
        $santaMariaId = DB::table('companies')->where('slug', 'ue-santa-maria')->value('id');
        $angelesChacaoId = DB::table('companies')->where('slug', 'colegio-angeles-chacao')->value('id');
        $liceoSimonId = DB::table('companies')->where('slug', 'liceo-simon-rodriguez')->value('id');

        // ============ EMPLEADOS (Empresas) ============
        
        // Empleados de SoluTech
        DB::table('persons')->insert([
            [
                'category' => 'employee',
                'subcategory' => null,
                'name' => 'Carlos',
                'lastname' => 'Mendoza',
                'document_id' => 'V-12345678',
                'email' => 'carlos.mendoza@solutech.com',
                'phone' => '+58 412 123 4567',
                'gender' => 'male',
                'birth_date' => '1985-03-15',
                'company_id' => $solutechId,
                'position' => 'Director de Tecnología',
                'department' => 'Tecnología',
                'bio' => 'Ingeniero en Computación con más de 15 años de experiencia en desarrollo de software y control de acceso.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'employee',
                'subcategory' => null,
                'name' => 'María',
                'lastname' => 'Fernández',
                'document_id' => 'V-87654321',
                'email' => 'maria.fernandez@solutech.com',
                'phone' => '+58 414 234 5678',
                'gender' => 'female',
                'birth_date' => '1990-07-22',
                'company_id' => $solutechId,
                'position' => 'Gerente de Ventas',
                'department' => 'Ventas',
                'bio' => 'Especialista en ventas B2B con enfoque en soluciones tecnológicas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'employee',
                'subcategory' => null,
                'name' => 'José',
                'lastname' => 'Rodríguez',
                'document_id' => 'V-11223344',
                'email' => 'jose.rodriguez@solutech.com',
                'phone' => '+58 416 345 6789',
                'gender' => 'male',
                'birth_date' => '1988-11-05',
                'company_id' => $solutechId,
                'position' => 'Desarrollador Senior',
                'department' => 'Tecnología',
                'bio' => 'Desarrollador full-stack especializado en Laravel y Vue.js.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Empleados de TecnoIndustrial
        DB::table('persons')->insert([
            [
                'category' => 'employee',
                'subcategory' => null,
                'name' => 'Ana',
                'lastname' => 'González',
                'document_id' => 'V-99887766',
                'email' => 'ana.gonzalez@tecnoindustrial.com',
                'phone' => '+58 424 456 7890',
                'gender' => 'female',
                'birth_date' => '1992-09-18',
                'company_id' => $tecnoindustrialId,
                'position' => 'Coordinadora de Producción',
                'department' => 'Producción',
                'bio' => 'Ingeniera Industrial con experiencia en optimización de procesos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'employee',
                'subcategory' => null,
                'name' => 'Luis',
                'lastname' => 'Pérez',
                'document_id' => 'V-55443322',
                'email' => 'luis.perez@tecnoindustrial.com',
                'phone' => '+58 412 567 8901',
                'gender' => 'male',
                'birth_date' => '1983-04-25',
                'company_id' => $tecnoindustrialId,
                'position' => 'Supervisor de Planta',
                'department' => 'Operaciones',
                'bio' => 'Técnico superior en mantenimiento industrial.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============ ESTUDIANTES (Colegios) ============
        
        // Estudiantes - Colegio San Ignacio
        DB::table('persons')->insert([
            [
                'category' => 'school',
                'subcategory' => 'student',
                'name' => 'Sofía',
                'lastname' => 'Martínez',
                'document_id' => 'E-12345',
                'email' => 'sofia.martinez@sanignacio.edu.ve',
                'phone' => '+58 412 111 1111',
                'gender' => 'female',
                'birth_date' => '2012-05-20',
                'company_id' => $sanIgnacioId,
                'grade_level' => '7th',
                'academic_year' => '2024-2025',
                'period' => 'first',
                'emergency_contact_name' => 'María Martínez',
                'emergency_phone' => '+58 414 222 2222',
                'allergies' => 'Ninguna',
                'medical_conditions' => 'Ninguna',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'school',
                'subcategory' => 'student',
                'name' => 'Mateo',
                'lastname' => 'López',
                'document_id' => 'E-12346',
                'email' => 'mateo.lopez@sanignacio.edu.ve',
                'phone' => '+58 416 222 2222',
                'gender' => 'male',
                'birth_date' => '2011-08-15',
                'company_id' => $sanIgnacioId,
                'grade_level' => '8th',
                'academic_year' => '2024-2025',
                'period' => 'first',
                'emergency_contact_name' => 'Carlos López',
                'emergency_phone' => '+58 424 333 3333',
                'allergies' => 'Penicilina',
                'medical_conditions' => 'Asma leve',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'school',
                'subcategory' => 'student',
                'name' => 'Valentina',
                'lastname' => 'Sánchez',
                'document_id' => 'E-12347',
                'email' => 'valentina.sanchez@sanignacio.edu.ve',
                'phone' => '+58 412 333 3333',
                'gender' => 'female',
                'birth_date' => '2013-03-10',
                'company_id' => $sanIgnacioId,
                'grade_level' => '6th',
                'academic_year' => '2024-2025',
                'period' => 'second',
                'emergency_contact_name' => 'Laura Sánchez',
                'emergency_phone' => '+58 414 444 4444',
                'allergies' => 'Mariscos',
                'medical_conditions' => 'Ninguna',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Estudiantes - U.E. Santa María
        DB::table('persons')->insert([
            [
                'category' => 'school',
                'subcategory' => 'student',
                'name' => 'Diego',
                'lastname' => 'Torres',
                'document_id' => 'E-23456',
                'email' => 'diego.torres@santamaria.edu.ve',
                'phone' => '+58 424 444 4444',
                'gender' => 'male',
                'birth_date' => '2010-11-30',
                'company_id' => $santaMariaId,
                'grade_level' => '9th',
                'academic_year' => '2024-2025',
                'period' => 'first',
                'emergency_contact_name' => 'Pedro Torres',
                'emergency_phone' => '+58 416 555 5555',
                'allergies' => 'Ninguna',
                'medical_conditions' => 'Ninguna',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'school',
                'subcategory' => 'student',
                'name' => 'Camila',
                'lastname' => 'Rojas',
                'document_id' => 'E-23457',
                'email' => 'camila.rojas@santamaria.edu.ve',
                'phone' => '+58 412 555 5555',
                'gender' => 'female',
                'birth_date' => '2012-01-25',
                'company_id' => $santaMariaId,
                'grade_level' => '7th',
                'academic_year' => '2024-2025',
                'period' => 'second',
                'emergency_contact_name' => 'Ana Rojas',
                'emergency_phone' => '+58 424 666 6666',
                'allergies' => 'Polen',
                'medical_conditions' => 'Rinitis alérgica',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============ DOCENTES (Colegios) ============
        
        // Docentes - Colegio Los Ángeles
        DB::table('persons')->insert([
            [
                'category' => 'school',
                'subcategory' => 'teacher',
                'name' => 'Prof. Carmen',
                'lastname' => 'Vargas',
                'document_id' => 'V-33445566',
                'email' => 'carmen.vargas@angeleschacao.edu.ve',
                'phone' => '+58 412 666 6666',
                'gender' => 'female',
                'birth_date' => '1975-06-12',
                'company_id' => $angelesChacaoId,
                'position' => 'Coordinadora de Matemáticas',
                'department' => 'Académico',
                'teacher_type' => 'regular',
                'emergency_contact_name' => 'Roberto Vargas',
                'emergency_phone' => '+58 414 777 7777',
                'bio' => 'Licenciada en Educación mención Matemáticas, con 20 años de experiencia.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'school',
                'subcategory' => 'teacher',
                'name' => 'Prof. Ricardo',
                'lastname' => 'Díaz',
                'document_id' => 'V-44556677',
                'email' => 'ricardo.diaz@angeleschacao.edu.ve',
                'phone' => '+58 416 777 7777',
                'gender' => 'male',
                'birth_date' => '1980-09-08',
                'company_id' => $angelesChacaoId,
                'position' => 'Docente de Ciencias',
                'department' => 'Académico',
                'teacher_type' => 'regular',
                'emergency_contact_name' => 'Lucía Díaz',
                'emergency_phone' => '+58 424 888 8888',
                'bio' => 'Profesor de Biología y Química, especialista en educación ambiental.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Docentes - Liceo Simón Rodríguez
        DB::table('persons')->insert([
            [
                'category' => 'school',
                'subcategory' => 'teacher',
                'name' => 'Prof. Elena',
                'lastname' => 'Morales',
                'document_id' => 'V-55667788',
                'email' => 'elena.morales@liceosimonrodriguez.edu.ve',
                'phone' => '+58 412 888 8888',
                'gender' => 'female',
                'birth_date' => '1978-03-25',
                'company_id' => $liceoSimonId,
                'position' => 'Docente de Castellano',
                'department' => 'Académico',
                'teacher_type' => 'regular',
                'emergency_contact_name' => 'Jorge Morales',
                'emergency_phone' => '+58 414 999 9999',
                'bio' => 'Licenciada en Letras, con maestría en Literatura Infantil.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============ PERSONAL ADMINISTRATIVO (Colegios) ============
        
        // Administrativo - Colegio San Ignacio
        DB::table('persons')->insert([
            [
                'category' => 'school',
                'subcategory' => 'administrative',
                'name' => 'Lic. Martha',
                'lastname' => 'Gómez',
                'document_id' => 'V-66778899',
                'email' => 'martha.gomez@sanignacio.edu.ve',
                'phone' => '+58 424 999 9999',
                'gender' => 'female',
                'birth_date' => '1970-12-01',
                'company_id' => $sanIgnacioId,
                'position' => 'Directora Administrativa',
                'department' => 'Administración',
                'emergency_contact_name' => 'Luis Gómez',
                'emergency_phone' => '+58 416 111 2222',
                'bio' => 'Contadora pública con especialización en gestión educativa.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Administrativo - U.E. Santa María
        DB::table('persons')->insert([
            [
                'category' => 'school',
                'subcategory' => 'administrative',
                'name' => 'Sr. Andrés',
                'lastname' => 'Castillo',
                'document_id' => 'V-77889900',
                'email' => 'andres.castillo@santamaria.edu.ve',
                'phone' => '+58 416 222 3333',
                'gender' => 'male',
                'birth_date' => '1968-07-19',
                'company_id' => $santaMariaId,
                'position' => 'Coordinador de Mantenimiento',
                'department' => 'Servicios Generales',
                'emergency_contact_name' => 'Rosa Castillo',
                'emergency_phone' => '+58 424 333 4444',
                'bio' => 'Ingeniero mecánico, encargado del mantenimiento de las instalaciones.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}