<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('mentors', function (Blueprint $table) {
            $table->integer('mentor_id')->primary();
            $table->integer('faculty_id')->index('mentors_faculty_id');
            $table->integer('student_program_record_id');
            $table->integer('student_sais_id')->index('mentors_student_saisid');
            $table->string('mentor_role', 29);
            $table->string('field_represented', 29);
            $table->string('status', 20);
            $table->date('start_date');
            $table->date('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mentors');
    }
};
