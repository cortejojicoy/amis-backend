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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->integer('student_sais_id');
            $table->integer('campus_id');
            $table->integer('term');
            $table->string('section', 11);
            $table->integer('unit_taken');
            $table->integer('course_id');
            $table->string('grade', 5);
            $table->string('college', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_grades');
    }
};
