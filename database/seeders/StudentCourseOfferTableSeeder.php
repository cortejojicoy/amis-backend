<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentCourseOfferTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/student-course-offer-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Student Course Offer data seeded!');
    }
}
