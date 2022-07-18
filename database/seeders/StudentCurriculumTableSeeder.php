<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentCurriculumTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/student-curriculum-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Student Curriculum data seeded!');
    }
}
