<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentGradeTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/student-grade-data-part1.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Student Grade Part 1 data seeded!');

        $path2 = base_path('database/seeders/sql/student-grade-data-part2.sql');
        DB::unprepared(file_get_contents($path2));
        $this->command->info('Student Grade Part 2 data seeded!');
    }
}
