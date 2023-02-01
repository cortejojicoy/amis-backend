<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminTableSeeder::class,
            ClassFacultyInChargeTableSeeder::class,
            ClassTableSeeder::class,
            // CourseOfferingTableSeeder::class,
            COISeeder::class,
            COITxnSeeder::class,
            // CourseTableSeeder::class,
            FacultyTableSeeder::class,
            // MentorAssignmentTxnTableSeeder::class,
            MentorTableSeeder::class,
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
            ModelHasPermissionTableSeeder::class,
            ModelHasRoleTableSeeder::class,
            // SavedMentorTableSeeder::class,
            StudentCourseCatalogTableSeeder::class,
            StudentCourseOfferTableSeeder::class,
            StudentCurriculumTableSeeder::class,
            // StudentGradeTableSeeder::class,
            StudentProgramRecordTableSeeder::class,
            StudentTableSeeder::class,
            // StudentTermTableSeeder::class,
            UserTableSeeder::class,

            FacultyAppointmentSeeder::class,
            MentorRoleSeeder::class,
            TagSeeder::class,
            UserPemissionTableSeeder::class,
            ModelTagSeeder::class
        ]);
    }
}