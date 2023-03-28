<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseOfferingTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/course-offering-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Course Offering data seeded!');
    }
}
