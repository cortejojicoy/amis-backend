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
        Schema::create('curriculum_standings', function (Blueprint $table) {
            $table->integer('curriculum_id');
            $table->string('classification', 20);
            $table->integer('units_attained');
            $table->primary(['curriculum_id', 'classification'], 'curriculum_standings_pkey');
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
        Schema::dropIfExists('curriculum_standings');
    }
};
