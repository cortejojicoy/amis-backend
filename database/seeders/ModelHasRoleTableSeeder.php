<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasRoleTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/model-has-role-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Model-has-Role data seeded!');
    }
}
