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
        Schema::create('mentor_status', function (Blueprint $table) {
            $table->id();
            $table->integer('student_sais_id');
            $table->integer('mentor_id');
            $table->string('mentor_name', 50);
            $table->string('mentor_role', 50);
            $table->string('status', 20);
            $table->string('actions', 6);
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
        Schema::dropIfExists('mentor_status');
    }
};
