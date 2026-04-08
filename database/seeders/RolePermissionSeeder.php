<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'access admin panel',
            'manage users',
            'manage access control',
            'view reports',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $operatorRole = Role::create(['name' => 'operator']);
        $userRole = Role::create(['name' => 'user']);

        // Asignar permisos al Super Admin (todos los permisos)
        $superAdminRole->givePermissionTo(Permission::all());

        // Asignar permisos al Admin
        $adminRole->givePermissionTo([
            'access admin panel',
            'manage users',
            'manage access control',
            'view reports',
        ]);

        // Asignar permisos al Operator
        $operatorRole->givePermissionTo([
            'access admin panel',
            'manage access control',
            'view reports',
        ]);

        // Crear usuario Super Admin
        $superAdmin = User::create([
            'name' => 'Herbert Diaz',
            'email' => 'soporteitsolutech@gmail.com',
            'password' => bcrypt('Tesla2026'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        // Crear usuario Admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@solutech.com',
            'password' => bcrypt('Admin123!'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
    }
}