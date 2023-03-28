<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'student', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'faculty', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'reps', 'guard_name' => 'web'],
            ['id' => 4, 'name' => 'admin', 'guard_name' => 'web'],
            ['id' => 5, 'name' => 'super_admin', 'guard_name' => 'web']
         ];
      
         foreach ($roles as $role) {
              Role::updateOrCreate(['id' => $role['id']], $role);
         }

        $this->command->info('Roles Table Seeded!');
    }
}
