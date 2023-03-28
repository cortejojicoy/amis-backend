<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/mentor-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Mentor data seeded!');
    }
}
