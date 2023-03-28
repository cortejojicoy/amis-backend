<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentProgramRecordTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/student-program-record-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Student Program Record data seeded!');
    }
}
