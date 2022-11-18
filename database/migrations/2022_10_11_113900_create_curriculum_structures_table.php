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
        Schema::create('curriculum_structures', function (Blueprint $table) {
            $table->integer('curriculum_id');
            $table->string('year', 1);
            $table->string('sem', 1);
            $table->integer('major_units');
            $table->integer('ge_elective_units');
            $table->integer('required_units');
            $table->integer('elective_units');
            $table->integer('cognate_units');
            $table->integer('specialized_units');
            $table->integer('track_units');
            $table->integer('total_units');
            $table->integer('major_count');
            $table->integer('ge_elective_count');
            $table->integer('required_count');
            $table->integer('elective_count');
            $table->integer('cognate_count');
            $table->integer('specialized_count');
            $table->integer('track_count');
            $table->integer('total_count');
            $table->primary(['curriculum_id', 'year', 'sem'], 'curriculum_structures_pkey');
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
        Schema::dropIfExists('curriculum_structures');
    }
};
