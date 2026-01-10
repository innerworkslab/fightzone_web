<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionType;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin Admin
        $Admin = Admin::where('username', 'superadmin')->first();

        if (!$Admin) {
            $Admin = Admin::create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => 'password',
            ]);
        }

        // Assign all permission types to super admin
        $allPermissionTypeIds = PermissionType::pluck('id');

        foreach ($allPermissionTypeIds as $typeId) {
            Permission::firstOrCreate([
                'admin_id' => $Admin->id,
                'permission_type_id' => $typeId,
            ]);
        }
    }
}
