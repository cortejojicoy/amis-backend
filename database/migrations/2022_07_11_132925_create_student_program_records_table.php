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
        Schema::create('student_program_records', function (Blueprint $table) {
            $table->bigIncrements('student_program_record_id');
            $table->string('campus_id', 10);
            $table->string('academic_program_id', 10);
            $table->string('acad_group', 10);
            $table->string('acad_org', 10);
            $table->integer('curriculum_id');
            $table->string('status', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_program_records');
    }
};
