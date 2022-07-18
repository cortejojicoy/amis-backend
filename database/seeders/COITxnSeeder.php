<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class COITxnSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/coitxn-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('COITXN data seeded!');
    }
}
