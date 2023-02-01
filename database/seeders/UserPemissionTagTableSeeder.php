<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPemissionTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path('database/seeders/sql/user-permission-tag-data.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('User permission tag data seeded!');
    }
}
