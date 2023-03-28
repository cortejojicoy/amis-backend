<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultyTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/faculty-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Faculty data seeded!');
    }
}