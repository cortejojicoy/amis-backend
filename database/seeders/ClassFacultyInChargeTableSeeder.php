<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassFacultyInChargeTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/class-faculty-in-charge-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Class Faculty-in-Charge data seeded!');
    }
}
