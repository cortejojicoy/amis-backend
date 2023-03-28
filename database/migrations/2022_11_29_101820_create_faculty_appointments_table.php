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
        Schema::create('faculty_appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('faculty_id');
            $table->string('homeunit', 20);
            $table->string('status', 20);
            $table->integer('is_homeunit')->nullable();
            $table->string('unit', 10)->nullable();
            $table->string('description', 255)->nullable();
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
        Schema::dropIfExists('faculty_appointments');
    }
};
