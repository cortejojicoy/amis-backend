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
            $table->integer('mentor_id')->autoIncrement();
            $table->integer('faculty_id')->index('mentors_faculty_id');
            $table->integer('student_program_record_id');
            $table->string('uuid', 36)->index('mentors_student_uuid');
            $table->integer('mentor_role');
            $table->string('field_represented', 29)->nullable();
            $table->string('status', 20)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
