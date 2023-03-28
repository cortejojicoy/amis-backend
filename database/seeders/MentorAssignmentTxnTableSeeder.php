<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentorAssignmentTxnTableSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('database/seeders/sql/mentor-assignment-txn-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('Mentor Assignment TXN data seeded!');
    }
}
