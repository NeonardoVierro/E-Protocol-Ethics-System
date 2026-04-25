<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (kelompokkan berdasarkan modul)
        $permissions = [
            // User Management
            'view users', 'create users', 'edit users', 'delete users', 'activate users',
            
            // Proposal Management
            'view proposals', 'create proposals', 'edit proposals', 'delete proposals', 'submit proposals',
            'verify proposals', 'assign reviewer', 'assign secretary',
            
            // Review Management
            'view reviews', 'create reviews', 'submit reviews', 'view feedback',
            
            // Document Management
            'manage templates', 'upload documents', 'download documents', 'generate ethics',
            'sign documents', 'publish documents',
            
            // Dashboard Access
            'access researcher dashboard',
            'access secretary dashboard',
            'access reviewer dashboard', 
            'access admin dashboard',
            'access head dashboard',
            
            // Monitoring
            'view monitoring',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // 1. Admin role (Super admin, bisa semua)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
        
        // 2. Peneliti role
        $researcherRole = Role::firstOrCreate(['name' => 'peneliti']);
        $researcherPermissions = [
            'access researcher dashboard',
            'view proposals', 'create proposals', 'edit proposals', 'submit proposals',
            'upload documents', 'download documents', 'view feedback',
        ];
        $researcherRole->givePermissionTo($researcherPermissions);
        
        // 3. Sekretaris role
        $secretaryRole = Role::firstOrCreate(['name' => 'sekretaris']);
        $secretaryPermissions = [
            'access secretary dashboard',
            'view proposals', 'verify proposals', 'assign reviewer',
            'view reviews', 'download documents', 'view monitoring',
        ];
        $secretaryRole->givePermissionTo($secretaryPermissions);
        
        // 4. Reviewer role
        $reviewerRole = Role::firstOrCreate(['name' => 'reviewer']);
        $reviewerPermissions = [
            'access reviewer dashboard',
            'view proposals', 'view reviews', 'create reviews', 'submit reviews',
            'download documents',
        ];
        $reviewerRole->givePermissionTo($reviewerPermissions);
        
        // 5. Ketua role
        $headRole = Role::firstOrCreate(['name' => 'ketua']);
        $headPermissions = [
            'access head dashboard',
            'view proposals', 'sign documents', 'download documents',
        ];
        $headRole->givePermissionTo($headPermissions);
        
        // Create test users
        // Admin User
        $admin = User::firstOrCreate([
            'email' => 'admin@ethical.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $admin->assignRole('admin');
        
        // Peneliti User
        $researcher = User::firstOrCreate([
            'email' => 'peneliti@ethical.com',
        ], [
            'name' => 'Peneliti Test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $researcher->assignRole('peneliti');
        
        // Sekretaris User
        $secretary = User::firstOrCreate([
            'email' => 'sekretaris@ethical.com',
        ], [
            'name' => 'Sekretaris Test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $secretary->assignRole('sekretaris');
        
        // Reviewer User
        $reviewer = User::firstOrCreate([
            'email' => 'reviewer@ethical.com',
        ], [
            'name' => 'Reviewer Test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $reviewer->assignRole('reviewer');
        
        // Ketua User
        $head = User::firstOrCreate([
            'email' => 'ketua@ethical.com',
        ], [
            'name' => 'Ketua Test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $head->assignRole('ketua');
        
        // User pending (belum diaktivasi)
        $pending = User::firstOrCreate([
            'email' => 'pending@ethical.com',
        ], [
            'name' => 'Pending User',
            'password' => bcrypt('password'),
            'status' => 'pending',
        ]);
        $pending->assignRole('peneliti');
    }
}