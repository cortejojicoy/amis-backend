<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/user-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('User data seeded!');
    }
}
