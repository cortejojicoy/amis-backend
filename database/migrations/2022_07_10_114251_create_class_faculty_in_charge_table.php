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
        Schema::create('class_faculty_in_charge', function (Blueprint $table) {
            $table->bigIncrements('cfic_id');
            $table->integer('class_id');
            $table->integer('faculty_id');
            $table->string('mode', 50);
            $table->string('primary', 2);
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
        Schema::dropIfExists('class_faculty_in_charge');
    }
};
