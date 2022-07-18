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
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('admin_id');
            $table->integer('uuid');
            $table->integer('saisid');
            $table->string('unit', 10);
            $table->string('college', 10);
            $table->integer('university');
            $table->integer('graduate');
            $table->integer('undergrad');
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
        Schema::dropIfExists('admins');
    }
};
