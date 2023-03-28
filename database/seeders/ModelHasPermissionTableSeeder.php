<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasPermissionTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/model-has-permission-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Model-has-Permission data seeded!');
    }
}
