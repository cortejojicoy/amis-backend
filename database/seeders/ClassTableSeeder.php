<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/class-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Class data seeded!');
    }
}
