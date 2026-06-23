<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@iwlms.test',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@iwlms.test',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole($studentRole);
    }
}
