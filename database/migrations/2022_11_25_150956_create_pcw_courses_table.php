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
        Schema::create('pcw_courses', function (Blueprint $table) {
            $table->id();
            $table->string('pcw_id');
            $table->integer('course_id');
            $table->string('course_type');
            $table->integer('units');
            $table->integer('term_id');
            $table->string('version');
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
        Schema::dropIfExists('pcw_courses');
    }
};
