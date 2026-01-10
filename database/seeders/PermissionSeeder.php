<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use App\Models\PermissionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Admin Management' => [
                'admin_view' => 'View Admins',
                'admin_create' => 'Create Admin',
                'admin_update' => 'Update Admin',
                'admin_delete' => 'Delete Admin',
            ],
        ];

        foreach ($data as $groupLabel => $permissions) {
            $group = PermissionGroup::firstOrCreate([
                'name' => Str::slug($groupLabel, '_'),
            ], [
                'label' => $groupLabel,
            ]);

            foreach ($permissions as $name => $label) {
                PermissionType::firstOrCreate([
                    'name' => $name,
                ], [
                    'label' => $label,
                    'permission_group_id' => $group->id,
                ]);
            }
        }
    }
}
