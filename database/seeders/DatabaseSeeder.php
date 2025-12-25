<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'cendy@domain.com'],
            [
                'name' => 'Cendy',
                'password' => bcrypt('password'),
            ]
        );

        // Attach administrator role to admin user
        $adminRole = \App\Models\Role::where('slug', 'administrator')->first();
        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);

            // Attach all permissions to administrator role
            $allPermissions = \App\Models\Permission::all();
            foreach ($allPermissions as $permission) {
                if (!$adminRole->permissions()->where('permission_id', $permission->id)->exists()) {
                    $adminRole->permissions()->attach($permission->id);
                }
            }
        }
    }
}
