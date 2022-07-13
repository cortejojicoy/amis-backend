<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentCourseCatalogTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/student-course-catalog-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Student Course Catalog data seeded!');
    }
}
