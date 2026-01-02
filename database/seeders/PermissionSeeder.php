<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Proposal Permissions
            ['name' => 'View Proposals', 'slug' => 'view-proposals', 'description' => 'Can view proposals', 'group' => 'Proposals'],
            ['name' => 'Create Proposals', 'slug' => 'create-proposals', 'description' => 'Can create proposals', 'group' => 'Proposals'],
            ['name' => 'Edit Proposals', 'slug' => 'edit-proposals', 'description' => 'Can edit proposals', 'group' => 'Proposals'],
            ['name' => 'Delete Proposals', 'slug' => 'delete-proposals', 'description' => 'Can delete proposals', 'group' => 'Proposals'],

            // Invoice Permissions
            ['name' => 'View Invoices', 'slug' => 'view-invoices', 'description' => 'Can view invoices', 'group' => 'Invoices'],
            ['name' => 'Create Invoices', 'slug' => 'create-invoices', 'description' => 'Can create invoices', 'group' => 'Invoices'],
            ['name' => 'Edit Invoices', 'slug' => 'edit-invoices', 'description' => 'Can edit invoices', 'group' => 'Invoices'],
            ['name' => 'Delete Invoices', 'slug' => 'delete-invoices', 'description' => 'Can delete invoices', 'group' => 'Invoices'],

            // Client Permissions
            ['name' => 'View Clients', 'slug' => 'view-clients', 'description' => 'Can view clients', 'group' => 'Clients'],
            ['name' => 'Create Clients', 'slug' => 'create-clients', 'description' => 'Can create clients', 'group' => 'Clients'],
            ['name' => 'Edit Clients', 'slug' => 'edit-clients', 'description' => 'Can edit clients', 'group' => 'Clients'],
            ['name' => 'Delete Clients', 'slug' => 'delete-clients', 'description' => 'Can delete clients', 'group' => 'Clients'],

            // User Management Permissions
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view users', 'group' => 'Users'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'description' => 'Can create users', 'group' => 'Users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'description' => 'Can edit users', 'group' => 'Users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users', 'group' => 'Users'],

            // Role Management Permissions
            ['name' => 'View Roles', 'slug' => 'view-roles', 'description' => 'Can view roles', 'group' => 'Roles'],
            ['name' => 'Create Roles', 'slug' => 'create-roles', 'description' => 'Can create roles', 'group' => 'Roles'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles', 'description' => 'Can edit roles', 'group' => 'Roles'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles', 'description' => 'Can delete roles', 'group' => 'Roles'],

            // Permission Management Permissions
            ['name' => 'View Permissions', 'slug' => 'view-permissions', 'description' => 'Can view permissions', 'group' => 'Permissions'],
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'description' => 'Can assign permissions to roles', 'group' => 'Permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
