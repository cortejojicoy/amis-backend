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
        Schema::create('student_course_catalogs', function (Blueprint $table) {
            $table->integer('course_id');
            $table->date('eff_date');
            $table->string('status', 1);
            $table->string('description', 255);
            $table->string('consent', 1);
            $table->string('allow_mult', 1);
            $table->integer('min_units');
            $table->integer('max_units');
            $table->string('repeatable', 1);
            $table->integer('allow_unit');
            $table->integer('allow_comp_org');
            $table->string('grading', 5);
            $table->string('long_title', 255);
            $table->string('last_crse', 1);
            $table->integer('crs_cntct');
            $table->integer('crse_count');
            $table->string('instr_edit', 1);
            $table->string('fees_exist', 1);
            $table->string('component', 5);
            $table->integer('enrl_unt_calc');
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
        Schema::dropIfExists('student_course_catalogs');
    }
};
