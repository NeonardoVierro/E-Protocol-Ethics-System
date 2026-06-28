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
            'activate users',
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
        // Admin Users
        $admin = User::updateOrCreate([
            'email' => 'admin@ethical.com',
        ], [
            'name' => 'Admin Neo',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        $admin2 = User::updateOrCreate([
            'email' => 'admin2@ethical.com',
        ], [
            'name' => 'Admin Rifaldy',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $admin2->assignRole('admin');
        
        // Peneliti Users
        $researcher = User::updateOrCreate([
            'email' => 'peneliti@ethical.com',
        ], [
            'name' => 'Werkudara',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $researcher->assignRole('peneliti');

        $researchers = [
            ['email' => 'peneliti2@ethical.com', 'name' => 'Janoko'],
            ['email' => 'peneliti3@ethical.com', 'name' => 'Sadewo'],
            ['email' => 'peneliti4@ethical.com', 'name' => 'Petruk'],
            ['email' => 'peneliti5@ethical.com', 'name' => 'Bagong'],
            ['email' => 'peneliti6@ethical.com', 'name' => 'Semar'],
        ];

        foreach ($researchers as $data) {
            $user = User::updateOrCreate([
                'email' => $data['email'],
            ], [
                'name' => $data['name'],
                'password' => bcrypt('password'),
                'status' => 'active',
            ]);
            $user->assignRole('peneliti');
        }
        
        // Sekretaris Users
        $secretary = User::updateOrCreate([
            'email' => 'sekretaris@ethical.com',
        ], [
            'name' => 'Nizma Nabila',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $secretary->assignRole('sekretaris');

        $secretaries = [
            ['email' => 'sekretaris2@ethical.com', 'name' => 'Kayla Nabila'],
            ['email' => 'sekretaris3@ethical.com', 'name' => 'Farida Nabila'],
            ['email' => 'sekretaris4@ethical.com', 'name' => 'Putri Nabila'],
        ];

        foreach ($secretaries as $data) {
            $user = User::updateOrCreate([
                'email' => $data['email'],
            ], [
                'name' => $data['name'],
                'password' => bcrypt('password'),
                'status' => 'active',
            ]);
            $user->assignRole('sekretaris');
        }
        
        // Reviewer Users
        $reviewer = User::updateOrCreate([
            'email' => 'reviewer@ethical.com',
        ], [
            'name' => 'Hendra Adelia',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $reviewer->assignRole('reviewer');

        $reviewers = [
            ['email' => 'reviewer2@ethical.com', 'name' => 'Widhi Nur'],
            ['email' => 'reviewer3@ethical.com', 'name' => 'Ardika Putra'],
            ['email' => 'reviewer4@ethical.com', 'name' => 'Rizal Fadillah'],
            ['email' => 'reviewer5@ethical.com', 'name' => 'Rosyid Hanafi'],
            ['email' => 'reviewer6@ethical.com', 'name' => 'Dillo Beshieto'],
        ];

        foreach ($reviewers as $data) {
            $user = User::updateOrCreate([
                'email' => $data['email'],
            ], [
                'name' => $data['name'],
                'password' => bcrypt('password'),
                'status' => 'active',
            ]);
            $user->assignRole('reviewer');
        }
        
        // Ketua Users
        $head = User::updateOrCreate([
            'email' => 'ketua@ethical.com',
        ], [
            'name' => 'Pak Yusfia',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $head->assignRole('ketua');

        $head2 = User::updateOrCreate([
            'email' => 'ketua2@ethical.com',
        ], [
            'name' => 'Pak Cucuk',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $head2->assignRole('ketua');
        
        // User pending (belum diaktivasi)
        $pending = User::updateOrCreate([
            'email' => 'pending@ethical.com',
        ], [
            'name' => 'Pending User',
            'password' => bcrypt('password'),
            'status' => 'pending',
        ]);
        $pending->assignRole('peneliti');
    }
}